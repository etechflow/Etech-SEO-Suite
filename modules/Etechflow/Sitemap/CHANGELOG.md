# Changelog

All notable changes to `etechflow/module-sitemap` are documented here.

## [1.0.0] - 2026-06-11

### Added
- Initial release.
- XML sitemap generation for products, categories, CMS pages and custom URLs.
- Per-type, per-store-view `changefreq` and `priority`.
- Product `<image:image>` entries (gated by the module toggle and Magento's native product-image-include setting).
- `hreflang` alternates across the store views of a website.
- Exclude rules by product SKU, category ID and CMS identifier.
- Sitemap-index splitting at the 50,000-URL protocol limit.
- Multi-store / multi-website output; default store view writes the canonical `sitemap.xml`.
- On-demand generation via admin **Generate Now** and `bin/magento etechflow:sitemap:generate`.
- Nightly cron generation (opt-in).
