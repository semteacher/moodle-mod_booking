[Back to documentation index](../README.md)

# Browser console troubleshooting

A Moodle page can render correctly while its console contains many messages. A single
course page may contain the Moodle parent document and several independent iframe
documents. Each frame loads its own JavaScript, analytics, and APIs, so one failed
third-party request can produce a long stack trace. The number of console lines is
therefore not the number of Moodle or mod_booking defects.

This guide is for triage. It does not establish the cause of a production incident
without a network trace and the generated page source.

## Classify messages before changing code

In DevTools, enable the **Frame** and **Initiator** columns and preserve the network
log. Reload once with extensions disabled or in a clean browser profile. For every
message, record:

1. the top-level page or iframe that emitted it;
2. the request URL and initiator;
3. whether it is an error, warning, or informational message;
4. whether a user-facing feature actually failed; and
5. whether the response changes on another network or without DNS/ad blocking.

Do not patch mod_booking merely because the top-level course contains a Booking
activity. A stack whose initiator belongs to Zoom, Airtable, POWR, MiniExtensions,
Google, Microsoft Clarity, Cloudflare, or New Relic normally belongs to that embed or
to site-wide theme/custom HTML, not to the Booking module.

## Common messages and recommended action

### `JQMIGRATE: Migrate is installed`

This is informational. It says that the jQuery migration compatibility layer loaded;
it is not a failed request. It often reveals that the theme or another plugin still
uses legacy jQuery. Inventory the scripts on the page and migrate owned code to Moodle
AMD/ES modules. Remove the migration layer only after testing every dependent theme
and plugin; hiding the message does not fix deprecated code.

### bootstrap-select cannot retrieve Bootstrap's version

This is the most important site-wide JavaScript error when it also appears on the
landing page. `bootstrap-select` is looking for a jQuery Bootstrap plugin such as
`$.fn.dropdown.Constructor`, but the expected object is absent. Likely causes are:

- bootstrap-select was loaded before Moodle/theme Bootstrap initialization;
- the library expects a different Bootstrap major version;
- a theme, custom HTML block, tag manager, or plugin loaded a second jQuery or
  Bootstrap and replaced the objects Moodle registered; or
- a concatenated/minified bundle runs outside Moodle's dependency loader.

Locate the original script behind the generated `head:<line>` entry using the Network
**Initiator** view and source maps. Search the theme and additional-HTML settings as
well as installed plugins. Load one compatible copy through Moodle's module/dependency
system, remove duplicate CDN copies, and initialize it only after its dependencies.
Setting `$.fn.selectpicker.Constructor.BootstrapVersion` can be a diagnostic or a
vendor-documented compatibility setting, but it must not be used to conceal a real
version mismatch. Purge Moodle caches after changing JavaScript and retest with
JavaScript caching temporarily disabled.

### `net::ERR_ADDRESS_INVALID` for analytics or monitoring

The browser did not make a usable connection. When the same status affects unrelated
well-known hosts (for example Google Analytics, Cloudflare Insights, Clarity, New
Relic, and POWR), the common cause is usually client/network filtering: an extension,
managed-browser policy, DNS sinkhole, hosts-file entry, firewall, or privacy proxy may
resolve tracking domains to an invalid/non-routable address. This is distinct from an
HTTP 4xx or 5xx returned by the remote service.

Compare a clean profile and a second network, inspect DNS results and the browser's
request-blocking/enterprise-policy pages, and check response headers and remote IPs in
a HAR. If tracking is optional, treat blocking as expected and ensure application
features do not depend on it. Otherwise ask the network/privacy administrator to
allow only the required domains. Do not broadly weaken Content Security Policy or
disable browser security.

### MiniExtensions API returns HTTP 500

Unlike a browser-side address error, `500 Internal Server Error` means the endpoint
was reached and failed while processing the request. Inspect the response body and
the vendor request/correlation ID, validate the embed token or portal configuration,
and check the provider's logs/status. Escalate to MiniExtensions with a redacted HAR.
The subsequent Stackdriver error can merely be failed error reporting and can greatly
inflate the console output; it is not necessarily a second application failure.

