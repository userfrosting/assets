<?php

use PHPUnit\Framework\TestCase;
use UserFrosting\UniformResourceLocator\ResourceLocator;
use UserFrosting\Assets\Assets;
use UserFrosting\Assets\AssetBundles\GulpBundleAssetsRawBundles;

/**
 * Tests Assets class.
 *
 * @todo Test serveAsset, will require Slim::mock (apparently), and IS possible. Just difficult to set up.
 */
class AssetsTest extends TestCase
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
        $this->basePath = __DIR__ . '/data';
        $this->baseUrl = "https://assets.userfrosting.com/";
        $this->locatorScheme = "assets";
        $this->locator = new ResourceLocator($this->basePath);
        $this->locator->registerStream($this->locatorScheme, '', 'assets');
        $this->locator->registerStream($this->locatorScheme, 'vendor', 'assets', true);
        $this->locator->registerLocation('hawks', 'sprinkles/hawks/');
        $this->locator->registerLocation('owls', 'sprinkles/owls/');
    }

    /**
     * Tests Assets constructor.
     * Returns the created Assets instance for use by dependent tests.
     *
     * @return Assets
     */
    public function testConstructAssets()
    {
        $assets = new Assets($this->locator, $this->locatorScheme, $this->baseUrl);
        $this->assertInstanceOf(Assets::class, $assets);
        return $assets;
    }

    /**
     * Test string parameter for getAbsoluteUrl
     *
     * @param Assets $assets
     * @return void
     *
     * @depends testConstructAssets
     */
    public function testGetAbsoluteUrlWithString(Assets $assets)
    {
        $url = $assets->getAbsoluteUrl('assets://vendor/bootstrap/js/bootstrap.js');
        $this->assertEquals($this->baseUrl . 'assets/vendor/bootstrap/js/bootstrap.js', $url);

        // Translate it back
        $this->assertEquals('assets://vendor/bootstrap/js/bootstrap.js', $assets->urlPathToStreamUri($url));
        $this->assertEquals(__DIR__ . '/data/assets/bootstrap/js/bootstrap.js', $assets->urlPathToAbsolutePath($url));
    }

    /**
     * Test string[] parameter for getAbsoluteUrl
     *
     * @param Assets $assets
     * @return void
     *
     * @depends testConstructAssets
     */
    public function testGetAbsoluteUrlWithStringArray(Assets $assets)
    {
        $this->assertEquals($assets->getAbsoluteUrl([
            'assets',
            'vendor/bootstrap/js/bootstrap.js'
        ]), $this->baseUrl . 'assets/vendor/bootstrap/js/bootstrap.js');
    }

    /**
     * Test non-existent asset getAbsoluteUrl
     *
     * @param Assets $assets
     * @return void
     *
     * @depends testConstructAssets
     */
    public function testGetAbsoluteUrlWithNonExistentFile(Assets $assets)
    {
        $this->expectException(UserFrosting\Support\Exception\FileNotFoundException::class);
        $assets->getAbsoluteUrl('assets://vendor/bootstrap/js/faker.js');
    }

    /**
     * Tests addition of bundles to Assets instance.
     *
     * @param Assets $assets
     * @return Assets
     *
     * @depends testConstructAssets
     */
    public function testAddAssetBundles(Assets $assets)
    {
        $assets->addAssetBundles(new GulpBundleAssetsRawBundles(__DIR__ . "/data/bundle.config.json"));
        $this->assertInternalType('array', $assets->getAssetBundles());
        return $assets;
    }

    /**
     * Tests getJsBundleAssets
     *
     * @param Assets $assets
     * @return void
     *
     * @depends testAddAssetBundles
     */
    public function testGetJsBundleAssets(Assets $assets)
    {
        $this->assertEquals($assets->getJsBundleAssets("test"), [
            $this->baseUrl . 'assets/vendor/bootstrap/js/bootstrap.js',
            $this->baseUrl . 'assets/vendor/bootstrap/js/npm.js'
        ]);
    }

    /**
     * Tests getCssBundleAssets
     *
     * @param Assets $assets
     * @return void
     *
     * @depends testAddAssetBundles
     */
    public function testGetCssBundleAssets(Assets $assets)
    {
        $this->assertEquals($assets->getCssBundleAssets("test"), [
            $this->baseUrl . 'assets/vendor/bootstrap/css/bootstrap.css'
        ]);
    }
}