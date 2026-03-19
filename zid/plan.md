# Salla WhatsApp Plugin (Paid Workspace) – Implementation Plan

## Goal
Build a Salla marketplace app similar to the reference app (Cartat / WhatsApp automation) but integrated into this Chatwoot-based codebase, with **access limited to paid workspaces**.

This document defines the MVP plan for:
- A **Salla app/plugin** under `plugins/salla/`
- A **backend integration** (OAuth/token storage + webhooks)
- A minimal **admin UI** inside Chatwoot (or an external setup screen if required by Salla)
- A repeatable way to **package the plugin as a ZIP** for distribution

## Scope (MVP)
### In-scope
- Install/connect flow between Salla store and our system
- Token/auth storage per account/workspace
- Webhooks ingestion from Salla for:
  - Abandoned cart events (if available)
  - Order status changes
  - New customer events
  - Store/product review events (for “thank you + coupon” automation)
- Message sending via our WhatsApp provider(s) already supported in the codebase
- Paid plan gating: only paid workspaces can enable/operate the integration
- Basic templates management (enable/disable + variables)
- Delivery of digital products/files via WhatsApp (only if this is already supported by Salla webhook/API signals; otherwise deferred)
- Auditability: message log entries and webhook delivery logs (at least in app logs; UI log optional)

### Out-of-scope (defer)
- Full bot builder UI with branching tree + interactive buttons (unless there is already a reusable automation builder)
- Mass campaign import from Excel with segmentation
- Link shortener service (unless already present)
- Dedicated WhatsApp device, call rejection, contact/chat import
- Multi-number WhatsApp linking

## User Stories
- Merchant installs the app from Salla marketplace and gets redirected to a setup flow.
- Merchant connects Salla store to their Chatwoot account/workspace.
- Merchant selects which notifications to enable (abandoned cart, order updates, welcome message, review thank-you).
- System sends WhatsApp messages automatically based on events.
- If the workspace is not paid:
  - UI shows paywall
  - webhooks are accepted but **do not trigger message sends** (or are rejected depending on desired behavior)

## Architecture
### High-level data flow
- Salla -> our webhook endpoint(s) -> event normalization -> enqueue job -> send WhatsApp message -> log delivery

### Backend components (Rails)
- **Models** (names are suggestions; final names should match existing conventions):
  - `Salla::Installation` (store identifiers, workspace/account reference)nb
  - `Salla::AccessToken` / credentials fields (encrypted at rest)
  - `Salla::WebhookSubscription` (optional; if we programmatically subscribe)
  - `Salla::MessageTemplate` (enabled flags + body + variable mapping)
- **Controllers**:
  - OAuth/installation callback controller (handles initial install, token exchange)
  - Webhook controller (verifies signature, parses event, enqueues processing)
- **Services**:
  - `Salla::ApiClient` (REST calls to Salla)
  - `Salla::WebhookVerifier` (signature validation)
  - `Salla::EventProcessor` (normalize event -> internal event)
  - `Salla::MessageComposer` (template variables -> final message)
- **Jobs**:
  - `Salla::ProcessWebhookJob`
  - `Salla::SendWhatsappNotificationJob`

### Frontend (Dashboard)
- A settings screen under Integrations (or a plugin area if exists):
  - Connection status
  - Enable/disable toggles per template type
  - Template editor
  - Paid-plan gating banner
- No bare strings in templates; use frontend i18n (`en.json`).

## Paid Workspace Gating
### Rules
- Only paid workspaces can:
  - enable the integration
  - create/update templates
  - trigger outbound WhatsApp notifications from Salla events

### Enforcement points
- **UI**: hide/disable actions + show upgrade CTA
- **API**: enforce in controller/service layer
- **Jobs**: double-check gating before sending

### Implementation approach
- Reuse existing SaaS billing/plan checks in this codebase (if present).
- Prefer a single predicate like `account.paid_plan?` or `account.feature_enabled?(:salla_integration)`.
- If Enterprise overlays exist for billing/plan logic, ensure compatibility.

## Security & Compliance
- Validate Salla webhook signatures.
- Store access tokens encrypted (use Rails encrypted credentials patterns already in the app).
- Rate limit webhook endpoints.
- Avoid logging secrets/tokens.

## API Contracts (Draft)
### Webhook endpoint
- `POST /webhooks/salla` (exact route TBD)
- Headers:
  - Signature header (TBD; follow Salla docs)
- Body:
  - Salla event payload

### Installation callback
- `GET /integrations/salla/callback` (exact route TBD)
- Params:
  - `code`, `state` and any Salla-specific identifiers

## Templates (MVP set)
- Abandoned cart reminder
- Order status update
- Welcome new customer
- Verification code
- Review thank-you + optional coupon

Variables (examples; final should align with Salla payloads):
- `{customer_name}`
- `{order_number}`
- `{order_status}`
- `{cart_total}`
- `{track_link}`
- `{coupon_code}`

## Observability
- Log webhook receipt + event type + store id
- Log message send attempt + provider response
- Optional: lightweight UI table for last N events/messages

## Packaging as ZIP
### What the ZIP contains (proposal)
- `plugins/salla/**` only
- Exclude:
  - `node_modules`
  - `tmp`
  - `.DS_Store`

### Build command (proposal)
- Create a `dist/` folder (ignored by git) at repo root.
- Produce:
  - `dist/salla-plugin.zip`

### Suggested ZIP command
Run from repo root:

```bash
zip -r dist/salla-plugin.zip plugins/salla -x "**/.DS_Store" "**/node_modules/**" "**/tmp/**"
```

If we add a script later, it should follow existing repo patterns (e.g. `script/` or `bin/`).

## Milestones
1. **Discovery**: confirm Salla auth + webhook specs, required permissions, event payloads.
2. **Backend skeleton**: routes/controllers/models/services + signature verification.
3. **Paid gating**: reuse plan checks + enforce in API/jobs.
4. **Message sending**: integrate with existing WhatsApp provider service layer.
5. **Dashboard UI**: connection status + template toggles/editor.
6. **Packaging**: documented ZIP packaging; optional script.

## Open Questions
- Which Salla auth method is required for marketplace apps (OAuth2, API key, etc.)?
- Which webhook events are available for carts/orders/reviews and what are their payload schemas?
- Do we need multi-tenant mapping by Salla store id -> Chatwoot account id?
- Should non-paid workspaces have webhooks rejected (4xx) or accepted but no-op?
- Which WhatsApp provider is mandatory for this plugin in your environment?
