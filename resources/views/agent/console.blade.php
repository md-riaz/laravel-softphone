@extends('layouts.app')
@section('title', 'Agent Console')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<span>Console</span>
@endsection

@push('styles')
<style>
    .console-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; }
    @media (max-width: 1024px) { .console-grid { grid-template-columns: 1fr; } }
    .active-call-card {
        border-radius: 0.5rem;
        border: 1px solid var(--border);
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        background: var(--card);
        transition: border-color 0.2s;
    }
    .active-call-card.ringing { border-color: hsl(var(--warning)); background: hsl(var(--warning) / 0.05); }
    .active-call-card.answered { border-color: hsl(var(--success)); background: hsl(var(--success) / 0.05); }
    .active-call-card.held { border-color: var(--primary); background: hsl(var(--primary) / 0.05); }
    .wrapup-panel { display: none; }
    .wrapup-panel.show { display: block; }
    .disposition-btn { cursor: pointer; transition: all 0.2s; }
    .disposition-btn.selected { box-shadow: 0 0 0 2px var(--ring); transform: scale(1.05); }
    .dialer-display {
        font-family: 'Courier New', monospace;
        font-size: 1.5rem;
        text-align: center;
        letter-spacing: 2px;
        background: var(--muted);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 0.75rem;
        color: var(--foreground);
    }
    .sip-warning { display: none; padding: 0.75rem 1rem; background: hsl(var(--warning) / 0.1); border: 1px solid hsl(var(--warning) / 0.3); border-radius: var(--radius); color: var(--foreground); font-size: 0.875rem; margin-bottom: 1rem; }
    .sip-warning.show { display: block; }
    .no-calls-placeholder { text-align: center; padding: 2rem 1rem; color: var(--muted-foreground); }
    .no-calls-placeholder svg { width: 2.5rem; height: 2.5rem; margin-bottom: 0.5rem; }
    .call-action-row { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }
    .call-action-btns { display: flex; gap: 0.25rem; }
    .call-info-row { display: flex; justify-content: space-between; align-items: flex-start; }
    .ext-card-row { display: flex; justify-content: space-between; align-items: center; }
    .ext-card-toggle { display: flex; align-items: center; gap: 0.5rem; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Agent Console</h1>
            <p class="page-description" id="consoleClock"></p>
        </div>
    </div>
</div>

<div class="sip-warning" id="sipWarning">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;vertical-align:middle;margin-right:4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
    <strong>SIP.js not loaded.</strong> Real-time calling features are unavailable. The dialer UI remains functional for demo purposes.
</div>

<audio id="sip-remote-audio" autoplay></audio>
<audio id="ringtone-audio" loop preload="auto"></audio>

<div class="console-grid">
    {{-- Left Column: Extensions + Dialer --}}
    <div>
        <div class="card" style="margin-bottom: 1rem;">
            <div class="card-header">
                <h3 class="card-title">Extensions</h3>
                <span class="badge badge-secondary" id="activeExtCount">0 / {{ $extensions->count() }}</span>
            </div>
            <div class="card-body" id="extensionList">
                @forelse($extensions as $ext)
                <div class="extension-card {{ $ext->is_active ? 'ext-active' : '' }} {{ $ext->is_registered ? 'ext-registered' : '' }}"
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
                    <div class="ext-card-row">
                        <div>
                            <strong>{{ $ext->display_name ?? $ext->extension_number }}</strong>
                            <div style="font-size:0.75rem;color:var(--muted-foreground);">{{ $ext->pbxConnection->name ?? 'No PBX' }} &middot; Ext {{ $ext->extension_number }}</div>
                        </div>
                        <div class="ext-card-toggle">
                            <span id="ext-status-{{ $ext->id }}">
                                @if($ext->is_registered)
                                    <span class="badge badge-primary">Registered</span>
                                @elseif($ext->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </span>
                            <label style="position:relative;display:inline-block;width:36px;height:20px;">
                                <input class="checkbox-input ext-toggle" type="checkbox"
                                       id="ext-toggle-{{ $ext->id }}"
                                       data-ext-id="{{ $ext->id }}"
                                       {{ $ext->is_active ? 'checked' : '' }}
                                       style="width:36px;height:20px;">
                            </label>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <h3 class="empty-state-title">No extensions assigned</h3>
                    <p class="empty-state-description">Contact your administrator.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Dialer --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Dialer</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <select class="form-select" id="dialerExtension">
                        <option value="">Select extension...</option>
                        @foreach($extensions->where('is_active', true) as $ext)
                        <option value="{{ $ext->id }}">{{ $ext->display_name ?? $ext->extension_number }} ({{ $ext->extension_number }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="dialer-display" id="dialerDisplay">&nbsp;</div>

                <div class="phone-dialer" style="margin-top:1rem;">
                    <div class="dial-pad">
                        <button class="dial-btn" data-digit="1">1</button>
                        <button class="dial-btn" data-digit="2">2</button>
                        <button class="dial-btn" data-digit="3">3</button>
                        <button class="dial-btn" data-digit="4">4</button>
                        <button class="dial-btn" data-digit="5">5</button>
                        <button class="dial-btn" data-digit="6">6</button>
                        <button class="dial-btn" data-digit="7">7</button>
                        <button class="dial-btn" data-digit="8">8</button>
                        <button class="dial-btn" data-digit="9">9</button>
                        <button class="dial-btn" data-digit="*">*</button>
                        <button class="dial-btn" data-digit="0">0</button>
                        <button class="dial-btn" data-digit="#">#</button>
                    </div>
                    <div style="display:flex;justify-content:center;gap:0.5rem;margin-top:0.75rem;">
                        <button class="dial-btn dial-btn-call" id="btnCall" title="Call">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                        </button>
                        <button class="dial-btn" id="btnBackspace" title="Backspace">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75L14.25 12m0 0l2.25 2.25M14.25 12l2.25-2.25M14.25 12L12 14.25m-2.58 4.92l-6.375-6.375a1.125 1.125 0 010-1.59L9.42 4.83c.211-.211.498-.33.796-.33H19.5a2.25 2.25 0 012.25 2.25v10.5a2.25 2.25 0 01-2.25 2.25h-9.284c-.298 0-.585-.119-.796-.33z" /></svg>
                        </button>
                        <button class="dial-btn dial-btn-hangup" id="btnHangupDialer" title="Hangup">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 3.75L18 6m0 0l2.25 2.25M18 6l2.25-2.25M18 6l-2.25 2.25m1.5 13.5c-8.284 0-15-6.716-15-15v-2.25A2.25 2.25 0 014.5 2.25h1.372c.516 0 .966.351 1.091.852l1.106 4.423c.11.44-.055.902-.417 1.173l-1.293.97a1.062 1.062 0 00-.38 1.21 12.035 12.035 0 007.143 7.143c.441.162.928-.004 1.21-.38l.97-1.293a1.125 1.125 0 011.173-.417l4.423 1.106c.5.125.852.575.852 1.091V19.5a2.25 2.25 0 01-2.25 2.25h-2.25z" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Active Calls + Wrap-up + Recent --}}
    <div>
        <div class="card" style="margin-bottom: 1rem;">
            <div class="card-header">
                <h3 class="card-title">Active Calls</h3>
                <span class="badge badge-primary" id="activeCallCount">0</span>
            </div>
            <div class="card-body" id="activeCallsList">
                <div class="no-calls-placeholder" id="noCallsPlaceholder">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:block;margin:0 auto;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 3.75L18 6m0 0l2.25 2.25M18 6l2.25-2.25M18 6l-2.25 2.25m1.5 13.5c-8.284 0-15-6.716-15-15v-2.25A2.25 2.25 0 014.5 2.25h1.372c.516 0 .966.351 1.091.852l1.106 4.423c.11.44-.055.902-.417 1.173l-1.293.97a1.062 1.062 0 00-.38 1.21 12.035 12.035 0 007.143 7.143c.441.162.928-.004 1.21-.38l.97-1.293a1.125 1.125 0 011.173-.417l4.423 1.106c.5.125.852.575.852 1.091V19.5a2.25 2.25 0 01-2.25 2.25h-2.25z" /></svg>
                    <div>No active calls</div>
                    <small>Use the dialer to make a call or wait for incoming calls.</small>
                </div>
            </div>
        </div>

        {{-- Wrap-up Panel --}}
        <div class="card wrapup-panel" id="wrapupPanel" style="margin-bottom: 1rem;">
            <div class="card-header">
                <h3 class="card-title">Call Wrap-up</h3>
                <button class="btn btn-sm btn-secondary" id="btnSkipWrapup">Skip</button>
            </div>
            <div class="card-body">
                <div style="font-size:0.875rem;color:var(--muted-foreground);margin-bottom:0.75rem;" id="wrapupCallInfo"></div>
                <input type="hidden" id="wrapupCallId">

                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea class="form-textarea" id="wrapupNotes" rows="3" placeholder="Add call notes..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Dispositions</label>
                    <div style="display:flex;flex-wrap:wrap;gap:0.5rem;" id="dispositionList">
                        @forelse($dispositions as $disp)
                        <button type="button"
                                class="btn btn-sm btn-ghost disposition-btn"
                                data-disp-id="{{ $disp->id }}"
                                style="border: 1px solid {{ $disp->color ?? '#6b7280' }}; color: {{ $disp->color ?? '#6b7280' }};">
                            {{ $disp->name }}
                        </button>
                        @empty
                        <span style="color:var(--muted-foreground);font-size:0.875rem;">No dispositions configured.</span>
                        @endforelse
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn btn-primary" id="btnSaveWrapup">Save</button>
                    <button class="btn btn-secondary" id="btnSkipWrapup2">Skip</button>
                </div>
            </div>
        </div>

        {{-- Recent Calls --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Calls</h3>
                <a href="{{ route('agent.call-history') }}" class="btn btn-sm btn-secondary">View All</a>
            </div>
            <div class="table-container">
                <table class="table">
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
                        <tr><td colspan="6" style="text-align:center;color:var(--muted-foreground);padding:2rem 0;">Loading...</td></tr>
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

    function generateUUID() {
        if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
            return crypto.randomUUID();
        }
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0;
            return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
        });
    }

    const USER_ID = {{ auth()->id() }};
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

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
            document.querySelectorAll('.dial-btn[data-digit]').forEach(btn => {
                btn.addEventListener('click', () => this._appendDigit(btn.dataset.digit));
            });
            document.getElementById('btnBackspace')?.addEventListener('click', () => {
                this.dialerNumber = this.dialerNumber.slice(0, -1);
                this._updateDialerDisplay();
            });
            document.getElementById('btnCall')?.addEventListener('click', () => this._makeCall());
            document.getElementById('btnHangupDialer')?.addEventListener('click', () => this._hangupCurrentCall());

            document.querySelectorAll('.ext-toggle').forEach(toggle => {
                toggle.addEventListener('change', (e) => this._toggleExtension(e.target));
            });

            document.querySelectorAll('.disposition-btn').forEach(btn => {
                btn.addEventListener('click', () => this._toggleDisposition(btn));
            });
            document.getElementById('btnSaveWrapup')?.addEventListener('click', () => this._saveWrapup());
            document.getElementById('btnSkipWrapup')?.addEventListener('click', () => this._hideWrapup());
            document.getElementById('btnSkipWrapup2')?.addEventListener('click', () => this._hideWrapup());

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
                return;
            }
            this.sipManager = new SIPAccountManager({ userId: USER_ID, apiHeaders: API.headers });
            this.sipManager.onCallStateChange = (uuid, state, data) => { this._handleSipCallState(uuid, state, data); };
            this.sipManager.onRegistrationChange = (extId, registered) => { this._updateExtensionStatus(extId, true, registered); };
            this.sipManager.onError = (extId, error) => { console.warn(`SIP error [ext ${extId}]:`, error.message || error); };
            document.querySelectorAll('.extension-card[data-active="1"]').forEach(card => { this._registerExtension(card.dataset); });
        }

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
            if (!extId) { alert('Please select an active extension first.'); return; }

            if (this.sipManager) {
                const uuid = await this.sipManager.makeCall(extId, this.dialerNumber);
                if (uuid) this.dialerNumber = '';
                this._updateDialerDisplay();
            } else {
                const uuid = generateUUID();
                const extCard = document.getElementById(`ext-card-${extId}`);
                const extNumber = extCard?.dataset.extNumber || '';
                try {
                    const call = await API.createCall({
                        uuid, extension_id: extId, user_id: USER_ID, direction: 'outbound',
                        caller_number: extNumber, callee_number: this.dialerNumber, started_at: new Date().toISOString(),
                    });
                    this._addActiveCall(uuid, {
                        direction: 'outbound', number: this.dialerNumber, extensionId: extId,
                        startTime: new Date(), answered: false, held: false, muted: false,
                    });
                    this.activeCalls.get(uuid).callId = call.uuid || uuid;
                } catch (err) { console.warn('Failed to create call record:', err); }
                this.dialerNumber = '';
                this._updateDialerDisplay();
            }
        }

        _hangupCurrentCall() {
            const uuids = [...this.activeCalls.keys()];
            if (uuids.length > 0) this._endCall(uuids[uuids.length - 1]);
        }

        async _toggleExtension(toggle) {
            const extId = parseInt(toggle.dataset.extId);
            const activate = toggle.checked;
            if (activate) {
                const activeCount = document.querySelectorAll('.ext-toggle:checked').length;
                if (activeCount > 6) { toggle.checked = false; alert('Maximum of 6 active extensions allowed.'); return; }
            }
            try {
                const result = activate ? await API.activateExtension(extId) : await API.deactivateExtension(extId);
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
                if (result.message?.includes('Maximum')) { toggle.checked = false; alert(result.message); }
            } catch (err) { toggle.checked = !activate; console.error('Toggle extension failed:', err); }
        }

        async _registerExtension(data) {
            if (!this.sipManager || !data.wss) return;
            let password = '';
            try {
                const res = await fetch(`/internal/extensions/${data.extId}/sip-credentials`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN } });
                if (res.ok) { const creds = await res.json(); password = creds.password || ''; }
            } catch (e) { /* proceed without password */ }
            this.sipManager.register({
                extensionId: parseInt(data.extId), server: data.wss, extension_number: data.extNumber,
                password, displayName: data.extName, stunServer: data.stun || undefined,
                turnServer: data.turn || undefined, turnUsername: data.turnUser || undefined,
            });
        }

        _updateExtensionStatus(extId, active, registered) {
            const card = document.getElementById(`ext-card-${extId}`);
            const statusEl = document.getElementById(`ext-status-${extId}`);
            if (!card) return;
            card.classList.toggle('ext-active', active);
            card.classList.toggle('ext-registered', registered);
            card.dataset.active = active ? '1' : '0';
            card.dataset.registered = registered ? '1' : '0';
            if (statusEl) {
                if (registered) { statusEl.innerHTML = '<span class="badge badge-primary">Registered</span>'; }
                else if (active) { statusEl.innerHTML = '<span class="badge badge-success">Active</span>'; }
                else { statusEl.innerHTML = '<span class="badge badge-secondary">Inactive</span>'; }
            }
        }

        _updateDialerExtensionOptions() {
            const select = this.elDialerExtension;
            const current = select.value;
            const options = ['<option value="">Select extension...</option>'];
            document.querySelectorAll('.extension-card[data-active="1"]').forEach(card => {
                const id = card.dataset.extId;
                options.push(`<option value="${id}" ${id === current ? 'selected' : ''}>${card.dataset.extName} (${card.dataset.extNumber})</option>`);
            });
            select.innerHTML = options.join('');
        }

        _updateActiveExtCount() {
            const total = document.querySelectorAll('.extension-card').length;
            const active = document.querySelectorAll('.extension-card[data-active="1"]').length;
            this.elActiveExtCount.textContent = `${active} / ${total}`;
        }

        _handleSipCallState(uuid, state, data) {
            switch (state) {
                case 'ringing': this._addActiveCall(uuid, data); break;
                case 'answered': this._updateCallStatus(uuid, 'answered', data); break;
                case 'held': case 'resumed': this._updateCallHoldMute(uuid, data); break;
                case 'muted': case 'unmuted': this._updateCallHoldMute(uuid, data); break;
                case 'ended': this._removeActiveCall(uuid, data); break;
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
            if (call) { this._showWrapup(call.callId || uuid, call); }
        }

        async _answerCall(uuid) {
            if (this.sipManager) { await this.sipManager.answerCall(uuid); }
            else {
                const call = this.activeCalls.get(uuid);
                if (call && call.callId) { await API.callAnswered(call.callId, new Date().toISOString()); }
                this._updateCallStatus(uuid, 'answered', { answered: true });
            }
        }

        async _endCall(uuid) {
            if (this.sipManager) { await this.sipManager.endCall(uuid); }
            else {
                const call = this.activeCalls.get(uuid);
                if (call && call.callId) { await API.callEnded(call.callId, new Date().toISOString()); }
                this._removeActiveCall(uuid, call);
            }
        }

        async _toggleHold(uuid) {
            if (this.sipManager) { await this.sipManager.toggleHold(uuid); }
            else { const call = this.activeCalls.get(uuid); if (call) { call.held = !call.held; this._renderActiveCalls(); } }
        }

        async _toggleMute(uuid) {
            if (this.sipManager) { this.sipManager.toggleMute(uuid); }
            else { const call = this.activeCalls.get(uuid); if (call) { call.muted = !call.muted; this._renderActiveCalls(); } }
        }

        _startCallTimer(uuid) {
            const timer = setInterval(() => {
                const el = document.getElementById(`timer-${uuid}`);
                const call = this.activeCalls.get(uuid);
                if (el && call) { el.textContent = this._formatDuration(call.startTime); }
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
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:2.5rem;height:2.5rem;display:block;margin:0 auto;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 3.75L18 6m0 0l2.25 2.25M18 6l2.25-2.25M18 6l-2.25 2.25m1.5 13.5c-8.284 0-15-6.716-15-15v-2.25A2.25 2.25 0 014.5 2.25h1.372c.516 0 .966.351 1.091.852l1.106 4.423c.11.44-.055.902-.417 1.173l-1.293.97a1.062 1.062 0 00-.38 1.21 12.035 12.035 0 007.143 7.143c.441.162.928-.004 1.21-.38l.97-1.293a1.125 1.125 0 011.173-.417l4.423 1.106c.5.125.852.575.852 1.091V19.5a2.25 2.25 0 01-2.25 2.25h-2.25z" /></svg>
                        <div>No active calls</div>
                        <small>Use the dialer to make a call or wait for incoming calls.</small>
                    </div>`;
                return;
            }
            let html = '';
            for (const [uuid, call] of this.activeCalls) {
                const statusClass = call.held ? 'held' : (call.answered ? 'answered' : 'ringing');
                const dirBadge = call.direction === 'inbound' ? 'badge-info' : 'badge-primary';
                const extCard = document.getElementById(`ext-card-${call.extensionId}`);
                const extName = extCard?.dataset.extName || `Ext ${call.extensionId}`;
                const phoneIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;vertical-align:middle;margin-right:2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>';
                html += `
                <div class="active-call-card ${statusClass}" id="call-${uuid}">
                    <div class="call-info-row">
                        <div>
                            <div class="call-action-row" style="margin-bottom:0.25rem;">
                                <span class="badge ${dirBadge}">${call.direction === 'inbound' ? 'Inbound' : 'Outbound'}</span>
                                <strong>${this._escapeHtml(call.number)}</strong>
                                ${call.held ? '<span class="badge badge-warning">On Hold</span>' : ''}
                                ${call.muted ? '<span class="badge badge-secondary">Muted</span>' : ''}
                            </div>
                            <div style="font-size:0.75rem;color:var(--muted-foreground);">
                                ${phoneIcon}${this._escapeHtml(extName)}
                                <span class="call-timer" id="timer-${uuid}" style="margin-left:0.5rem;">${this._formatDuration(call.startTime)}</span>
                            </div>
                        </div>
                        <div class="call-action-btns">
                            ${!call.answered && call.direction === 'inbound' ? `<button class="btn btn-sm btn-primary" data-action="answer" data-uuid="${uuid}" title="Answer"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg></button>` : ''}
                            ${call.answered ? `<button class="btn btn-sm ${call.held ? 'btn-primary' : 'btn-secondary'}" data-action="hold" data-uuid="${uuid}" title="${call.held ? 'Resume' : 'Hold'}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" /></svg></button>` : ''}
                            ${call.answered ? `<button class="btn btn-sm ${call.muted ? 'btn-primary' : 'btn-secondary'}" data-action="mute" data-uuid="${uuid}" title="${call.muted ? 'Unmute' : 'Mute'}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 9.75L19.5 12m0 0l2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-6l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" /></svg></button>` : ''}
                            <button class="btn btn-sm btn-danger" data-action="hangup" data-uuid="${uuid}" title="Hangup"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 3.75L18 6m0 0l2.25 2.25M18 6l2.25-2.25M18 6l-2.25 2.25m1.5 13.5c-8.284 0-15-6.716-15-15v-2.25A2.25 2.25 0 014.5 2.25h1.372c.516 0 .966.351 1.091.852l1.106 4.423c.11.44-.055.902-.417 1.173l-1.293.97a1.062 1.062 0 00-.38 1.21 12.035 12.035 0 007.143 7.143c.441.162.928-.004 1.21-.38l.97-1.293a1.125 1.125 0 011.173-.417l4.423 1.106c.5.125.852.575.852 1.091V19.5a2.25 2.25 0 01-2.25 2.25h-2.25z" /></svg></button>
                        </div>
                    </div>
                </div>`;
            }
            this.elActiveCallsList.innerHTML = html;
        }

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
            if (this.selectedDispositions.has(id)) { this.selectedDispositions.delete(id); btn.classList.remove('selected'); }
            else { this.selectedDispositions.add(id); btn.classList.add('selected'); }
        }

        async _saveWrapup() {
            if (!this.wrapupCallId) return;
            const notes = this.elWrapupNotes.value.trim();
            const dispositionIds = [...this.selectedDispositions];
            try {
                await API.callWrapup(this.wrapupCallId, notes || null, dispositionIds);
                this._hideWrapup();
                this._loadRecentCalls();
            } catch (err) { console.error('Wrapup save failed:', err); alert('Failed to save wrap-up. Please try again.'); }
        }

        async _loadRecentCalls() {
            try {
                const result = await API.getRecentCalls();
                const calls = result.data || [];
                if (calls.length === 0) {
                    this.elRecentCallsBody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--muted-foreground);padding:2rem 0;">No calls yet</td></tr>';
                    return;
                }
                this.elRecentCallsBody.innerHTML = calls.slice(0, 5).map(call => `
                    <tr>
                        <td><span class="badge ${call.direction === 'inbound' ? 'badge-info' : 'badge-primary'}">${call.direction === 'inbound' ? 'Inbound' : 'Outbound'}</span></td>
                        <td>${this._escapeHtml(call.direction === 'inbound' ? call.caller_number : call.callee_number)}</td>
                        <td>${call.extension ? this._escapeHtml(call.extension.extension_number) : '-'}</td>
                        <td><span class="badge ${call.status === 'answered' ? 'badge-success' : call.status === 'missed' ? 'badge-danger' : 'badge-secondary'}">${call.status ? call.status.charAt(0).toUpperCase() + call.status.slice(1) : '-'}</span></td>
                        <td>${call.duration ? this._formatSecondsToHMS(call.duration) : '-'}</td>
                        <td style="font-size:0.875rem;">${call.started_at ? new Date(call.started_at).toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-'}</td>
                    </tr>
                `).join('');
            } catch (err) {
                this.elRecentCallsBody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--muted-foreground);padding:2rem 0;">Failed to load calls</td></tr>';
            }
        }

        _formatSecondsToHMS(seconds) {
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            if (h > 0) return `${h}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
            return `${m}:${String(s).padStart(2, '0')}`;
        }

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

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => new ConsoleUI());
    } else {
        new ConsoleUI();
    }
})();
</script>
@endpush
