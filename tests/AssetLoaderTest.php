<?php

use PHPUnit\Framework\TestCase;
use UserFrosting\Assets\AssetLoader;

class AssetLoaderTest extends TestCase
{
    protected $basePath = __DIR__ . '/data';
    protected $loader;

    public function setUp()
    {
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
