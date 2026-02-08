/**
 * SIPAccountManager - Multi-account SIP.js manager for browser softphone
 *
 * Manages up to 6 SIP accounts simultaneously using SIP.js UserAgent instances.
 * Each account corresponds to a PBX extension and maintains its own WebSocket
 * connection, registration, and call sessions.
 *
 * Posts call analytics to internal Laravel API endpoints for tracking.
 *
 * Dependencies:
 *   - SIP.js v0.21.x (loaded via CDN or bundled)
 *
 * Usage:
 *   const manager = new SIPAccountManager({ userId: 1 });
 *   manager.onCallStateChange = (callUUID, state, data) => { ... };
 *   manager.onRegistrationChange = (extensionId, registered) => { ... };
 *   await manager.register({ extensionId: 1, server: 'wss://pbx:8089/ws', ... });
 *   await manager.makeCall(1, 'sip:1001@pbx.example.com');
 */
class SIPAccountManager {
    /**
     * @param {Object} options
     * @param {number} options.userId - Authenticated user's ID for API calls
     * @param {number} [options.maxAccounts=6] - Maximum simultaneous SIP registrations
     * @param {Object} [options.apiHeaders] - HTTP headers for internal API calls
     */
    constructor(options = {}) {
        this.userId = options.userId;
        this.maxAccounts = options.maxAccounts || 6;
        this.apiHeaders = options.apiHeaders || {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        };

        /** @type {Map<number, {ua: object, registerer: object, config: object, registered: boolean}>} */
        this.accounts = new Map();

        /** @type {Map<string, {session: object, callId: string, extensionId: number, direction: string, number: string, startTime: Date, timer: number|null, answered: boolean, held: boolean, muted: boolean}>} */
        this.activeCalls = new Map();

        /** @type {HTMLAudioElement} Remote audio playback element */
        this.remoteAudio = this._createAudioElement('sip-remote-audio');

        /** @type {function|null} Callback: (callUUID, state, data) => void */
        this.onCallStateChange = null;

        /** @type {function|null} Callback: (extensionId, registered) => void */
        this.onRegistrationChange = null;

        /** @type {function|null} Callback: (extensionId, error) => void */
        this.onError = null;

        this._sipAvailable = typeof SIP !== 'undefined';
    }

    /** Check if SIP.js library is loaded and available */
    get sipAvailable() {
        return this._sipAvailable;
    }

    // ─── Registration ────────────────────────────────────────────────────

    /**
     * Register an extension with its SIP server.
     *
     * @param {Object} config
     * @param {number} config.extensionId - Extension DB ID
     * @param {string} config.server - WebSocket URL (wss://...)
     * @param {string} config.extension_number - SIP username / extension
     * @param {string} config.password - SIP password
     * @param {string} [config.displayName] - Caller display name
     * @param {string} [config.stunServer] - STUN server URL
     * @param {string} [config.turnServer] - TURN server URL
     * @param {string} [config.turnUsername] - TURN username
     * @param {string} [config.turnPassword] - TURN password
     * @param {string} [config.domain] - SIP domain (defaults to server hostname)
     * @returns {Promise<boolean>} true if registration started successfully
     */
    async register(config) {
        if (!this._sipAvailable) {
            this._emitError(config.extensionId, new Error('SIP.js is not loaded'));
            return false;
        }

        if (this.accounts.size >= this.maxAccounts) {
            this._emitError(config.extensionId, new Error(`Maximum ${this.maxAccounts} accounts reached`));
            return false;
        }

        if (this.accounts.has(config.extensionId)) {
            await this.unregister(config.extensionId);
        }

        try {
            const serverUrl = new URL(config.server);
            const domain = config.domain || serverUrl.hostname;
            const uri = SIP.UserAgent.makeURI(`sip:${config.extension_number}@${domain}`);
            if (!uri) throw new Error('Invalid SIP URI');

            const iceServers = this._buildIceServers(config);

            const transportOptions = {
                server: config.server,
                keepAliveInterval: 30,
            };

            const userAgentOptions = {
                uri,
                transportOptions,
                authorizationUsername: config.extension_number,
                authorizationPassword: config.password,
                displayName: config.displayName || config.extension_number,
                sessionDescriptionHandlerFactoryOptions: {
                    peerConnectionConfiguration: { iceServers },
                },
                delegate: {
                    onInvite: (invitation) => this._handleIncomingCall(config.extensionId, invitation),
                },
                logLevel: 'warn',
            };

            const ua = new SIP.UserAgent(userAgentOptions);
            const registerer = new SIP.Registerer(ua, { expires: 300 });

            registerer.stateChange.addListener((state) => {
                const registered = state === SIP.RegistererState.Registered;
                const account = this.accounts.get(config.extensionId);
                if (account) account.registered = registered;
                this._emitRegistration(config.extensionId, registered);
            });

            ua.transport.onDisconnect = (error) => {
                if (error) this._emitError(config.extensionId, error);
            };

            this.accounts.set(config.extensionId, {
                ua,
                registerer,
                config,
                registered: false,
            });

            await ua.start();
            await registerer.register();

            return true;
        } catch (error) {
            this._emitError(config.extensionId, error);
            this.accounts.delete(config.extensionId);
            return false;
        }
    }

