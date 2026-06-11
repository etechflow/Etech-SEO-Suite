# Etechflow Sitemap

Advanced XML sitemap generator for Magento 2 — part of the [Etechflow SEO Suite](https://github.com/etechflow/Etech-SEO-Suite).

It reuses Magento's own, battle-tested sitemap resource models for product/category/CMS URL collection (so URL rewrites, visibility and per-store status behave exactly as core does) and adds the capabilities a standard install lacks:

- **Products, categories, CMS pages and custom URLs** in one sitemap.
- **Per-type `changefreq` and `priority`**, configurable per store view.
- **Product image entries** (`<image:image>`).
- **hreflang alternates** (`<xhtml:link rel="alternate">`) across the store views of a website — for multi-language stores.
- **Exclude rules** by product SKU, category ID or CMS identifier.
- **Additional custom URLs** (`path | priority | changefreq`).
- **Sitemap-index splitting** — automatically splits past 50,000 URLs/file and writes a sitemap index.
- **Multi-store / multi-website** — each store view gets its own sitemap; the default store view writes the canonical `sitemap.xml`.
- **Generate on demand** (admin button + `bin/magento etechflow:sitemap:generate`) or **nightly via cron**.

## Installation

```bash
composer require etechflow/module-sitemap
bin/magento module:enable Etechflow_Sitemap
bin/magento setup:upgrade
bin/magento setup:di:compile      # production mode only
```

## Configuration

**Stores → Configuration → Etechflow → Sitemap**

| Group | What it controls |
|-------|------------------|
| General | Master enable, output path (under `pub/`), index filename, max URLs per file |
| Products / Categories / CMS Pages | Include toggle, change frequency, priority (Products also: add images) |
| Additional URLs | Hand-added URLs, one per line: `path` or `path\|priority\|changefreq` |
| Exclusions | SKUs / category IDs / CMS identifiers to omit |
| Hreflang | Emit cross-store-view `hreflang` alternates |
| Scheduled Generation | Rebuild nightly at 03:00 via cron |

### Generating

- **Admin:** Marketing → SEO & Search → Etechflow Sitemap → **Generate Now**.
- **CLI:** `bin/magento etechflow:sitemap:generate`
- **Cron:** enable *Scheduled Generation*; the `etechflow_sitemap_generate` job runs nightly.

Files are written under the web root (`pub/`), so the default output is reachable at `https://your-domain/sitemap.xml`.

### Notes

- **Product images** require Magento's native *Stores → Configuration → Catalog → XML Sitemap → Product Options → Add Images Into Sitemap* to be set to **Base** or **All** — that is what populates the image data this module emits. The module's own *Add product images* toggle then controls whether those entries are written.
- Add the sitemap to `robots.txt` (`Sitemap: https://your-domain/sitemap.xml`) and submit it in Google Search Console.

## License

Proprietary — © Etechflow. See `LICENSE.txt`.
