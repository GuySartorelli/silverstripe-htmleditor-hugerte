<?php

use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\Forms\HTMLEditor\HTMLEditorConfig;
use GuySartorelli\HugeRTE\HugeRTEConfig;

// Avoid creating global variables
call_user_func(function () {
    $editorConfig = HTMLEditorConfig::get('cms');

    if (!$editorConfig instanceof HugeRTEConfig) {
        return;
    }

    $editorConfig->setOptions([
        'friendly_name' => 'Default CMS',
        'priority' => '50',
        'skin' => 'silverstripe',
        'contextmenu' => "searchreplace | sslink anchor ssmedia ssembed inserttable | cell row column deletetable",
        'use_native_selects' => false,
    ]);
    $editorConfig->insertButtonsAfter('table', 'anchor');

    // Prepare list of plugins to enable
    $moduleManifest = ModuleLoader::inst()->getManifest();
    $module = $moduleManifest->getModule('guysartorelli/silverstripe-htmleditor-hugerte');
    $plugins = [];

    // Add link plugins if silverstripe/admin is installed.
    // The JS in these relies on some of the admin code e.g. modals.
    if ($moduleManifest->moduleExists('silverstripe/admin')) {
        $plugins += [
            'sslink' => $module->getResource('client/dist/js/HugeRTE_sslink.js'),
            'sslinkexternal' => $module->getResource('client/dist/js/HugeRTE_sslink-external.js'),
            'sslinkemail' => $module->getResource('client/dist/js/HugeRTE_sslink-email.js'),
            'sslinkphone' => $module->getResource('client/dist/js/HugeRTE_sslink-phone.js'),
        ];
        // Move anchor button to be after the link button
        $editorConfig->removeButtons('anchor');
        $editorConfig->insertButtonsAfter('sslink', 'anchor');
    }

    // Add plugins for managing assets if silverstripe/asset-admin is installed
    if ($moduleManifest->moduleExists('silverstripe/asset-admin')) {
        $plugins += [
            'ssmedia' => $module->getResource('client/dist/js/HugeRTE_ssmedia.js'),
            'ssembed' => $module->getResource('client/dist/js/HugeRTE_ssembed.js'),
            'sslinkfile' => $module->getResource('client/dist/js/HugeRTE_sslink-file.js'),
        ];
        $editorConfig->insertButtonsAfter('table', 'ssmedia');
        $editorConfig->insertButtonsAfter('ssmedia', 'ssembed');
    }

    // Add internal link plugins if silverstripe/cms is installed
    if ($moduleManifest->moduleExists('silverstripe/cms')) {
        $plugins += [
            'sslinkinternal' => $module->getResource('client/dist/js/HugeRTE_sslink-internal.js'),
            'sslinkanchor' => $module->getResource('client/dist/js/HugeRTE_sslink-anchor.js'),
        ];
    }

    if (!empty($plugins)) {
        $editorConfig->enablePlugins($plugins);
    }
});
