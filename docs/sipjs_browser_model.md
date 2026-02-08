# SIP.js Browser Integration Model

## Overview
SIP.js (v0.21.2) is bundled locally as `public/js/sip.js` for WebRTC softphone functionality. The Laravel backend has NO involvement in SIP signaling — all call control happens in the browser.

## Local Asset
The SIP.js library is pre-bundled (IIFE format via esbuild) and served from `public/js/sip.js`. No CDN dependency.

## UserAgent Instantiation
```javascript
const ua = new SIP.UserAgent({
    uri: SIP.UserAgent.makeURI(`sip:${extension}@${domain}`),
    transportOptions: { server: wssUrl },
    authorizationUsername: extension,
    authorizationPassword: password,
    displayName: displayName
});
```

## Registration Lifecycle
1. `ua.start()` — connects WebSocket transport
2. `new SIP.Registerer(ua).register()` — sends SIP REGISTER
3. On success: extension is registered with PBX
4. `registerer.unregister()` — sends SIP unREGISTER
5. `ua.stop()` — disconnects transport

## Session Lifecycle Events
- **Inbound**: `ua.delegate.onInvite(invitation)` → `invitation.accept()` → session established
- **Outbound**: `new SIP.Inviter(ua, targetURI).invite()` → session established
- **Terminated**: `session.delegate.onBye()` or `session.bye()`

## Multi-Account Pattern (SIPAccountManager)
- One UserAgent per extension (max 6)
- Each UA has its own WebSocket connection
- `SIPAccountManager` class tracks all accounts in a Map
- Each account independently registers/unregisters
- File: `public/js/sip-account-manager.js`

## Event Flow to Backend
1. Browser handles all SIP events locally
2. Browser POSTs analytics to `/internal/calls` endpoints
3. Backend only stores data, never controls calls
4. If API is down, calls still work — only analytics are lost

## STUN/TURN Configuration
Configured per PBX connection via admin UI. Passed to SIP.js as:
```javascript
sessionDescriptionHandlerOptions: {
    peerConnectionConfiguration: {
        iceServers: [
            { urls: stunServer },
            { urls: turnServer, username: turnUser, credential: turnPass }
        ]
    }
}
```

## Call Flow

### Inbound
1. SIP.js receives INVITE via WebSocket
2. Browser generates call UUID
3. Browser POSTs `POST /internal/calls` (direction: inbound)
4. User answers in browser UI
5. Browser POSTs `POST /internal/calls/{id}/answered`
6. Call ends → Browser POSTs `POST /internal/calls/{id}/ended`
7. Wrap-up → Browser POSTs `POST /internal/calls/{id}/wrapup`

### Outbound
1. Agent dials number in browser dialer
2. Browser POSTs `POST /internal/calls` (direction: outbound)
3. Browser initiates SIP INVITE via SIP.js
4. Events posted as they occur (answered, ended, wrapup)
