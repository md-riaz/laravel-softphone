@extends('layouts.app')
@section('title', 'Agent Console')
@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Console</li>
</ol>
@endsection

@push('styles')
<style>
    .active-call-card {
        border-radius: 0.5rem;
        border: 1px solid #e2e8f0;
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        background: #fff;
        transition: border-color 0.2s;
    }
    .active-call-card.ringing { border-color: #f59e0b; background: #fffbeb; }
    .active-call-card.answered { border-color: #22c55e; background: #f0fdf4; }
    .active-call-card.held { border-color: #6366f1; background: #eef2ff; }
    .call-timer { font-family: 'Courier New', monospace; font-size: 1.1rem; font-weight: 600; }
    .wrapup-panel { display: none; }
    .wrapup-panel.show { display: block; }
    .disposition-btn { cursor: pointer; transition: all 0.2s; }
    .disposition-btn.selected { box-shadow: 0 0 0 2px rgba(59,130,246,0.5); transform: scale(1.05); }
    .dialer-display {
        font-family: 'Courier New', monospace;
        font-size: 1.5rem;
        text-align: center;
        letter-spacing: 2px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.75rem;
    }
    .sip-warning { display: none; }
    .sip-warning.show { display: block; }
    #extensionList .extension-card { cursor: default; }
    .no-calls-placeholder {
        text-align: center;
        padding: 2rem 1rem;
        color: #94a3b8;
    }
    .no-calls-placeholder i { font-size: 2.5rem; margin-bottom: 0.5rem; }
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1><i class="bi bi-headset me-2"></i>Agent Console</h1>
    <span class="text-muted small" id="consoleClock"></span>
</div>

{{-- SIP.js load warning --}}
<div class="alert alert-warning sip-warning" id="sipWarning" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <strong>SIP.js not loaded.</strong> Real-time calling features are unavailable. The dialer UI remains functional for demo purposes.
</div>

{{-- Hidden audio elements --}}
<audio id="sip-remote-audio" autoplay></audio>
<audio id="ringtone-audio" loop preload="auto"></audio>

<div class="row g-3">
    {{-- ─── Left Column: Extensions + Dialer ─── --}}
    <div class="col-lg-4">

        {{-- Extension Management --}}
        <div class="card table-card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-phone-fill me-2"></i>Extensions</span>
                <span class="badge bg-secondary" id="activeExtCount">0 / {{ $extensions->count() }}</span>
            </div>
            <div class="card-body p-3" id="extensionList">
                @forelse($extensions as $ext)
                <div class="extension-card {{ $ext->is_active ? 'active' : '' }} {{ $ext->is_registered ? 'registered' : '' }}"
                     id="ext-card-{{ $ext->id }}"
                     data-ext-id="{{ $ext->id }}"
                     data-ext-number="{{ $ext->extension_number }}"
                     data-ext-name="{{ $ext->display_name ?? $ext->extension_number }}"
                     data-pbx-name="{{ $ext->pbxConnection->name ?? '' }}"
                     data-wss="{{ $ext->pbxConnection->wss_url ?? '' }}"
                     data-stun="{{ $ext->pbxConnection->stun_server ?? '' }}"
                     data-turn="{{ $ext->pbxConnection->turn_server ?? '' }}"
                     data-turn-user="{{ $ext->pbxConnection->turn_username ?? '' }}"
                     data-active="{{ $ext->is_active ? '1' : '0' }}"
                     data-registered="{{ $ext->is_registered ? '1' : '0' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $ext->display_name ?? $ext->extension_number }}</strong>
                            <div class="text-muted small">{{ $ext->pbxConnection->name ?? 'No PBX' }} &middot; Ext {{ $ext->extension_number }}</div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge" id="ext-status-{{ $ext->id }}">
                                @if($ext->is_registered)
                                    <span class="badge bg-primary">Registered</span>
                                @elseif($ext->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </span>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input ext-toggle" type="checkbox" role="switch"
                                       id="ext-toggle-{{ $ext->id }}"
                                       data-ext-id="{{ $ext->id }}"
                                       {{ $ext->is_active ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-3 mb-0">No extensions assigned. Contact your administrator.</p>
                @endforelse
            </div>
        </div>

        {{-- Dialer --}}
        <div class="card form-card mb-3">
            <div class="card-header"><i class="bi bi-telephone-fill me-2"></i>Dialer</div>
            <div class="card-body">
                {{-- Extension selector --}}
                <div class="mb-3">
                    <select class="form-select form-select-sm" id="dialerExtension">
                        <option value="">Select extension...</option>
                        @foreach($extensions->where('is_active', true) as $ext)
                        <option value="{{ $ext->id }}">{{ $ext->display_name ?? $ext->extension_number }} ({{ $ext->extension_number }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Display --}}
                <div class="dialer-display mb-3" id="dialerDisplay">&nbsp;</div>

                {{-- Number pad --}}
                <div class="phone-dialer mx-auto mb-3">
                    <div class="d-flex justify-content-center gap-2 mb-2">
                        <button class="dial-btn" data-digit="1">1</button>
                        <button class="dial-btn" data-digit="2">2</button>
                        <button class="dial-btn" data-digit="3">3</button>
                    </div>
                    <div class="d-flex justify-content-center gap-2 mb-2">
                        <button class="dial-btn" data-digit="4">4</button>
                        <button class="dial-btn" data-digit="5">5</button>
                        <button class="dial-btn" data-digit="6">6</button>
                    </div>
                    <div class="d-flex justify-content-center gap-2 mb-2">
                        <button class="dial-btn" data-digit="7">7</button>
                        <button class="dial-btn" data-digit="8">8</button>
                        <button class="dial-btn" data-digit="9">9</button>
                    </div>
                    <div class="d-flex justify-content-center gap-2 mb-2">
                        <button class="dial-btn" data-digit="*">*</button>
                        <button class="dial-btn" data-digit="0">0</button>
                        <button class="dial-btn" data-digit="#">#</button>
                    </div>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="dial-btn call-btn" id="btnCall" title="Call">
                            <i class="bi bi-telephone-fill"></i>
                        </button>
                        <button class="dial-btn" id="btnBackspace" title="Backspace">
                            <i class="bi bi-backspace"></i>
                        </button>
                        <button class="dial-btn hangup-btn" id="btnHangupDialer" title="Hangup">
                            <i class="bi bi-telephone-x-fill"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Right Column: Active Calls + Wrap-up + Recent ─── --}}
    <div class="col-lg-8">

        {{-- Active Calls --}}
        <div class="card table-card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-broadcast me-2"></i>Active Calls</span>
                <span class="badge bg-primary" id="activeCallCount">0</span>
            </div>
            <div class="card-body p-3" id="activeCallsList">
                <div class="no-calls-placeholder" id="noCallsPlaceholder">
                    <i class="bi bi-telephone-x d-block"></i>
                    <div>No active calls</div>
                    <small>Use the dialer to make a call or wait for incoming calls.</small>
                </div>
            </div>
        </div>

        {{-- Wrap-up Panel --}}
        <div class="card form-card mb-3 wrapup-panel" id="wrapupPanel">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clipboard-check me-2"></i>Call Wrap-up</span>
                <button class="btn btn-sm btn-outline-secondary" id="btnSkipWrapup">Skip</button>
            </div>
            <div class="card-body">
                <div class="mb-2 small text-muted" id="wrapupCallInfo"></div>
                <input type="hidden" id="wrapupCallId">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Notes</label>
                    <textarea class="form-control" id="wrapupNotes" rows="3" placeholder="Add call notes..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Dispositions</label>
                    <div class="d-flex flex-wrap gap-2" id="dispositionList">
                        @forelse($dispositions as $disp)
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary disposition-btn"
                                data-disp-id="{{ $disp->id }}"
                                style="border-color: {{ $disp->color ?? '#6b7280' }}; color: {{ $disp->color ?? '#6b7280' }};">
                            {{ $disp->name }}
                        </button>
                        @empty
                        <span class="text-muted small">No dispositions configured.</span>
                        @endforelse
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary" id="btnSaveWrapup">
                        <i class="bi bi-check-lg me-1"></i>Save
                    </button>
                    <button class="btn btn-outline-secondary" id="btnSkipWrapup2">Skip</button>
                </div>
            </div>
        </div>

        {{-- Recent Calls --}}
        <div class="card table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i>Recent Calls</span>
                <a href="{{ route('agent.call-history') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Direction</th>
                            <th>Number</th>
                            <th>Extension</th>
                            <th>Status</th>
                            <th>Duration</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody id="recentCallsBody">
                        <tr><td colspan="6" class="text-center text-muted py-4">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- SIP.js CDN with fallback --}}
<script>
window._sipReady = new Promise(function(resolve) {
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/sip.js@0.21.2/lib/index.min.js';
    s.onload = function() { window._sipLoaded = true; resolve(true); };
    s.onerror = function() {
        window._sipLoaded = false;
        document.getElementById('sipWarning')?.classList.add('show');
        console.warn('SIP.js failed to load from CDN.');
        resolve(false);
    };
    document.head.appendChild(s);
});
</script>
<script src="{{ asset('js/sip-account-manager.js') }}"></script>
<script>
(function() {
    'use strict';

    // UUID generator with fallback for older browsers
    function generateUUID() {
        if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
            return crypto.randomUUID();
        }
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0;
            return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
        });
    }

    // ─── Configuration ───────────────────────────────────────────────────
    const USER_ID = {{ auth()->id() }};
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

    // ─── API Helper ──────────────────────────────────────────────────────
    const API = {
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
        },

        async activateExtension(id) {
            const res = await fetch(`/internal/extensions/${id}/activate`, { method: 'POST', headers: this.headers });
            return res.json();
        },

        async deactivateExtension(id) {
            const res = await fetch(`/internal/extensions/${id}/deactivate`, { method: 'POST', headers: this.headers });
            return res.json();
        },

        async createCall(data) {
            const res = await fetch('/internal/calls', { method: 'POST', headers: this.headers, body: JSON.stringify(data) });
            return res.json();
        },

        async callAnswered(id, answeredAt) {
            const res = await fetch(`/internal/calls/${id}/answered`, { method: 'POST', headers: this.headers, body: JSON.stringify({ answered_at: answeredAt }) });
            return res.json();
        },

        async callEnded(id, endedAt) {
            const res = await fetch(`/internal/calls/${id}/ended`, { method: 'POST', headers: this.headers, body: JSON.stringify({ ended_at: endedAt }) });
            return res.json();
        },

        async callWrapup(id, notes, dispositionIds) {
            const res = await fetch(`/internal/calls/${id}/wrapup`, { method: 'POST', headers: this.headers, body: JSON.stringify({ notes, disposition_ids: dispositionIds }) });
            return res.json();
        },

        async getRecentCalls() {
            const res = await fetch('/internal/calls?per_page=5', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN } });
            return res.json();
        },
    };

    // ─── Console UI Manager ──────────────────────────────────────────────
    class ConsoleUI {
        constructor() {
            this.dialerNumber = '';
            this.activeCalls = new Map();
            this.callTimers = new Map();
            this.wrapupCallId = null;
            this.selectedDispositions = new Set();
            this.sipManager = null;
            this._bindElements();
            this._bindEvents();
            this._initSipManager();
            this._updateClock();
            this._loadRecentCalls();
            this._updateActiveExtCount();
            setInterval(() => this._updateClock(), 1000);
        }

        _bindElements() {
            this.elDialerDisplay = document.getElementById('dialerDisplay');
            this.elDialerExtension = document.getElementById('dialerExtension');
            this.elActiveCallsList = document.getElementById('activeCallsList');
            this.elActiveCallCount = document.getElementById('activeCallCount');
            this.elNoCallsPlaceholder = document.getElementById('noCallsPlaceholder');
            this.elWrapupPanel = document.getElementById('wrapupPanel');
            this.elWrapupCallId = document.getElementById('wrapupCallId');
            this.elWrapupCallInfo = document.getElementById('wrapupCallInfo');
            this.elWrapupNotes = document.getElementById('wrapupNotes');
            this.elRecentCallsBody = document.getElementById('recentCallsBody');
            this.elActiveExtCount = document.getElementById('activeExtCount');
            this.elSipWarning = document.getElementById('sipWarning');
        }

        _bindEvents() {
            // Dialer buttons
            document.querySelectorAll('.dial-btn[data-digit]').forEach(btn => {
                btn.addEventListener('click', () => this._appendDigit(btn.dataset.digit));
            });

            document.getElementById('btnBackspace')?.addEventListener('click', () => {
                this.dialerNumber = this.dialerNumber.slice(0, -1);
                this._updateDialerDisplay();
            });

            document.getElementById('btnCall')?.addEventListener('click', () => this._makeCall());
            document.getElementById('btnHangupDialer')?.addEventListener('click', () => this._hangupCurrentCall());

            // Extension toggles
            document.querySelectorAll('.ext-toggle').forEach(toggle => {
                toggle.addEventListener('change', (e) => this._toggleExtension(e.target));
            });

            // Wrap-up
            document.querySelectorAll('.disposition-btn').forEach(btn => {
                btn.addEventListener('click', () => this._toggleDisposition(btn));
            });
            document.getElementById('btnSaveWrapup')?.addEventListener('click', () => this._saveWrapup());
            document.getElementById('btnSkipWrapup')?.addEventListener('click', () => this._hideWrapup());
            document.getElementById('btnSkipWrapup2')?.addEventListener('click', () => this._hideWrapup());

            // Active call actions (delegated)
            this.elActiveCallsList.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-action]');
                if (!btn) return;
                const uuid = btn.dataset.uuid;
                switch (btn.dataset.action) {
                    case 'answer': this._answerCall(uuid); break;
                    case 'hangup': this._endCall(uuid); break;
                    case 'hold':   this._toggleHold(uuid); break;
                    case 'mute':   this._toggleMute(uuid); break;
                }
            });

            // Keyboard dial support
            document.addEventListener('keydown', (e) => {
                if (document.activeElement.tagName === 'TEXTAREA' || document.activeElement.tagName === 'INPUT') return;
                if (/^[0-9*#]$/.test(e.key)) {
                    e.preventDefault();
                    this._appendDigit(e.key);
                } else if (e.key === 'Backspace') {
                    e.preventDefault();
                    this.dialerNumber = this.dialerNumber.slice(0, -1);
                    this._updateDialerDisplay();
                } else if (e.key === 'Enter' && this.dialerNumber) {
                    e.preventDefault();
                    this._makeCall();
                }
            });
        }

        async _initSipManager() {
            const sipLoaded = await window._sipReady;

            if (!sipLoaded || typeof SIP === 'undefined') {
                this.elSipWarning?.classList.add('show');
                console.warn('SIP.js not available – running in UI-only mode.');
                return;
            }

            this.sipManager = new SIPAccountManager({ userId: USER_ID, apiHeaders: API.headers });

                this.sipManager.onCallStateChange = (uuid, state, data) => {
                    this._handleSipCallState(uuid, state, data);
                };

                this.sipManager.onRegistrationChange = (extId, registered) => {
                    this._updateExtensionStatus(extId, true, registered);
                };

                this.sipManager.onError = (extId, error) => {
                    console.warn(`SIP error [ext ${extId}]:`, error.message || error);
                };

                // Auto-register already-active extensions
                document.querySelectorAll('.extension-card[data-active="1"]').forEach(card => {
                    this._registerExtension(card.dataset);
                });
        }

        // ── Dialer ──────────────────────────────────────────────────────

        _appendDigit(digit) {
            if (this.dialerNumber.length >= 20) return;
            this.dialerNumber += digit;
            this._updateDialerDisplay();
        }

        _updateDialerDisplay() {
            this.elDialerDisplay.textContent = this.dialerNumber || '\u00A0';
        }

        async _makeCall() {
            if (!this.dialerNumber) return;
            const extId = parseInt(this.elDialerExtension.value);
            if (!extId) {
                alert('Please select an active extension first.');
                return;
            }

            if (this.sipManager) {
                const uuid = await this.sipManager.makeCall(extId, this.dialerNumber);
                if (uuid) this.dialerNumber = '';
                this._updateDialerDisplay();
            } else {
                // UI-only demo mode: create a simulated call record
                const uuid = generateUUID();
                const extCard = document.getElementById(`ext-card-${extId}`);
                const extNumber = extCard?.dataset.extNumber || '';

                try {
                    const call = await API.createCall({
                        uuid,
                        extension_id: extId,
                        user_id: USER_ID,
                        direction: 'outbound',
                        caller_number: extNumber,
                        callee_number: this.dialerNumber,
                        started_at: new Date().toISOString(),
                    });
                    this._addActiveCall(uuid, {
                        direction: 'outbound',
                        number: this.dialerNumber,
                        extensionId: extId,
                        startTime: new Date(),
                        answered: false,
                        held: false,
                        muted: false,
                    });
                    this.activeCalls.get(uuid).callId = call.uuid || uuid;
                } catch (err) {
                    console.warn('Failed to create call record:', err);
                }
                this.dialerNumber = '';
                this._updateDialerDisplay();
            }
        }

        _hangupCurrentCall() {
            // Hang up the most recent active call
            const uuids = [...this.activeCalls.keys()];
            if (uuids.length > 0) this._endCall(uuids[uuids.length - 1]);
        }

        // ── Extension Management ────────────────────────────────────────

        async _toggleExtension(toggle) {
            const extId = parseInt(toggle.dataset.extId);
            const activate = toggle.checked;

            // Enforce max 6 in UI
            if (activate) {
                const activeCount = document.querySelectorAll('.ext-toggle:checked').length;
                if (activeCount > 6) {
                    toggle.checked = false;
                    alert('Maximum of 6 active extensions allowed.');
                    return;
                }
            }

            try {
                const result = activate
                    ? await API.activateExtension(extId)
                    : await API.deactivateExtension(extId);

                if (result.message) {
                    this._updateExtensionStatus(extId, activate, false);
                    this._updateDialerExtensionOptions();
                    this._updateActiveExtCount();

                    if (activate && this.sipManager) {
                        const card = document.getElementById(`ext-card-${extId}`);
                        if (card) this._registerExtension(card.dataset);
                    } else if (!activate && this.sipManager) {
                        this.sipManager.unregister(extId);
                    }
                }

                if (result.message?.includes('Maximum')) {
                    toggle.checked = false;
                    alert(result.message);
                }
            } catch (err) {
                toggle.checked = !activate;
                console.error('Toggle extension failed:', err);
            }
        }

        async _registerExtension(data) {
            if (!this.sipManager || !data.wss) return;
            // Fetch SIP credentials securely from the backend
            let password = '';
            try {
                const res = await fetch(`/internal/extensions/${data.extId}/sip-credentials`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN } });
                if (res.ok) { const creds = await res.json(); password = creds.password || ''; }
            } catch (e) { /* proceed without password if endpoint unavailable */ }
            this.sipManager.register({
                extensionId: parseInt(data.extId),
                server: data.wss,
                extension_number: data.extNumber,
                password,
                displayName: data.extName,
                stunServer: data.stun || undefined,
                turnServer: data.turn || undefined,
                turnUsername: data.turnUser || undefined,
            });
        }

        _updateExtensionStatus(extId, active, registered) {
            const card = document.getElementById(`ext-card-${extId}`);
            const statusEl = document.getElementById(`ext-status-${extId}`);
            if (!card) return;

            card.classList.toggle('active', active);
            card.classList.toggle('registered', registered);
            card.dataset.active = active ? '1' : '0';
            card.dataset.registered = registered ? '1' : '0';

            if (statusEl) {
                if (registered) {
                    statusEl.innerHTML = '<span class="badge bg-primary">Registered</span>';
                } else if (active) {
                    statusEl.innerHTML = '<span class="badge bg-success">Active</span>';
                } else {
                    statusEl.innerHTML = '<span class="badge bg-secondary">Inactive</span>';
                }
            }
        }

        _updateDialerExtensionOptions() {
            const select = this.elDialerExtension;
            const current = select.value;
            const options = ['<option value="">Select extension...</option>'];
            document.querySelectorAll('.extension-card[data-active="1"]').forEach(card => {
                const id = card.dataset.extId;
                const name = card.dataset.extName;
                const number = card.dataset.extNumber;
                options.push(`<option value="${id}" ${id === current ? 'selected' : ''}>${name} (${number})</option>`);
            });
            select.innerHTML = options.join('');
        }

        _updateActiveExtCount() {
            const total = document.querySelectorAll('.extension-card').length;
            const active = document.querySelectorAll('.extension-card[data-active="1"]').length;
            this.elActiveExtCount.textContent = `${active} / ${total}`;
        }

        // ── Active Calls ────────────────────────────────────────────────

        _handleSipCallState(uuid, state, data) {
            switch (state) {
                case 'ringing':
                    this._addActiveCall(uuid, data);
                    break;
                case 'answered':
                    this._updateCallStatus(uuid, 'answered', data);
                    break;
                case 'held':
                case 'resumed':
                    this._updateCallHoldMute(uuid, data);
                    break;
                case 'muted':
                case 'unmuted':
                    this._updateCallHoldMute(uuid, data);
                    break;
                case 'ended':
                    this._removeActiveCall(uuid, data);
                    break;
            }
        }

        _addActiveCall(uuid, data) {
            this.activeCalls.set(uuid, { ...data, status: 'ringing' });
            this._renderActiveCalls();
            this._startCallTimer(uuid);
        }

        _updateCallStatus(uuid, status, data) {
            const call = this.activeCalls.get(uuid);
            if (!call) return;
            call.status = status;
            call.answered = data?.answered ?? call.answered;
            this._renderActiveCalls();
        }

        _updateCallHoldMute(uuid, data) {
            const call = this.activeCalls.get(uuid);
            if (!call) return;
            call.held = data?.held ?? call.held;
            call.muted = data?.muted ?? call.muted;
            this._renderActiveCalls();
        }

        _removeActiveCall(uuid, data) {
            const call = this.activeCalls.get(uuid);
            this._stopCallTimer(uuid);
            this.activeCalls.delete(uuid);
            this._renderActiveCalls();
            this._loadRecentCalls();

            // Show wrap-up panel
            if (call) {
                this._showWrapup(call.callId || uuid, call);
            }
        }

        async _answerCall(uuid) {
            if (this.sipManager) {
                await this.sipManager.answerCall(uuid);
            } else {
                // Demo mode
                const call = this.activeCalls.get(uuid);
                if (call && call.callId) {
                    await API.callAnswered(call.callId, new Date().toISOString());
                }
                this._updateCallStatus(uuid, 'answered', { answered: true });
            }
        }

        async _endCall(uuid) {
            if (this.sipManager) {
                await this.sipManager.endCall(uuid);
            } else {
                // Demo mode
                const call = this.activeCalls.get(uuid);
                if (call && call.callId) {
                    await API.callEnded(call.callId, new Date().toISOString());
                }
                this._removeActiveCall(uuid, call);
            }
        }

        async _toggleHold(uuid) {
            if (this.sipManager) {
                await this.sipManager.toggleHold(uuid);
            } else {
                const call = this.activeCalls.get(uuid);
                if (call) { call.held = !call.held; this._renderActiveCalls(); }
            }
        }

        async _toggleMute(uuid) {
            if (this.sipManager) {
                this.sipManager.toggleMute(uuid);
            } else {
                const call = this.activeCalls.get(uuid);
                if (call) { call.muted = !call.muted; this._renderActiveCalls(); }
            }
        }

        _startCallTimer(uuid) {
            const timer = setInterval(() => {
                const el = document.getElementById(`timer-${uuid}`);
                const call = this.activeCalls.get(uuid);
                if (el && call) {
                    el.textContent = this._formatDuration(call.startTime);
                }
            }, 1000);
            this.callTimers.set(uuid, timer);
        }

        _stopCallTimer(uuid) {
            const timer = this.callTimers.get(uuid);
            if (timer) { clearInterval(timer); this.callTimers.delete(uuid); }
        }

        _formatDuration(startTime) {
            const diff = Math.floor((Date.now() - new Date(startTime).getTime()) / 1000);
            const h = Math.floor(diff / 3600);
            const m = Math.floor((diff % 3600) / 60);
            const s = diff % 60;
            if (h > 0) return `${h}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
            return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        }

        _renderActiveCalls() {
            const count = this.activeCalls.size;
            this.elActiveCallCount.textContent = count;

            if (count === 0) {
                this.elActiveCallsList.innerHTML = `
                    <div class="no-calls-placeholder" id="noCallsPlaceholder">
                        <i class="bi bi-telephone-x d-block"></i>
                        <div>No active calls</div>
                        <small>Use the dialer to make a call or wait for incoming calls.</small>
                    </div>`;
                return;
            }

            let html = '';
            for (const [uuid, call] of this.activeCalls) {
                const statusClass = call.held ? 'held' : (call.answered ? 'answered' : 'ringing');
                const dirBadge = call.direction === 'inbound' ? 'badge-inbound' : 'badge-outbound';
                const extCard = document.getElementById(`ext-card-${call.extensionId}`);
                const extName = extCard?.dataset.extName || `Ext ${call.extensionId}`;

                html += `
                <div class="active-call-card ${statusClass}" id="call-${uuid}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge ${dirBadge}">${call.direction === 'inbound' ? '<i class="bi bi-telephone-inbound me-1"></i>Inbound' : '<i class="bi bi-telephone-outbound me-1"></i>Outbound'}</span>
                                <strong>${this._escapeHtml(call.number)}</strong>
                                ${call.held ? '<span class="badge bg-warning text-dark">On Hold</span>' : ''}
                                ${call.muted ? '<span class="badge bg-secondary">Muted</span>' : ''}
                            </div>
                            <div class="text-muted small">
                                <i class="bi bi-phone me-1"></i>${this._escapeHtml(extName)}
                                <span class="ms-2 call-timer" id="timer-${uuid}">${this._formatDuration(call.startTime)}</span>
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            ${!call.answered && call.direction === 'inbound' ? `<button class="btn btn-sm btn-success" data-action="answer" data-uuid="${uuid}" title="Answer"><i class="bi bi-telephone-fill"></i></button>` : ''}
                            ${call.answered ? `<button class="btn btn-sm ${call.held ? 'btn-warning' : 'btn-outline-warning'}" data-action="hold" data-uuid="${uuid}" title="${call.held ? 'Resume' : 'Hold'}"><i class="bi bi-pause-fill"></i></button>` : ''}
                            ${call.answered ? `<button class="btn btn-sm ${call.muted ? 'btn-secondary' : 'btn-outline-secondary'}" data-action="mute" data-uuid="${uuid}" title="${call.muted ? 'Unmute' : 'Mute'}"><i class="bi bi-mic-mute-fill"></i></button>` : ''}
                            <button class="btn btn-sm btn-danger" data-action="hangup" data-uuid="${uuid}" title="Hangup"><i class="bi bi-telephone-x-fill"></i></button>
                        </div>
                    </div>
                </div>`;
            }
            this.elActiveCallsList.innerHTML = html;
        }

        // ── Wrap-up ─────────────────────────────────────────────────────

        _showWrapup(callId, call) {
            this.wrapupCallId = callId;
            this.selectedDispositions.clear();
            document.querySelectorAll('.disposition-btn').forEach(b => b.classList.remove('selected'));
            this.elWrapupNotes.value = '';
            this.elWrapupCallId.value = callId;
            this.elWrapupCallInfo.textContent = `${call.direction === 'inbound' ? 'Inbound' : 'Outbound'} call with ${call.number}`;
            this.elWrapupPanel.classList.add('show');
            this.elWrapupNotes.focus();
        }

        _hideWrapup() {
            this.elWrapupPanel.classList.remove('show');
            this.wrapupCallId = null;
            this.selectedDispositions.clear();
        }

        _toggleDisposition(btn) {
            const id = parseInt(btn.dataset.dispId);
            if (this.selectedDispositions.has(id)) {
                this.selectedDispositions.delete(id);
                btn.classList.remove('selected');
            } else {
                this.selectedDispositions.add(id);
                btn.classList.add('selected');
            }
        }

        async _saveWrapup() {
            if (!this.wrapupCallId) return;
            const notes = this.elWrapupNotes.value.trim();
            const dispositionIds = [...this.selectedDispositions];

            try {
                await API.callWrapup(this.wrapupCallId, notes || null, dispositionIds);
                this._hideWrapup();
                this._loadRecentCalls();
            } catch (err) {
                console.error('Wrapup save failed:', err);
                alert('Failed to save wrap-up. Please try again.');
            }
        }

        // ── Recent Calls ────────────────────────────────────────────────

        async _loadRecentCalls() {
            try {
                const result = await API.getRecentCalls();
                const calls = result.data || [];
                if (calls.length === 0) {
                    this.elRecentCallsBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No calls yet</td></tr>';
                    return;
                }
                this.elRecentCallsBody.innerHTML = calls.slice(0, 5).map(call => `
                    <tr>
                        <td><span class="badge badge-${call.direction}">${call.direction === 'inbound' ? 'Inbound' : 'Outbound'}</span></td>
                        <td>${this._escapeHtml(call.direction === 'inbound' ? call.caller_number : call.callee_number)}</td>
                        <td>${call.extension ? this._escapeHtml(call.extension.extension_number) : '-'}</td>
                        <td><span class="badge badge-${call.status}">${call.status ? call.status.charAt(0).toUpperCase() + call.status.slice(1) : '-'}</span></td>
                        <td>${call.duration ? this._formatSecondsToHMS(call.duration) : '-'}</td>
                        <td class="small">${call.started_at ? new Date(call.started_at).toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-'}</td>
                    </tr>
                `).join('');
            } catch (err) {
                this.elRecentCallsBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Failed to load calls</td></tr>';
            }
        }

        _formatSecondsToHMS(seconds) {
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            if (h > 0) return `${h}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
            return `${m}:${String(s).padStart(2, '0')}`;
        }

        // ── Utilities ───────────────────────────────────────────────────

        _updateClock() {
            const el = document.getElementById('consoleClock');
            if (el) el.textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }

        _escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }
    }

    // ─── Initialize on DOM ready ─────────────────────────────────────────
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => new ConsoleUI());
    } else {
        new ConsoleUI();
    }
})();
</script>
@endpush
