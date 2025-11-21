<?php

namespace SilverStripe\Forms\Tests\HTMLEditor;

use SilverStripe\Control\Director;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Manifest\Module;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\HTMLEditor\HTMLEditorConfig;
use GuySartorelli\HugeRTE\HugeRTECombinedGenerator;
use GuySartorelli\HugeRTE\HugeRTEConfig;
use SilverStripe\View\SSViewer;

class HugeRTECombinedGeneratorTest extends SapphireTest
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set custom base_path for hugerte
        Director::config()->set('alternate_base_folder', __DIR__ . '/HugeRTECombinedGeneratorTest');
        Director::config()->set('alternate_base_url', 'http://www.mysite.com/basedir/');
        Director::config()->set('alternate_public_dir', ''); // Disable public dir
        SSViewer::config()->set('themes', [SSViewer::DEFAULT_THEME]);
        HugeRTEConfig::config()
            ->set('base_dir', 'hugerte')
            ->set('editor_css', [ 'mycode/editor.css' ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Flush test configs
        HTMLEditorConfig::set_config('testconfig', null);
    }

    public function testConfig()
    {
        $module = new Module(Director::baseFolder() . '/mycode', Director::baseFolder());
        // Disable nonces
        $c = new HugeRTEConfig();
        $c->setTheme('testtheme');
        $c->setOption('language', 'en');
        $c->disablePlugins('table', 'emoticons', 'paste', 'code', 'link', 'importcss', 'lists');
        $c->enablePlugins(
            [
                'plugin1' => 'mycode/plugin1.js', //
                'plugin2' => '/anotherbase/mycode/plugin2.js',
                'plugin3' => 'https://www.google.com/mycode/plugin3.js',
                'plugin4' => null,
                'plugin5' => null,
                'plugin6' => '/basedir/mycode/plugin6.js',
                'plugin7' => '/basedir/mycode/plugin7.js',
                'plugin8' => $module->getResource('plugin8.js'),
            ]
        );
        HTMLEditorConfig::set_config('testconfig', $c);

        // Get config for this
        /** @var HugeRTECombinedGenerator $generator */
        $generator = Injector::inst()->create(HugeRTECombinedGenerator::class);
        $this->assertMatchesRegularExpression(
            '#_hugerte/hugerte-testconfig-[0-9a-z]{10,10}#',
            $generator->generateFilename($c)
        );
        $content = $generator->generateContent($c);
        $this->assertStringContainsString(
            "var baseURL = baseTag.length ? baseTag[0].baseURI : 'http://www.mysite.com/basedir';\n",
            $content
        );
        // Main script file
        $this->assertStringContainsString("/* hugerte.js */\n", $content);
        // Locale file
        $this->assertStringContainsString("/* en.js */\n", $content);
        // Local plugins
        $this->assertStringContainsString("/* plugin1.js */\n", $content);
        $this->assertStringContainsString("/* plugin4.min.js */\n", $content);
        $this->assertStringContainsString("/* plugin4/langs/en.js */\n", $content);
        $this->assertStringContainsString("/* plugin5.js */\n", $content);
        $this->assertStringContainsString("/* plugin6.js */\n", $content);
        // module-resource plugin
        $this->assertStringContainsString("/* plugin8.js */\n", $content);
        // Exclude non-local plugins
        $this->assertStringNotContainsString('plugin2.js', $content);
        $this->assertStringNotContainsString('plugin3.js', $content);
        // Exclude missing file
        $this->assertStringNotContainsString('plugin7.js', $content);

        // Check themes
        $this->assertStringContainsString("/* theme.js */\n", $content);
        $this->assertStringContainsString("/* testtheme/langs/en.js */\n", $content);

        // Check plugin links included
        $this->assertStringContainsString(
            // phpcs:disable Generic.Files.LineLength.TooLong
            <<<EOS
hugerte.each('hugerte/langs/en.js,mycode/plugin1.js,hugerte/plugins/plugin4/plugin.min.js,hugerte/plugins/plugin4/langs/en.js,hugerte/plugins/plugin5/plugin.js,mycode/plugin6.js,_resources/mycode/plugin8.js?m=
EOS
            ,
            // phpcs:enable Generic.Files.LineLength.TooLong
            $content
        );

        // Check theme links included
        $this->assertStringContainsString(
            // phpcs:disable Generic.Files.LineLength.TooLong
            <<<EOS
hugerte/themes/testtheme/theme.js,hugerte/themes/testtheme/langs/en.js'.split(','),function(f){hugerte.ScriptLoader.markDone(baseURL+f);});
EOS
            ,
            // phpcs:enable Generic.Files.LineLength.TooLong
            $content
        );
    }

    public function testFlush()
    {
        // Disable nonces
        $c = new HugeRTEConfig();
        $c->setTheme('testtheme');
        $c->setOption('language', 'en');
        $c->disablePlugins('table', 'emoticons', 'paste', 'code', 'link', 'importcss', 'lists');
        $c->enablePlugins(['plugin1' => 'mycode/plugin1.js']);
        HTMLEditorConfig::set_config('testconfig', $c);

        // Generate file for this
        /** @var HugeRTECombinedGenerator $generator */
        $generator = Injector::inst()->create(HugeRTECombinedGenerator::class);
        $generator->getScriptURL($c);
        $filename = $generator->generateFilename($c);

        // Ensure content exists
        $this->assertNotEmpty($generator->getAssetHandler()->getContent($filename));

        // Flush should destroy this
        HugeRTECombinedGenerator::flush();
        $this->assertEmpty($generator->getAssetHandler()->getContent($filename));
    }
}
