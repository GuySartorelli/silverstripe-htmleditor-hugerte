---
title: Configuring HugeRTE
icon: code
---

# Configuring HugeRTE

## Adding and removing capabilities

In its simplest form, HugeRTE configuration includes adding and removing buttons and plugins.

You can add plugins to the editor using the [`HugeRTEConfig::enablePlugins()`](api:GuySartorelli\HugeRTE\HugeRTEConfig::enablePlugins()) method. This will
transparently generate the relevant underlying HugeRTE code.

> [!TIP]
> We've done an explicit `instanceof` check here for correctness, but in reality unless your project also uses an alternative WYSIWYG editor, you can safely omit that check. The remaining examples in this documentation will omit the check.

```php
// app/_config.php
use SilverStripe\Forms\HTMLEditor\HTMLEditorConfig;
use GuySartorelli\HugeRTE\HugeRTEConfig;

$editorConfig = HTMLEditorConfig::get('cms');
if ($editorConfig instanceof HugeRTEConfig) {
    $editorConfig->enablePlugins('emoticons');
}
```

> [!NOTE]
> This utilities the HugeRTE's [`external_plugins`](https://www.tiny.cloud/docs/tinymce/6/editor-important-options/#external_plugins)
> option under the hood.

Plugins and advanced themes can provide additional buttons that can be added (or removed) through the
configuration. Here is an example of adding a `ssmacron` button after the `charmap` button:

```php
// app/_config.php
use GuySartorelli\HugeRTE\HugeRTEConfig;

HugeRTEConfig::get('cms')->insertButtonsAfter('charmap', 'ssmacron');
```

Buttons can also be removed:

```php
// app/_config.php
use GuySartorelli\HugeRTE\HugeRTEConfig;

HugeRTEConfig::get('cms')->removeButtons('tablecontrols', 'blockquote', 'hr');
```

> [!WARNING]
> Internally `HugeRTEConfig` uses the HugeRTE's `toolbar` option to configure these. See the
> [HugeRTE documentation of this option](https://www.tiny.cloud/docs/tinymce/6/toolbar-configuration-options/#toolbar)
> for more details.

## Enabling premium plugins

@TODO is this even a thing?

## Enabling custom plugins

It is also possible to add custom plugins to HugeRTE, for example toolbar buttons.
You can enable them through [`HugeRTEConfig::enablePlugins()`](api:GuySartorelli\HugeRTE\HugeRTEConfig::enablePlugins()):

```php
// app/_config.php
use GuySartorelli\HugeRTE\HugeRTEConfig;

HugeRTEConfig::get('cms')->enablePlugins(['myplugin' => 'app/javascript/myplugin/editor_plugin.js']);
```

> [!TIP]
> The path for the plugin file must be one of the following:
>
> - `null` (if the plugin being enabled is a built-in plugin)
> - a path, relative to your `_resources/` directory, to the plugin file
> - a `ModuleResource` instance representing the plugin JavaScript file (see `guysartorelli/silverstripe-htmleditor-hugerte`'s `_config.php` file for examples)
> - an absolute URL (e.g. for a third-party plugin to be fetched from a CDN).

You can learn how to [create a plugin](https://www.tiny.cloud/docs/tinymce/6/creating-a-plugin/) from the HugeRTE documentation.

## Setting options

HugeRTE behaviour can be affected through its [configuration options](https://www.tiny.cloud/docs/tinymce/6/basic-setup).
These options will be passed straight to the editor.

> [!WARNING]
> While you can define `valid_elements` and `extended_valid_elements` using this API and it will be respected, the generalised API described in [defining HTML editor configurations](https://docs.silverstripe.org/en/developer_guides/forms/field_types/htmleditorfield/#defining-html-editor-configurations) should generally be preferred instead.

A default set of options has been defined in the [`HugeRTEConfig.default_options`](api:GuySartorelli\HugeRTE\HugeRTEConfig->default_options) configuration property. Updating this configuration property will update the default options which are set for all `HugeRTEConfig` instances.

For example to disable resizing HugeRTE editors:

```yml
GuySartorelli\HugeRTE\HugeRTEConfig:
  default_options:
    resize: false
```

You can also set options for a specific named config, either by setting [`HTMLEditorConfig.default_config_definitions`](api:SilverStripe\Forms\HTMLEditor\HTMLEditorConfig->default_config_definitions) via YAML configuration or by calling [`setOption`](api:SilverStripe\Forms\HTMLEditor\HTMLEditorConfig::setOption()) in your `_config.php` file:

```yml
SilverStripe\Forms\HTMLEditor\HTMLEditorConfig:
  default_config_definitions:
    my-config:
      options:
        resize: false
```

```php
// app/_config.php
use GuySartorelli\HugeRTE\HugeRTEConfig;

HugeRTEConfig::get('my-config')->setOption('resize', false);
```

> [!HINT]
> Note that the `setOption()` method *overrides* any existing value for that option. For options that accept a string or array, if you only want to change some small part
of the existing option value, you can call `getOption()`, modify the returned value, and then pass the result to `setOption()`.

### Image size pre-sets

Silverstripe CMS will suggest pre-set image size in the HTML editor. Content authors can quickly switch between the pre-set size when interacting with images.

The default values are defined in [`HugeRTEConfig.image_size_presets`](api:GuySartorelli\HugeRTE\HugeRTEConfig->image_size_presets). Developers can customise the pre-set sizes by altering their HugeRTEConfig.

You can alter the defaults for all HugeRTE editors with YAML configuration.

```yml
GuySartorelli\HugeRTE\HugeRTEConfig:
  image_size_presets:
    - name: widesize
      i18n: GuySartorelli\HugeRTE\HugeRTEConfig.WIDE_SIZE
      text: Wide size
      width: 900
```

You can edit the image size pre-sets for an individual configuration by calling `setOption()` on a `HugeRTEConfig` instance.

Remember that calling `setOption()` overrides the existing value, so you need to also include the default presets if you want those.

```php
use GuySartorelli\HugeRTE\HugeRTEConfig;

$presets = array_merge(
    [
        [
            'width' => 300,
            'text' => 'Small fit',
            'name' => 'smallfit',
            'default' => true,
        ],
    ],
    HugeRTEConfig::config()->get('image_size_presets')
);
HugeRTEConfig::get('cms')->setOption('image_size_presets', $presets);
```

## Disable oEmbed

The ["oEmbed" standard](https://www.oembed.com/) is implemented by many media services around the web, allowing easy
representation of files just by referencing a website URL. For example, a content author can insert a playable youtube
video just by knowing its URL, as opposed to dealing with manual HTML code.

To disable oEmbed you will need to follow the below to remove the plugin from HugeRTE, as well
as disabling the internal service via YAML:

```php
use GuySartorelli\HugeRTE\HugeRTEConfig;

HugeRTEConfig::get('cms')->disablePlugins('ssembed');
```

```yml
---
Name: oembed-disable
---
SilverStripe\AssetAdmin\Forms\RemoteFileFormFactory:
  enabled: false
```

## Doctypes

Since HugeRTE generates markup, it needs to know which doctype your documents will be rendered in. You can set this
through the [`element_format`](https://www.tiny.cloud/docs/tinymce/6/content-filtering/#element_format) configuration variable.

In case you want to adhere to the stricter xhtml format (for example rendering self closing tags like `<br/>` instead of `<br>`),
use the following configuration:

```php
use GuySartorelli\HugeRTE\HugeRTEConfig;

HugeRTEConfig::get('cms')->setOption('element_format', 'xhtml');
```