    /**
     * Unregister an extension and clean up its resources.
     * Ends any active calls on this extension before unregistering.
     *
     * @param {number} extensionId
     * @returns {Promise<void>}
     */
    async unregister(extensionId) {
        const account = this.accounts.get(extensionId);
        if (!account) return;

        // End active calls on this extension
        for (const [uuid, call] of this.activeCalls) {
            if (call.extensionId === extensionId) {
                await this.endCall(uuid);
            }
        }

        try {
            if (account.registerer.state === SIP.RegistererState.Registered) {
                await account.registerer.unregister();
            }
            await account.ua.stop();
        } catch (error) {
            this._emitError(extensionId, error);
        }

        this.accounts.delete(extensionId);
        this._emitRegistration(extensionId, false);
    }

    // ─── Outbound Calls ──────────────────────────────────────────────────

    /**
     * Make an outbound call from a registered extension.
     *
     * @param {number} extensionId - The extension to call from
     * @param {string} target - SIP URI or phone number to dial
     * @returns {Promise<string|null>} Call UUID if started, null on failure
     */
    async makeCall(extensionId, target) {
        const account = this.accounts.get(extensionId);
        if (!account) {
            this._emitError(extensionId, new Error('Extension not registered'));
            return null;
        }

        try {
            const domain = account.config.domain || new URL(account.config.server).hostname;
            const targetURI = target.includes('@')
                ? SIP.UserAgent.makeURI(`sip:${target}`)
                : SIP.UserAgent.makeURI(`sip:${target}@${domain}`);

            if (!targetURI) throw new Error('Invalid target URI');

            const callUUID = crypto.randomUUID();
            const inviter = new SIP.Inviter(account.ua, targetURI, {
                sessionDescriptionHandlerOptions: {
                    constraints: { audio: true, video: false },
                },
            });

            const callData = {
                session: inviter,
                callId: null,
                extensionId,
                direction: 'outbound',
                number: target,
                startTime: new Date(),
                timer: null,
                answered: false,
                held: false,
                muted: false,
            };

            this.activeCalls.set(callUUID, callData);
            this._setupSessionHandlers(callUUID, inviter);

            await inviter.invite();
            this._emitCallState(callUUID, 'ringing', callData);

            // Record in API
            this._apiCreateCall(callUUID, extensionId, 'outbound', account.config.extension_number, target);

            return callUUID;
        } catch (error) {
            this._emitError(extensionId, error);
            return null;
        }
    }

    // ─── Call Control ────────────────────────────────────────────────────

    /**
     * Answer an inbound ringing call.
     *
     * @param {string} callUUID
     * @returns {Promise<boolean>}
     */
    async answerCall(callUUID) {
        const call = this.activeCalls.get(callUUID);
        if (!call || !call.session) return false;

        try {
            await call.session.accept({
                sessionDescriptionHandlerOptions: {
                    constraints: { audio: true, video: false },
                },
            });
            return true;
        } catch (error) {
            this._emitError(call.extensionId, error);
            return false;
        }
    }

    /**
     * End / hang up a call.
     *
     * @param {string} callUUID
     * @returns {Promise<boolean>}
     */
    async endCall(callUUID) {
        const call = this.activeCalls.get(callUUID);
        if (!call || !call.session) return false;

        try {
            const session = call.session;
            switch (session.state) {
                case SIP.SessionState.Initial:
                case SIP.SessionState.Establishing:
                    if (session instanceof SIP.Inviter) {
                        await session.cancel();
                    } else {
                        await session.reject();
                    }
                    break;
                case SIP.SessionState.Established:
                    await session.bye();
                    break;
                default:
                    break;
            }
            return true;
        } catch (error) {
            this._emitError(call.extensionId, error);
            return false;
        }
    }