### Zoom gallery view requires `SharedArrayBuffer`

This is a capability warning. Gallery view needs a cross-origin-isolated context,
normally involving compatible `Cross-Origin-Opener-Policy` and
`Cross-Origin-Embedder-Policy` headers and compliant cross-origin resources. The WASM
preload success messages are informational. Before adding isolation headers, test all
SSO, popup, payment, LTI, and iframe integrations: isolation can prevent existing
cross-origin resources from loading. If the header change is unsafe, use a supported
Zoom view that does not require `SharedArrayBuffer`, or open the meeting outside the
embedded Moodle page.

### iframe has both `allow-scripts` and `allow-same-origin`

For a same-origin iframe, that combination may allow its script to remove the sandbox
and defeats the intended isolation. Give a frame only the capabilities it needs.
Prefer placing active/untrusted embed content on a separate origin; otherwise remove
`allow-same-origin` or `allow-scripts` if the application can operate without it. Do
not remove the entire sandbox merely to silence the warning. For a cross-origin vendor
frame, confirm the vendor's documented attributes and threat model.

### Permissions Policy says `unload` is not allowed

This is generally a warning from legacy third-party code attempting to register an
`unload` handler inside a context where policy disallows it. Update or replace the
embed and use modern lifecycle events in code you own. Expanding iframe permissions
solely to suppress the warning is not recommended. If functionality is unaffected,
track it as vendor technical debt rather than a Moodle outage.

### preloaded Airtable SVG was not used

This is a performance warning generated by the Airtable frame. It does not indicate
that the SVG failed to load. Only the frame provider can reliably remove or retune its
preload. Remove unused embeds or lazy-load off-screen frames; otherwise report it to
the provider and avoid changing Moodle code.

### Moodle session-timeout messages

`Starting Moodle session timeout warning` and `Not starting ... in this iframe` are
diagnostic messages. The latter prevents duplicate timeout UI in a child frame. They
do not indicate session loss unless accompanied by failed Moodle requests or visible
logout behaviour.

## Recommended investigation order

1. **Fix the global bootstrap-select exception first.** Reproduce on the landing page,
   switch temporarily to the unmodified Boost theme, and disable non-core plugins or
   site-wide custom HTML in a staging clone. Re-enable components one at a time.
2. **Separate frames.** Use DevTools' frame selector and test the top document with the
   embeds removed. A clean parent console confirms that remaining errors belong to
   external content.
3. **Test filtering.** Use a clean profile and another network. Multiple unrelated
   `ERR_ADDRESS_INVALID` tracking hosts strongly suggest one shared filtering layer.
4. **Investigate the real HTTP 500.** Capture a redacted HAR and vendor request ID.
   Never publish cookies, authorization headers, meeting passwords, API keys, or full
   analytics query strings.
5. **Choose a Zoom mode deliberately.** Enable cross-origin isolation only after a
   staging compatibility audit of every cross-origin resource.
6. **Update supported components.** Confirm the Moodle, theme, plugin, and embedded SDK
   compatibility matrix. Moodle 4.1 is an old branch; plan an upgrade to a currently
   supported Moodle release rather than accumulating JavaScript shims.
7. **Retest by impact.** Verify navigation, login/session expiry, booking actions,
   meeting join, and each embed. Optional telemetry being blocked is not a functional
   acceptance-test failure.

## Evidence to collect for escalation

- exact Moodle, theme, plugin, and browser versions;
- whether the error occurs with Boost and with third-party extensions disabled;
- generated script URL rather than only `head:83` or `main.js:2`;
- a redacted HAR including initiator, status, response headers, and frame URL;
- relevant response headers, especially CSP, Permissions-Policy, COOP, and COEP;
- `window.crossOriginIsolated` and availability of `SharedArrayBuffer` for Zoom tests;
- the first exception in chronological order, before secondary reporting failures;
- a short description of the user-visible failure, if any.

This evidence lets the Moodle administrator route each item to the correct owner:
theme/plugin maintainer, network administrator, embed provider, or mod_booking.
