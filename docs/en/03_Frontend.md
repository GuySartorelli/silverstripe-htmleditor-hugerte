---
title: Using HugeRTE on the front-end
---

# Using HugeRTE on the front-end

> [!WARNING]
> The plugins that come with `guysartorelli/silverstripe-htmleditor-hugerte` (e.g. for inserting links) are not supported for use on the front-end.

The following JavaScript code will initialise HugeRTE for every [`HTMLEditorField`](api:SilverStripe\Forms\HTMLEditor\HTMLEditorField) on the page which uses the [`HugeRTEConfig`](api:GuySartorelli\HugeRTE\HugeRTEConfig).

```js
const selector = 'textarea[data-editor="hugeRTE"]';
// eslint-disable-next-line no-restricted-syntax
for (const field of document.querySelectorAll(selector)) {
  const id = field.getAttribute('id');
  const config = JSON.parse(field.dataset.config);
  config.height = config.row_height ? config.row_height : undefined;
  config.selector = `textarea#${id}`;
  if (typeof config.baseURL !== 'undefined') {
    hugerte.EditorManager.baseURL = config.baseURL;
  }
  config.skin = config.skin || 'silverstripe';
  hugerte.init(config);
}
```

Note that the `hugerte` variable should already be available as it is included by the Requirements API when rendering the `HTMLEditorField` with the `$Field` template variable.