    /**
     * Toggle hold state for a call.
     *
     * @param {string} callUUID
     * @returns {Promise<boolean>} New hold state
     */
    async toggleHold(callUUID) {
        const call = this.activeCalls.get(callUUID);
        if (!call || !call.session || call.session.state !== SIP.SessionState.Established) return false;

        try {
            const sdh = call.session.sessionDescriptionHandler;
            if (!sdh || !sdh.peerConnection) return false;

            call.held = !call.held;
            sdh.peerConnection.getSenders().forEach((sender) => {
                if (sender.track) sender.track.enabled = !call.held;
            });

            if (call.held) {
                await call.session.invite({ requestDelegate: {}, sessionDescriptionHandlerModifiers: [SIP.Web.holdModifier] });
            } else {
                await call.session.invite();
            }

            this._emitCallState(callUUID, call.held ? 'held' : 'resumed', call);
            return call.held;
        } catch (error) {
            call.held = !call.held; // revert
            this._emitError(call.extensionId, error);
            return call.held;
        }
    }

    /**
     * Toggle mute (local audio) for a call.
     *
     * @param {string} callUUID
     * @returns {boolean} New mute state
     */
    toggleMute(callUUID) {
        const call = this.activeCalls.get(callUUID);
        if (!call || !call.session) return false;

        try {
            const sdh = call.session.sessionDescriptionHandler;
            if (!sdh || !sdh.peerConnection) return false;

            call.muted = !call.muted;
            sdh.peerConnection.getSenders().forEach((sender) => {
                if (sender.track && sender.track.kind === 'audio') {
                    sender.track.enabled = !call.muted;
                }
            });

            this._emitCallState(callUUID, call.muted ? 'muted' : 'unmuted', call);
            return call.muted;
        } catch (error) {
            call.muted = !call.muted; // revert
            this._emitError(call.extensionId, error);
            return call.muted;
        }
    }

    /**
     * Get info for all currently active calls.
     *
     * @returns {Array<{uuid: string, direction: string, number: string, extensionId: number, startTime: Date, answered: boolean, held: boolean, muted: boolean}>}
     */
    getActiveCalls() {
        const calls = [];
        for (const [uuid, call] of this.activeCalls) {
            calls.push({
                uuid,
                direction: call.direction,
                number: call.number,
                extensionId: call.extensionId,
                startTime: call.startTime,
                answered: call.answered,
                held: call.held,
                muted: call.muted,
            });
        }
        return calls;
    }

    /**
     * Clean up all accounts and calls.
     *
     * @returns {Promise<void>}
     */
    async destroy() {
        const extensionIds = [...this.accounts.keys()];
        for (const id of extensionIds) {
            await this.unregister(id);
        }
    }

    // ─── Private: Session Handling ───────────────────────────────────────

    /**
     * Handle an incoming SIP INVITE.
     * @private
     */
    _handleIncomingCall(extensionId, invitation) {
        const callUUID = crypto.randomUUID();
        const account = this.accounts.get(extensionId);
        const callerNumber = invitation.remoteIdentity?.uri?.user || 'Unknown';

        const callData = {
            session: invitation,
            callId: null,
            extensionId,
            direction: 'inbound',
            number: callerNumber,
            startTime: new Date(),
            timer: null,
            answered: false,
            held: false,
            muted: false,
        };

        this.activeCalls.set(callUUID, callData);
        this._setupSessionHandlers(callUUID, invitation);
        this._emitCallState(callUUID, 'ringing', callData);

        // Record in API
        const extNumber = account?.config?.extension_number || '';
        this._apiCreateCall(callUUID, extensionId, 'inbound', callerNumber, extNumber);
    }

    /**
     * Attach state change and media handlers to a SIP session.
     * @private
     */
    _setupSessionHandlers(callUUID, session) {
        session.stateChange.addListener((state) => {
            const call = this.activeCalls.get(callUUID);
            if (!call) return;

            switch (state) {
                case SIP.SessionState.Establishing:
                    this._emitCallState(callUUID, 'connecting', call);
                    break;

                case SIP.SessionState.Established:
                    call.answered = true;
                    this._attachRemoteAudio(session);
                    this._emitCallState(callUUID, 'answered', call);
                    this._apiCallAnswered(call.callId);
                    break;

                case SIP.SessionState.Terminated:
                    if (call.timer) clearInterval(call.timer);
                    this._emitCallState(callUUID, 'ended', call);
                    this._apiCallEnded(call.callId);
                    this.activeCalls.delete(callUUID);
                    break;
            }
        });
    }

