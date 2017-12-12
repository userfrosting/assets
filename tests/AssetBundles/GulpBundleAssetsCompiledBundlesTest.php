<?php

use PHPUnit\Framework\TestCase;
use UserFrosting\Assets\AssetBundles\GulpBundleAssetsCompiledBundles;

/**
 * Tests GulpBundleAssetsCompiledBundles class.
 */
class GulpBundleAssetsCompiledBundlesTest extends TestCase
{
    /**
     * Tests GulpBundleAssetsCompiledBundles constructor.
     * Returns the created GulpBundleAssetsCompiledBundles instance for use by dependent tests.
     *
     * @return Assets
     */
    public function testConstructGulpBundleAssetsCompiledBundles()
    {
        $bundles = new GulpBundleAssetsCompiledBundles(__DIR__ . "/../data/bundle.result.json");
        $this->addToAssertionCount(1);// Emulate expectNoException assertion.
        return $bundles;
    }

    /**
     * Tests getCssBundleAssets method.
     *
     * @param GulpBundleAssetsCompiledBundles $bundles
     * @return void
     * 
     * @depends testConstructGulpBundleAssetsCompiledBundles
     */
    public function testGetCssBundleAssets(GulpBundleAssetsCompiledBundles $bundles)
    {
        $this->assertEquals($bundles->getCssBundleAssets('test'), [
            'test-930fa5c1ee.css'
        ]);
    }

    /**
     * Tests that getCssBundleAssets method throws an exception when requested bundle doesn't exist.
     *
     * @param GulpBundleAssetsCompiledBundles $bundles
     * @return void
     * 
     * @depends testConstructGulpBundleAssetsCompiledBundles
     */
    public function testGetCssBundleAssetsOutOfRange(GulpBundleAssetsCompiledBundles $bundles)
    {
        $this->expectException(\OutOfRangeException::class);
        $bundles->getCssBundleAssets('owls');
    }

    /**
     * Tests getJsBundleAssets method.
     *
     * @param GulpBundleAssetsCompiledBundles $bundles
     * @return void
     * 
     * @depends testConstructGulpBundleAssetsCompiledBundles
     */
    public function testGetJsBundleAssets(GulpBundleAssetsCompiledBundles $bundles)
    {
        $this->assertEquals($bundles->getJsBundleAssets('test'), [
            'test-930fa5c1ee.js'
        ]);
    }

    /**
     * Tests that getJsBundleAssets method throws an exception when requested bundle doesn't exist.
     *
     * @param GulpBundleAssetsCompiledBundles $bundles
     * @return void
     * 
     * @depends testConstructGulpBundleAssetsCompiledBundles
     */
    public function testGetJsBundleAssetsOutOfRange(GulpBundleAssetsCompiledBundles $bundles)
    {
        $this->expectException(\OutOfRangeException::class);
        $bundles->getJsBundleAssets('owls');
    }
}