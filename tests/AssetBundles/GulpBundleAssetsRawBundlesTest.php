<?php

use PHPUnit\Framework\TestCase;
use UserFrosting\Assets\AssetBundles\GulpBundleAssetsRawBundles;

/**
 * Tests GulpBundleAssetsRawBundles class.
 */
class GulpBundleAssetsRawBundlesTest extends TestCase
{
    /**
     * Tests GulpBundleAssetsRawBundles constructor.
     * Returns the created GulpBundleAssetsRawBundles instance for use by dependent tests.
     *
     * @return Assets
     */
    public function testConstructGulpBundleAssetsRawBundles()
    {
        $bundles = new GulpBundleAssetsRawBundles(__DIR__ . "/../data/bundle.config.json");
        $this->addToAssertionCount(1);// Emulate expectNoException assertion.
        return $bundles;
    }

    /**
     * Tests getCssBundleAssets method.
     *
     * @param GulpBundleAssetsRawBundles $bundles
     * @return void
     * 
     * @depends testConstructGulpBundleAssetsRawBundles
     */
    public function testGetCssBundleAssets(GulpBundleAssetsRawBundles $bundles)
    {
        $this->assertEquals($bundles->getCssBundleAssets('test'), [
            'vendor/bootstrap/css/bootstrap.css'
        ]);
    }

    /**
     * Tests that getCssBundleAssets method throws an exception when requested bundle doesn't exist.
     *
     * @param GulpBundleAssetsRawBundles $bundles
     * @return void
     * 
     * @depends testConstructGulpBundleAssetsRawBundles
     */
    public function testGetCssBundleAssetsOutOfRange(GulpBundleAssetsRawBundles $bundles)
    {
        $this->expectException(\OutOfRangeException::class);
        $bundles->getCssBundleAssets('owls');
    }

    /**
     * Tests getJsBundleAssets method.
     *
     * @param GulpBundleAssetsRawBundles $bundles
     * @return void
     * 
     * @depends testConstructGulpBundleAssetsRawBundles
     */
    public function testGetJsBundleAssets(GulpBundleAssetsRawBundles $bundles)
    {
        $this->assertEquals($bundles->getJsBundleAssets('test'), [
            'vendor/bootstrap/js/bootstrap.js',
            'vendor/bootstrap/js/npm.js'
        ]);
    }

    /**
     * Tests that getJsBundleAssets method throws an exception when requested bundle doesn't exist.
     *
     * @param GulpBundleAssetsRawBundles $bundles
     * @return void
     * 
     * @depends testConstructGulpBundleAssetsRawBundles
     */
    public function testGetJsBundleAssetsOutOfRange(GulpBundleAssetsRawBundles $bundles)
    {
        $this->expectException(\OutOfRangeException::class);
        $bundles->getJsBundleAssets('owls');
    }
}