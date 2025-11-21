---
title: WYSIWYG Styles
summary: Add custom CSS properties to the WYSIWYG
icon: text-width
---

# WYSIWYG styles

HugeRTE lets you customise the style of content in the editor. This is done by setting the [`HugeRTEConfig.editor_css`](api:GuySartorelli\HugeRTE\HugeRTEConfig->editor_css) configuration property to the path of the CSS file that should be used.

```yml
---
name: hugerte-css
---
GuySartorelli\HugeRTE\HugeRTEConfig:
  editor_css:
    - 'app/css/editor.css'
```

Alternatively, you can set this on a specific `HugeRTEConfig` instance via the [`setContentCSS()`](api:GuySartorelli\HugeRTE\HugeRTEConfig::setContentCSS()) method.

```php
use GuySartorelli\HugeRTE\HugeRTEConfig;
$config = HugeRTEConfig::get('my-editor');
$config->setContentCSS(['/app/client/css/editor.css']);
```

> [!WARNING]
> `guysartorelli/silverstripe-htmleditor-hugerte` adds a small CSS file to `editor_css` which highlights broken links - you'll
> probably want to include that in the array you pass to `setContentCSS()`, either by first calling
> `getContentCSS()` and merging that array with your new one (and passing the result to `setContentCSS()`)
> or by adding `'/_resources/vendor/guysartorelli/silverstripe-htmleditor-hugerte/client/dist/styles/editor.css'` to the array you pass
> to `setContentCSS()`

## Custom style dropdown

The custom style dropdown can be enabled via the `importcss` plugin. ([Doc](https://www.tiny.cloud/docs/tinymce/6/importcss/))
Use the below code in `app/_config.php`:

```php
use GuySartorelli\HugeRTE\HugeRTEConfig;

HugeRTEConfig::get('my-editor')
    ->addButtonsToLine(1, 'styles')
    ->setOption('importcss_append', true);
```

Any CSS classes within this file will be automatically added to the `WYSIWYG` editors 'style' dropdown.
For instance, to
add the color 'red' as an option within the `WYSIWYG` add the following to the `editor.css`

```css
.red {
    color: red;
}
```

Adding a tag to the selector will automatically wrap with this tag. For example:

```css
h4.red {
    color: red;
}
```

will add an `h4` tag to the selected block.

For further customisation, customize the `style_formats` option.
`style_formats` won't be applied if you do not enable `importcss_append`.
Here is a working example to get you started.  
See related [HugeRTE doc](https://www.tiny.cloud/docs/tinymce/6/user-formatting-options/#style_formats).

```php
use GuySartorelli\HugeRTE\HugeRTEConfig;

$formats = [
    [
        'title' => 'Headings',
        'items' => [
            ['title' => 'Heading 1', 'block' => 'h1' ],
            ['title' => 'Heading 2', 'block' => 'h2' ],
            ['title' => 'Heading 3', 'block' => 'h3' ],
            ['title' => 'Heading 4', 'block' => 'h4' ],
            ['title' => 'Heading 5', 'block' => 'h5' ],
            ['title' => 'Heading 6', 'block' => 'h6' ],
            [
                'title' => 'Subtitle',
                'selector' => 'p',
                'classes' => 'title-sub',
            ],
        ],
    ],
    [
        'title' => 'Misc Styles',
        'items' => [
            [
                'title' => 'Style 1',
                'selector' => 'ul',
                'classes' => 'style1',
                'wrapper' => true,
                'merge_siblings' => false,
            ],
            [
                'title' => 'Button red',
                'inline' => 'span',
                'classes' => 'btn-red',
                'merge_siblings' => true,
            ],
        ],
    ],
];

HugeRTEConfig::get('cms')
    ->addButtonsToLine(1, 'styles')
    ->setOptions([
        'importcss_append' => true,
        'style_formats' => $formats,
    ]);
```

## API documentation

- [`HtmlEditorConfig`](api:SilverStripe\Forms\HTMLEditor\HtmlEditorConfig)
- [`HugeRTEConfig`](api:GuySartorelli\HugeRTE\HugeRTEConfig)
