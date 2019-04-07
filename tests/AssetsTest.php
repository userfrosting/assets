<?php

use PHPUnit\Framework\TestCase;
use UserFrosting\Assets\AssetBundles\GulpBundleAssetsRawBundles;
use UserFrosting\Assets\Assets;
use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\UniformResourceLocator\ResourceLocator;

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
        $this->baseUrl = 'https://assets.userfrosting.com/';
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

        // URL
        $this->assertEquals($this->baseUrl . 'vendor/bootstrap/js/bootstrap.js', $url);

        // Stream URI
        $this->assertEquals('assets://vendor/bootstrap/js/bootstrap.js', $assets->urlPathToStreamUri($url));

        // Absolute path
        $this->assertEquals(realpath(__DIR__ . '/data/assets/bootstrap/js/bootstrap.js'), $assets->urlPathToAbsolutePath($url));
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
        ]), $this->baseUrl . 'vendor/bootstrap/js/bootstrap.js');
    }

    /**
     * Test invalid string[] parameter for getAbsoluteUrl
     *
     * @param Assets $assets
     * @return void
     *
     * @depends testConstructAssets
     */
    public function testGetAbsoluteUrlWithInvalidStringArray(Assets $assets)
    {
        $this->expectException(\BadMethodCallException::class);
        $assets->getAbsoluteUrl([
            'assets',
            'vendor/bootstrap/js/bootstrap.js',
            'extra-invalid-entry'
        ]);
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
        $this->expectException(FileNotFoundException::class);
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
            $this->baseUrl . 'vendor/bootstrap/js/bootstrap.js',
            $this->baseUrl . 'vendor/bootstrap/js/npm.js'
        ]);
    }

    /**
     * Tests getJsBundleAssets with a non-existant bundle index.
     *
     * @param Assets $assets
     * @return void
     *
     * @depends testAddAssetBundles
     */
    public function testGetJsBundleAssetsOutOfRange(Assets $assets)
    {
        $this->expectException(\OutOfRangeException::class);
        $assets->getJsBundleAssets("i-don't-exist");
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
            $this->baseUrl . 'vendor/bootstrap/css/bootstrap.css'
        ]);
    }

    /**
     * Tests getCssBundleAssets with a non-existant bundle index.
     *
     * @param Assets $assets
     * @return void
     *
     * @depends testAddAssetBundles
     */
    public function testGetCssBundleAssetsOutOfRange(Assets $assets)
    {
        $this->expectException(\OutOfRangeException::class);
        $assets->getCssBundleAssets("i-don't-exist");
    }

    /**
     * Tests resetAssetBundles
     *
     * @param Assets $assets
     * @return void
     * 
     * @depends testAddAssetBundles
     */
    public function testResetAssetBundles(Assets $assets)
    {
        $this->assertNotEquals($assets->getAssetBundles(), []);
        $assets->resetAssetBundles();
        $this->assertEquals($assets->getAssetBundles(), []);
        
    }

    /**
     * Tests urlPathToAbsolutePath
     *
     * @param Assets $assets
     * @return void
     * 
     * @depends testAddAssetBundles
     */
    public function testUrlPathToAbsolutePath(Assets $assets)
    {
        $this->assertNull($assets->urlPathToAbsolutePath('../i/../don\'t/exist/and/cause/issues'));
    }

    /**
     * Tests getBaseUri
     *
     * @param Assets $assets
     * @return void
     * 
     * @depends testAddAssetBundles
     */
    public function testGetBaseUri(Assets $assets)
    {
        $this->assertEquals($assets->getBaseUrl(), 'https://assets.userfrosting.com/');
    }

    /**
     * Tests setLocatorScheme
     *
     * @param Assets $assets
     * @return void
     * 
     * @depends testAddAssetBundles
     */
    public function testSetLocatorScheme(Assets $assets)
    {
        $assets->setLocatorScheme('foo-bar');
        $this->assertEquals($assets->getLocatorScheme(), 'foo-bar://');

        $this->expectException(\InvalidArgumentException::class);
        $assets->setLocatorScheme('');
    }
}
