<?php

use PHPUnit\Framework\TestCase;
use RocketTheme\Toolbox\ResourceLocator\UniformResourceLocator;
use UserFrosting\Assets\Asset;
use UserFrosting\Assets\AssetBundle;
use UserFrosting\Assets\UrlBuilder\AssetUrlBuilder;

class AssetBundleTest extends TestCase
{
    protected $assetUrlBuilder;
    protected $cssAsset;
    protected $jsAsset;

    public function setUp()
    {
        // Arrange
        $basePath = __DIR__ . '/data';
        $locator = new UniformResourceLocator($basePath);
        $locator->addPath('assets', '', [
            'owls/assets',
            'hawks/assets'
        ]);

        $baseUrl = 'http://example.com/assets-raw';
        $this->assetUrlBuilder = new AssetUrlBuilder($locator, $baseUrl);

        $this->cssAsset = new Asset('vendor/bootstrap-3.3.6/css/bootstrap.css');
        $this->jsAsset = new Asset('vendor/bootstrap-3.3.6/js/bootstrap.js');
    }



    public function testRenderStyle()
    {
        $bundle = new AssetBundle($this->assetUrlBuilder);

        $bundle->addCssAsset($this->cssAsset);

        $tag = $bundle->renderStyle($this->cssAsset);

        // Assert
        $this->assertEquals('<link rel="stylesheet" type="text/css" href="http://example.com/assets-raw/owls/assets/vendor/bootstrap-3.3.6/css/bootstrap.css" >', $tag);
    }

    public function testRenderStyleOptions() {
        $bundle = new AssetBundle($this->assetUrlBuilder);

        $bundle->addCssAsset($this->cssAsset);

        $tag = $bundle->renderStyle($this->cssAsset, ['id' => 'value']);

        // Assert
        $this->assertEquals('<link rel="stylesheet" type="text/css" href="http://example.com/assets-raw/owls/assets/vendor/bootstrap-3.3.6/css/bootstrap.css" id="value">', $tag);
    }

    public function testRenderScript()
    {
        $bundle = new AssetBundle($this->assetUrlBuilder);

        $bundle->addJavascriptAsset($this->jsAsset);

        $tag = $bundle->renderScript($this->jsAsset);

        // Assert
        $this->assertEquals('<script src="http://example.com/assets-raw/owls/assets/vendor/bootstrap-3.3.6/js/bootstrap.js" ></script>', $tag);
    }

    public function testRenderScriptOptions()
    {
        $bundle = new AssetBundle($this->assetUrlBuilder);

        $bundle->addJavascriptAsset($this->jsAsset);

        $tag = $bundle->renderScript($this->jsAsset, ['defer' => true]);

        // Assert
        $this->assertEquals('<script src="http://example.com/assets-raw/owls/assets/vendor/bootstrap-3.3.6/js/bootstrap.js" defer></script>', $tag);
    }
}
