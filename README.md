# Etechflow SEO Suite

A complete, production-grade **SEO toolkit for Magento 2** (Open Source / Adobe Commerce, 2.4.x, PHP ≥ 8.1, Hyvä + Luma). Eight focused modules that cover the full SEO surface — and work together: the **SEO Audit** finds problems and points at the module that fixes each one.

`composer require etechflow/seo-suite` installs all eight. Or buy any module à la carte.

## The modules

| Module | Package | What it does |
|--------|---------|--------------|
| **Rich Snippets** | `etechflow/module-rich-snippets` | Hyvä-native schema.org JSON-LD (`@graph`) — Product/Offer, Breadcrumbs, Organization, WebSite — plus OpenGraph/Twitter. Richer Google results. |
| **Redirect Manager** | `etechflow/module-redirect-manager` | 301/302 redirect manager, auto-redirect on URL-key change, and a 404 catcher with a hit log. |
| **AI SEO** | `etechflow/module-ai-seo` | AI-generated meta titles & descriptions (Anthropic Claude / OpenAI), review grid + product-grid mass action. |
| **Meta Templates** | `etechflow/module-meta-templates` | Rule-based meta title/description/keywords with `{{product.name}} | {{store.name}}` variables — set once, apply across the catalogue. |
| **SEO Audit** | `etechflow/module-seo-audit` | On-demand SEO health **score (0–100)** + issue dashboard: missing/duplicate meta, thin content, orphaned products, 404s, redirect chains — each tagged with the suite module that fixes it. |
| **Canonical & Hreflang** | `etechflow/module-canonical-hreflang` | Category-free canonical URLs, query-param de-duplication, pagination control, and hreflang for multi-store/locale catalogues. |
| **SEO Layered Nav** | `etechflow/module-seo-layered-nav` | Human-readable filter URLs (`?manufacturer=yale`, two-way slug↔id) + canonical/robots/sitemap control for filter pages. |
| **Sitemap** | `etechflow/module-sitemap` | Advanced XML sitemap — products/categories/CMS/custom URLs, image entries, hreflang alternates, exclude rules, index splitting, multi-store, CLI + cron. |

## How they fit together

The **SEO Audit** is the hub: run a scan and every finding links to its fix —

- *Missing / duplicate / poor meta* → **Meta Templates** or **AI SEO**
- *404s & redirect chains* → **Redirect Manager**
- *Missing structured data* → **Rich Snippets**
- *Duplicate filter/category URLs* → **Canonical & Hreflang** + **SEO Layered Nav**
- *Missing / stale XML sitemap* → **Sitemap**

## Install

These modules are distributed from the `etechflow` GitHub organisation. Add the repositories to your project's `composer.json` (or point a Satis/Private Packagist at them), then require the suite:

```json
"repositories": {
    "etechflow-rich-snippets":     { "type": "vcs", "url": "https://github.com/etechflow/Etech-Rich-Snippet-SEO.git" },
    "etechflow-redirect-manager":  { "type": "vcs", "url": "https://github.com/etechflow/Etech-Redirect-Manager.git" },
    "etechflow-ai-seo":            { "type": "vcs", "url": "https://github.com/etechflow/Etech-AI-SEO.git" },
    "etechflow-meta-templates":    { "type": "vcs", "url": "https://github.com/etechflow/Etech-Meta-Templates.git" },
    "etechflow-seo-audit":         { "type": "vcs", "url": "https://github.com/etechflow/Etech-SEO-Audit.git" },
    "etechflow-canonical":         { "type": "vcs", "url": "https://github.com/etechflow/Etech-Canonical-Hreflang.git" },
    "etechflow-layered-nav":       { "type": "vcs", "url": "https://github.com/etechflow/Etech-SEO-Layered-Nav.git" },
    "etechflow-sitemap":           { "type": "vcs", "url": "https://github.com/etechflow/Etech-Sitemap.git" },
    "etechflow-seo-suite":         { "type": "vcs", "url": "https://github.com/etechflow/Etech-SEO-Suite.git" }
}
```

```bash
composer require etechflow/seo-suite:^1.0
bin/magento module:enable \
  Etechflow_RichSnippets Etechflow_RedirectManager Etechflow_AiSeo \
  Etechflow_MetaTemplates Etechflow_SeoAudit Etechflow_CanonicalHreflang \
  ETechFlow_SeoLayeredNav Etechflow_Sitemap
bin/magento setup:upgrade
bin/magento setup:di:compile           # production mode
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

Every module ships **disabled / flag-gated OFF** by default — nothing changes on the storefront until you opt in under **Stores → Configuration → Etechflow**. Enable per module and configure as needed.

## Compatibility

Magento Open Source / Adobe Commerce **2.4.x** · PHP **≥ 8.1** · Hyvä + Luma. Designed to coexist with an existing SEO extension (e.g. Mirasvit) — adopt module by module.

## License

Proprietary — © eTechFlow. Each module is independently licensed; the suite is the convenience bundle.

## Bundled module source

This repository also includes the full source of all 8 modules under `modules/` for reference and monorepo installs:

- `modules/Etechflow/SeoAudit` — on-demand SEO health audit + 0-100 score dashboard
- `modules/Etechflow/CanonicalHreflang` — canonical URLs + hreflang
- `modules/Etechflow/MetaTemplates` — rule-based meta title/description/keywords
- `modules/Etechflow/AiSeo` — AI-generated meta (Anthropic/OpenAI)
- `modules/Etechflow/RichSnippets` — Hyvä-native @graph JSON-LD structured data
- `modules/Etechflow/RedirectManager` — 301/302 redirects + 404 catcher
- `modules/Etechflow/SeoLayeredNav` — SEO-friendly layered-navigation filter URLs (namespace `ETechFlow\SeoLayeredNav`)
- `modules/Etechflow/Sitemap` — advanced XML sitemap generator (images, hreflang, excludes, index splitting, CLI + cron)

The `composer.json` above is the metapackage definition (pulls the 8 published packages); the `modules/` tree is the bundled source for those same modules.
