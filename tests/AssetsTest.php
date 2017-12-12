<?php

use PHPUnit\Framework\TestCase;
use RocketTheme\Toolbox\ResourceLocator\UniformResourceLocator;
use UserFrosting\Assets\Assets;
use UserFrosting\Assets\PathTransformer\PrefixTransformer;
use UserFrosting\Assets\AssetBundles\GulpBundleAssetsRawBundles;
use UserFrosting\Assets\AssetBundles\GulpBundleAssetsCompiledBundles;

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
        $this->locator = new UniformResourceLocator($this->basePath);
        $this->locator->addPath($this->locatorScheme, '', [
            'sprinkles/hawks/assets',
            'sprinkles/owls/assets'
        ]);
        $this->locator->addPath($this->locatorScheme, 'vendor', 'assets');
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
        $this->addToAssertionCount(1);// Emulate expectNoException assertion.
        return $assets;
    }

    /**
     * Tests Assets constructor with prefix transformations.
     * Returns the created Assets instance for use by dependent tests.
     *
     * @return Assets
     */
    public function testConstructAssetsWithPathTransformations()
    {
        $pathTransformer = new PrefixTransformer();
        $pathTransformer->define('assets', 'vendor');
        $pathTransformer->define('sprinkles/hawks/assets', 'sprinkles/hawks');
        $pathTransformer->define('sprinkles/owls/assets', 'sprinkles/owls');
        $assets = new Assets($this->locator, $this->locatorScheme, $this->baseUrl, $pathTransformer);
        $this->addToAssertionCount(1);// Emulate expectNoException assertion.
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
        $this->assertEquals($assets->getAbsoluteUrl('assets://vendor/bootstrap/js/bootstrap.js'), $this->baseUrl . 'assets/bootstrap/js/bootstrap.js');
    }

    /**
     * Test overrideBasePath to ensure override is used.
     *
     * @param Assets $assets
     * @return void
     * 
     * @depends testConstructAssets
     * @depends testGetAbsoluteUrlWithString
     */
    public function testOverrideBasePath(Assets $assets)
    {
        $assets->overrideBasePath(__DIR__ . '/data/assets');

        $this->assertEquals($assets->getAbsoluteUrl('assets://vendor/bootstrap/js/bootstrap.js'), $this->baseUrl . 'bootstrap/js/bootstrap.js');
        
        // Undo changes because PHPUnit likes to recycle dependencies.
        $assets->overrideBasePath(__DIR__ . '/data');
    }

    /**
     * Test getAbsoluteUrl while prefix transformations have been specified/
     *
     * @param Assets $assets
     * @return void
     * 
     * @depends testConstructAssetsWithPathTransformations
     */
    public function testGetAbsoluteUrlWithPathTransformations(Assets $assets)
    {
        // 'assets' to 'vendor' prefix transformation
        $this->assertEquals($assets->getAbsoluteUrl('assets://vendor/bootstrap/js/bootstrap.js'), $this->baseUrl . 'vendor/bootstrap/js/bootstrap.js');

        // 'sprinkles' to '' prefix transformation
        $this->assertEquals($assets->getAbsoluteUrl('assets://allowed.txt'), $this->baseUrl . 'sprinkles/hawks/allowed.txt');
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
            ]), $this->baseUrl . 'assets/bootstrap/js/bootstrap.js');
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
     * @return Assets
     * 
     * @depends testConstructAssetsWithPathTransformations
     */
    public function testAddAssetBundles(Assets $assets)
    {
        $assets->addAssetBundles(new GulpBundleAssetsRawBundles(__DIR__ . "/data/bundle.config.json"));
        $this->addToAssertionCount(1);// Emulate expectNoException assertion.
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
}