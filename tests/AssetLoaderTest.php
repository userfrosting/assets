<?php

use PHPUnit\Framework\TestCase;
use RocketTheme\Toolbox\ResourceLocator\UniformResourceLocator;
use UserFrosting\Assets\Asset;
use UserFrosting\Assets\AssetBundle;
use UserFrosting\Assets\AssetLoader;

class AssetLoaderTest extends TestCase
{
    protected $basePath = __DIR__ . '/data';
    protected $baseUrl = 'http://example.com/assets-raw';
    protected $loader;
    protected $locator;

    public function setUp()
    {
        // Arrange
        $this->locator = new UniformResourceLocator($this->basePath);
        $this->locator->addPath('assets', '', [
            'owls/assets',
            'hawks/assets'
        ]);
        
        $this->loader = new AssetLoader($this->basePath, "/^[A-Za-z0-9_\-]+\/assets\//");
        
    }

    public function testFindSuccess()
    {
        $result = $this->loader->loadAsset('owls/assets/vendor/bootstrap-3.3.6/css/bootstrap.css');
        
        $this->assertEquals(true, $result);
    }
    
    public function testFindFailure()
    {        
        $result = $this->loader->loadAsset('owls/assets/vendor/foundation/foundation.css');
        $this->assertEquals(false, $result);
        
        $result = $this->loader->loadAsset('hawks/forbidden.txt');
        $this->assertEquals(false, $result);

        $result = $this->loader->loadAsset('hawks/assets/../forbidden.txt');
        $this->assertEquals(false, $result);
    }

}