    /**
     * Attach the remote media stream to the audio element.
     * @private
     */
    _attachRemoteAudio(session) {
        const sdh = session.sessionDescriptionHandler;
        if (!sdh || !sdh.peerConnection) return;

        const receiver = sdh.peerConnection.getReceivers().find(r => r.track && r.track.kind === 'audio');
        if (receiver) {
            this.remoteAudio.srcObject = new MediaStream([receiver.track]);
            this.remoteAudio.play().catch(() => {});
        }
    }

    // ─── Private: ICE Configuration ──────────────────────────────────────

    /**
     * Build ICE server configuration from extension config.
     * @private
     */
    _buildIceServers(config) {
        const servers = [];

        if (config.stunServer) {
            servers.push({ urls: config.stunServer });
        } else {
            servers.push({ urls: 'stun:stun.l.google.com:19302' });
        }

        if (config.turnServer) {
            servers.push({
                urls: config.turnServer,
                username: config.turnUsername || '',
                credential: config.turnPassword || '',
            });
        }

        return servers;
    }

    // ─── Private: API Integration ────────────────────────────────────────

    /**
     * Create a call record in the internal API.
     * @private
     */
    async _apiCreateCall(callUUID, extensionId, direction, callerNumber, calleeNumber) {
        try {
            const res = await fetch('/internal/calls', {
                method: 'POST',
                headers: this.apiHeaders,
                body: JSON.stringify({
                    uuid: callUUID,
                    extension_id: extensionId,
                    user_id: this.userId,
                    direction,
                    caller_number: callerNumber,
                    callee_number: calleeNumber,
                    started_at: new Date().toISOString(),
                }),
            });
            if (res.ok) {
                const data = await res.json();
                const call = this.activeCalls.get(callUUID);
                if (call) call.callId = data.uuid;
            }
        } catch (error) {
            console.warn('SIPAccountManager: Failed to record call', error);
        }
    }

    /**
     * Mark call as answered in the internal API.
     * @private
     */
    async _apiCallAnswered(callId) {
        if (!callId) return;
        try {
            await fetch(`/internal/calls/${callId}/answered`, {
                method: 'POST',
                headers: this.apiHeaders,
                body: JSON.stringify({ answered_at: new Date().toISOString() }),
            });
        } catch (error) {
            console.warn('SIPAccountManager: Failed to record answer', error);
        }
    }

    /**
     * Mark call as ended in the internal API.
     * @private
     */
    async _apiCallEnded(callId) {
        if (!callId) return;
        try {
            await fetch(`/internal/calls/${callId}/ended`, {
                method: 'POST',
                headers: this.apiHeaders,
                body: JSON.stringify({ ended_at: new Date().toISOString() }),
            });
        } catch (error) {
            console.warn('SIPAccountManager: Failed to record end', error);
        }
    }

    // ─── Private: Event Emitters ─────────────────────────────────────────

    /** @private */
    _emitCallState(callUUID, state, data) {
        if (typeof this.onCallStateChange === 'function') {
            this.onCallStateChange(callUUID, state, {
                direction: data.direction,
                number: data.number,
                extensionId: data.extensionId,
                startTime: data.startTime,
                answered: data.answered,
                held: data.held,
                muted: data.muted,
            });
        }
    }

    /** @private */
    _emitRegistration(extensionId, registered) {
        if (typeof this.onRegistrationChange === 'function') {
            this.onRegistrationChange(extensionId, registered);
        }
    }

    /** @private */
    _emitError(extensionId, error) {
        console.error(`SIPAccountManager [ext ${extensionId}]:`, error);
        if (typeof this.onError === 'function') {
            this.onError(extensionId, error);
        }
    }

    // ─── Private: Utilities ──────────────────────────────────────────────

    /**
     * Create or retrieve an audio element for remote media playback.
     * @private
     */
    _createAudioElement(id) {
        let el = document.getElementById(id);
        if (!el) {
            el = document.createElement('audio');
            el.id = id;
            el.autoplay = true;
            document.body.appendChild(el);
        }
        return el;
    }
}

// Export for module environments; safe no-op in plain script tags
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SIPAccountManager;
}
