# Etechflow_RedirectManager

A 301/302 **redirect manager** with a built-in **404 catcher** for Magento 2 / Adobe Commerce.
Part of the Etechflow SEO Suite. A leaner alternative to the redirect tools in Mirasvit/Amasty.

## Features

- **301 / 302 redirects** managed from the admin (grid + add/edit form), per store view, with an
  active toggle, internal notes, and a live **hit counter** per redirect.
- **Runs before `urlrewrite`** — a custom router (sortOrder 15) so managed redirects take
  precedence over catalog/CMS URL rewrites.
- **404 catcher** — every not-found URL is logged (with hit count + referrer). One click on a
  404 row creates a draft redirect and opens it for you to set the target.
- Target can be a **path** (`new-page`) or a **full URL** (`https://…`).
- Fully **flag-gated** — master switch (off by default); inert until enabled.
- Configurable **exclude regex** so static assets / noise aren't logged.

## Where it lives

- **Marketing → Redirect Manager → Redirects** — the redirect grid
- **Marketing → Redirect Manager → 404 Log** — captured 404s + "Create Redirect"
- **Stores → Configuration → Etechflow → Redirect Manager** — settings

## Configuration

| Path | Default | Notes |
|------|---------|-------|
| `etechflow_redirectmanager/general/enabled` | `0` | Master switch (engine + 404 logger) |
| `etechflow_redirectmanager/redirects/default_type` | `301` | Default status for new redirects |
| `etechflow_redirectmanager/log404/enabled` | `1` | Log not-found URLs |
| `etechflow_redirectmanager/log404/exclude_patterns` | asset regex | Paths matching are not logged |

## URL-key changes

Magento's native *"Create Permanent Redirect for old URLs"* (Stores → Configuration → Catalog →
Catalog → Search Engine Optimization) still creates `url_rewrite` 301s on product/category URL-key
changes. This module's **404 catcher backstops** anything that slips through, so you can convert it
to a managed redirect in one click.

## Install

```bash
bin/magento module:enable Etechflow_RedirectManager
bin/magento setup:upgrade
bin/magento setup:static-content:deploy <locale> -f --area frontend   # prod: setup:upgrade clears var/view_preprocessed
rm -rf generated/code/* generated/metadata/*                          # di:compile won't clean this itself
bin/magento setup:di:compile
bin/magento cache:flush
bin/magento config:set etechflow_redirectmanager/general/enabled 1
```

## Tables

- `etechflow_redirect` — managed redirects (request_path → target_path, type, store, active, hits)
- `etechflow_redirect_404_log` — captured 404s (request_path unique per store, hit upsert)

## Tests

```bash
vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist app/code/Etechflow/RedirectManager/Test/Unit
```
