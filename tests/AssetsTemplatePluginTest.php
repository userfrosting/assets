<?php

/*
 * UserFrosting Assets (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/assets
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Jordan Mele
 * @license   https://github.com/userfrosting/assets/blob/master/LICENSE.md (MIT License)
 */

use PHPUnit\Framework\TestCase;
use UserFrosting\Assets\AssetBundles\GulpBundleAssetsRawBundles;
use UserFrosting\Assets\Assets;
use UserFrosting\Assets\AssetsTemplatePlugin;
use UserFrosting\UniformResourceLocator\ResourceLocator;

/**
 * Tests AssetsForTemplates class.
 *
 * @todo Test serveAsset, will require Slim::mock (apparently), and IS possible. Just difficult to set up.
 */
class AssetsTemplatePluginTest extends TestCase
{
    protected $basePath;

    protected $baseUrl;

    protected $locatorScheme;

    protected $locator;

    /**
     * Initializes test environment.
     *
     * @return void
     */
    public function setUp()
    {
        $this->basePath = __DIR__.'/data';
        $this->baseUrl = 'https://assets.userfrosting.com/assets/';
        $this->locatorScheme = 'assets';
        $this->locator = new ResourceLocator($this->basePath);
        $this->locator->registerStream($this->locatorScheme, '', 'assets');
        $this->locator->registerStream($this->locatorScheme, 'vendor', 'assets', true);
        $this->locator->registerLocation('hawks', 'sprinkles/hawks/');
        $this->locator->registerLocation('owls', 'sprinkles/owls/');
        $this->assets = new Assets($this->locator, $this->locatorScheme, $this->baseUrl);
        $this->assets->addAssetBundles(new GulpBundleAssetsRawBundles(__DIR__.'/data/bundle.config.json'));
    }

    /**
     * Tests Assets constructor.
     * Returns the created Assets instance for use by dependent tests.
     *
     * @return Assets
     */
    public function testConstructAssetsTemplatePlugin()
    {
        $plugin = new AssetsTemplatePlugin($this->assets);
        $this->assertInstanceOf(AssetsTemplatePlugin::class, $plugin);

        return $plugin;
    }

    /**
     * Test JS bundle method.
     *
     * @param AssetsTemplatePlugin $plugin
     *
     * @return void
     *
     * @depends testConstructAssetsTemplatePlugin
     */
    public function testJsBundle(AssetsTemplatePlugin $plugin)
    {
        $this->assertEquals($plugin->js('test'), '<script src="https://assets.userfrosting.com/assets/vendor/bootstrap/js/bootstrap.js"></script><script src="https://assets.userfrosting.com/assets/vendor/bootstrap/js/npm.js"></script>');
    }

    /**
     * Test JS bundle method with attributes.
     *
     * @param AssetsTemplatePlugin $plugin
     *
     * @return void
     *
     * @depends testConstructAssetsTemplatePlugin
     */
    public function testJsBundleWithAttributes(AssetsTemplatePlugin $plugin)
    {
        $this->assertEquals($plugin->js('test', ['async', 'data-test' => 'value']), '<script src="https://assets.userfrosting.com/assets/vendor/bootstrap/js/bootstrap.js" async data-test="value"></script><script src="https://assets.userfrosting.com/assets/vendor/bootstrap/js/npm.js" async data-test="value"></script>');
    }

    /**
     * Test CSS bundle method.
     *
     * @param AssetsTemplatePlugin $plugin
     *
     * @return void
     *
     * @depends testConstructAssetsTemplatePlugin
     */
    public function testCssBundle(AssetsTemplatePlugin $plugin)
    {
        $this->assertEquals($plugin->css('test'), '<link href="https://assets.userfrosting.com/assets/vendor/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" />');
    }

    /**
     * Test CSS bundle method with attributes.
     *
     * @param AssetsTemplatePlugin $plugin
     *
     * @return void
     *
     * @depends testConstructAssetsTemplatePlugin
     */
    public function testCssBundleWithAttributes(AssetsTemplatePlugin $plugin)
    {
        $this->assertEquals($plugin->css('test', ['want-async', 'data-test' => 'value']), '<link href="https://assets.userfrosting.com/assets/vendor/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" want-async data-test="value" />');
    }

    /**
     * Test url method.
     *
     * @param AssetsTemplatePlugin $plugin
     *
     * @return void
     *
     * @depends testConstructAssetsTemplatePlugin
     */
    public function testUrl(AssetsTemplatePlugin $plugin)
    {
        $this->assertEquals($plugin->url('assets://vendor/bootstrap/fonts/glyphicons-halflings-regular.eot'), 'https://assets.userfrosting.com/assets/vendor/bootstrap/fonts/glyphicons-halflings-regular.eot');
    }
}
