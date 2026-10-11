# jdz/template

Template rendering infrastructure with Twig support — the rendering layer extracted from the jizy-framework: page template classes that assemble the view data and body classes, a Twig renderer with layout fallbacks, and post-render extensions.

## Installation

```bash
composer require jdz/template
```

## Requirements

- PHP 8.2 or higher
- jdz/data ^2.0
- twig/twig ^3.0

## Usage

```php
use JDZ\Template\Template;
use JDZ\Template\TwigRenderer;
use JDZ\Template\Extension\MergeAttributesTwigExtension;

class HomeTemplate extends Template
{
    protected function loadData(): void
    {
        $this->data['title'] = 'Welcome Home';
        $this->data['typeClass'] = ['type-page', 'type-home'];
    }

    protected function loadBodyClass(): void
    {
        $this->bodyClasses[] = 'page-home';
    }
}

$template = (new HomeTemplate())->setTheme('dark')->load();
// $template->getData()['bodyclass'] === 'theme-dark type-page type-home page-home'

$renderer = new TwigRenderer(debug: false);
$renderer->layoutPath = __DIR__ . '/templates';
$renderer->viewLayouts = ['home', 'page'];   // tries views/home.tmpl, then views/page.tmpl
$renderer->data = $template->getData()->all();
$renderer->loadTwig();
$renderer->addTwigExtension(new MergeAttributesTwigExtension());

echo $renderer->render();
```

## Classes

Namespace `JDZ\Template\`.

| Class | Description |
|-------|-------------|
| `Template` | Abstract page template (implements `Contract\TemplateInterface`). Override `loadData()` and `loadBodyClass()`; `load()` merges the `typeClass` entries and `theme-<theme>` into the `bodyclass` data key. The name defaults to the class short name (lcfirst), and the theme to the name unless it is `main`. |
| `TemplateData` | The template's data bag: `JDZ\Utils\Data` with `ArrayAccess`, `append()`, `pushToArray()`, `addTypeClass()`. |
| `ViewData` | `JDZ\Utils\Data` for a view, with `addJsTranslation()` / `addJsTranslations()` (stored under `i18n`). |
| `MetasData` | Page metadata bag (title, description, Open Graph…): `set()`, `sets()`, `append()`, `get()`, `def()`, `has()`, `erase()`, `all()`. |
| `TwigRenderer` | `new TwigRenderer(bool $debug = false, ?string $cacheDir = null)`; set `layoutPath`, `viewLayouts`, `data` (and optionally `timezone`), then `loadTwig()`, `addTwigExtension()`, `render()`. Views are resolved as `<layoutPath>/views/<layout>.tmpl`, first match wins. |
| `Extension\MergeAttributesTwigExtension` | Twig function `mergeHtmlAttributes(attrs)` — renders an attribute map as ` key="value"` pairs (array `class` values are joined); every value is HTML-escaped (`ENT_QUOTES`, without re-encoding entities already in it). |
| `Extension\RendererExtension` | Post-render hook (`Contract\RendererExtensionInterface::render(string $body): string`); the base class returns the body unchanged. |
| `Extension\NoFollowLinksRendererExtension` | Post-render pass that adds `target="_blank"` and `rel="nofollow"` to `mailto:` and `tel:` links. |

## Examples

Runnable samples in `examples/`:

- `01-basic-template.php` — a concrete template, body classes and themes
- `02-metas-data.php` — `MetasData` for page metadata
- `03-template-view-data.php` — `TemplateData` and `ViewData`
- `04-twig-renderer.php` — `TwigRenderer`, layout fallbacks and a Twig extension (uses `examples/templates/views/page.tmpl`)
- `05-renderer-extensions.php` — post-processing the rendered HTML

## Testing

```bash
composer test
```

## Changelog

- **1.0.3** — `mergeHtmlAttributes()` HTML-escapes every attribute value (a `"` used to become `\"`, which HTML does not read: the attribute ended there); entities already in a value are kept.
- **1.0.2** — `TemplateData::pushToArray()` without a key appends the value (it was dropped).

## License

MIT — see the LICENSE file.
