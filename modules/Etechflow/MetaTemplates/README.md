# Etechflow_MetaTemplates

Rule-based **meta title / meta description / meta keywords** templates for Magento 2, with `{{variable}}` placeholders resolved per page at render time. Part of the **Etechflow SEO Suite**.

Define a rule once (e.g. `{{product.name}} | Buy at {{store.name}}`) and it applies across every matching product, category, or CMS page — no per-product editing.

## Features

- **Template rules** for `product`, `category`, and `cms_page` pages.
- **`{{object.attribute}}` variables** with optional fallback: `{{product.brand|Genuine Part}}`.
- **Any attribute** is available — `{{product.color}}`, `{{product.<any_code>}}`, dropdown labels resolved automatically.
- **Scoping**: per store view + optional "limit to category ID" for product rules.
- **Priority**: highest-priority matching rule wins.
- **Two modes**: *Fill empty* (never overwrites meta a product/category already has) or *Override* (always applies).
- **Title-safe**: applies the title via an `afterGet` plugin so it wins even when another extension also sets it.
- Admin grid + add/edit form under **Marketing → Meta Templates**. Master switch + mode under **Stores → Configuration → Etechflow → Meta Templates**.

## Variables

| Object | Variables |
|--------|-----------|
| Product | `{{product.name}}`, `{{product.sku}}`, `{{product.price}}`, `{{product.category}}` (deepest), `{{product.<attribute_code>}}` |
| Category | `{{category.name}}`, `{{category.description}}`, `{{category.<attribute_code>}}` |
| CMS | `{{cms.title}}` |
| Store | `{{store.name}}`, `{{store.url}}` |

Fallback syntax: `{{variable|fallback text}}` — used when the variable resolves to empty.

## Install

```bash
composer require etechflow/module-meta-templates
bin/magento module:enable Etechflow_MetaTemplates
bin/magento setup:upgrade
bin/magento setup:di:compile      # production mode
```

## Configure

1. **Stores → Configuration → Etechflow → Meta Templates** — set *Enable* = Yes and choose *Apply mode*.
2. **Marketing → Meta Templates** — *Add New Template*: name, applies-to, store view, optional category limit, priority, and the meta title / description / keywords templates.

## How it works

- A frontend plugin on `Magento\Framework\View\Result\Page::renderResult` resolves the matching rule for the current page and sets the description + keywords via `PageConfig`.
- A second plugin on `Magento\Framework\View\Page\Title::get` returns the templated title at read time, so it is authoritative.
- `Service/VariableProcessor` performs the `{{...}}` substitution; `Service/MetaResolver` picks the matching rule (type + store + category + priority) and honours the fill-empty / override mode.

## License

Proprietary — © eTechFlow.
