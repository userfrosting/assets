<?php

use PHPUnit\Framework\TestCase;
use RocketTheme\Toolbox\ResourceLocator\UniformResourceLocator;
use UserFrosting\Assets\AssetBundle;
use UserFrosting\Assets\Asset;

class AssetBundleTest extends TestCase
{
    protected $basePath = __DIR__ . '/data';
    protected $baseUrl = 'http://example.com/assets-raw';
    protected $cssAsset;
    protected $jsAsset;
    protected $locator;

    public function setUp()
    {
        // Arrange
        $this->locator = new UniformResourceLocator($this->basePath);
        $this->locator->addPath('assets', '', [
            'owls/assets',
            'hawks/assets'
        ]);
        
        $this->bundle = new AssetBundle($this->locator, $this->baseUrl);
        $this->cssAsset = new Asset('vendor/bootstrap-3.3.6/css/bootstrap.css');
        $this->jsAsset = new Asset('vendor/bootstrap-3.3.6/js/bootstrap.js');

        $this->bundle->addCssAsset($this->cssAsset);
        $this->bundle->addJavascriptAsset($this->jsAsset);
    }

    public function testGetUrl()
    {
        $path = $this->cssAsset->getPath();
        
        $url = $this->bundle->getAssetUrl($path);
        
        $this->assertEquals('http://example.com/assets-raw/owls/assets/vendor/bootstrap-3.3.6/css/bootstrap.css', $url);

        $url = $this->bundle->getAssetUrl('/fake/path/file.css');
        
        $this->assertEquals('', $url);
    }
    
    public function testRenderStyle()
    {
        $tag = $this->bundle->renderStyle($this->cssAsset);

        // Assert
        $this->assertEquals('<link rel="stylesheet" type="text/css" href="http://example.com/assets-raw/owls/assets/vendor/bootstrap-3.3.6/css/bootstrap.css" >', $tag);
    }
    
    public function testRenderScript()
    {
        $tag = $this->bundle->renderScript($this->jsAsset);

        // Assert
        $this->assertEquals('<script src="http://example.com/assets-raw/owls/assets/vendor/bootstrap-3.3.6/js/bootstrap.js" ></script>', $tag);
    }
}
