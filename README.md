## Silverstripe HTMLEditor HugeRTE

[![CI](https://github.com/GuySartorelli/silverstripe-htmleditor-hugerte/actions/workflows/ci.yml/badge.svg)](https://github.com/GuySartorelli/silverstripe-htmleditor-hugerte/actions/workflows/ci.yml)

## Overview

Provides a HugeRTE HTML editor for Silverstripe CMS.

## Installation

```sh
composer require guysartorelli/silverstripe-htmleditor-hugerte
```

## Documentation

> [!WARNING]
> If you want to use any third-party TinyMCE plugins, you'll need to update any reference of "tinymce" to "hugerte" in that plugin's code.

### For this module

Theoretically this is just a copy of `silverstripe/htmleditor-tinymce` but with every instance of "tinymce" renamed for "hugerte" and pulling in the [hugerte NPM package](https://www.npmjs.com/package/hugerte) instead of the tinymce one. Basically all of the TinyMCE docs should apply.

See [docs.silverstripe.org](https://docs.silverstripe.org/en/optional_features/htmleditor-tinymce/) for usage of this module, but replace any "tinymce" with "hugerte" (with appropriate casing) and update the namespaces as appropriate from `SilverStripe\TinyMCE` to `GuySartorelli\HugeRTE`.

If something isn't working that those docs say should work, open a GitHub issue in this repository.

### For HugeRTE itself

Go to https://github.com/hugerte/hugerte-docs for HugeRTE docs.

Note that their advice is basically the same as my advice for this module's docs - use the tinymce docs, and replace "tinymce" with "hugerte" when implementing.
