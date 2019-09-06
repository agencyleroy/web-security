# Web Security plugin for Craft CMS 3.x
Allows you to set [HTTP Strict Transport Security](https://infosec.mozilla.org/guidelines/web_security#http-strict-transport-security) and [Content Security Policy](https://infosec.mozilla.org/guidelines/web_security#content-security-policy) headers, and configure their values.

This plugin automatically adds a `nonce` attribute to inline styles and scripts added with `registerJs` and `registerCss` and adds a `{{ nonce() }}` twig function.

## Requirements
This plugin requires Craft CMS 3.0.0 or later.
