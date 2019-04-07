<?php

/*
 * UserFrosting Assets (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/assets
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Jordan Mele
 * @license   https://github.com/userfrosting/assets/blob/master/LICENSE.md (MIT License)
 */

use PHPUnit\Framework\TestCase;
use UserFrosting\Assets\AssetBundles\GulpBundleAssetsCompiledBundles;
use UserFrosting\Assets\Assets;
use UserFrosting\Assets\Exception\InvalidBundlesFileException;

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
    public function testConstruct()
    {
        $bundles = new GulpBundleAssetsCompiledBundles(__DIR__.'/../data/bundle.result.json');
        $this->assertInstanceOf(GulpBundleAssetsCompiledBundles::class, $bundles);

        return $bundles;
    }

    /**
     * Tests GulpBundleAssetsCompiledBundles constructor when a bundle contains an invalid styles property.
     */
    public function testConstructInvalidStylesBundle()
    {
        $this->expectException(InvalidBundlesFileException::class);
        new GulpBundleAssetsCompiledBundles(__DIR__.'/../data/bundle.result.bad-styles.json');
    }

    /**
     * Tests GulpBundleAssetsCompiledBundles constructor when a bundle contains an invalid scripts property.
     */
    public function testConstructInvalidJsBundle()
    {
        $this->expectException(InvalidBundlesFileException::class);
        new GulpBundleAssetsCompiledBundles(__DIR__.'/../data/bundle.result.bad-scripts.json');
    }

    /**
     * Tests getCssBundleAssets method.
     *
     * @param GulpBundleAssetsCompiledBundles $bundles
     *
     * @return void
     *
     * @depends testConstruct
     */
    public function testGetCssBundleAssets(GulpBundleAssetsCompiledBundles $bundles)
    {
        $this->assertEquals($bundles->getCssBundleAssets('test'), [
            'test-930fa5c1ee.css',
        ]);
    }

    /**
     * Tests that getCssBundleAssets method throws an exception when requested bundle doesn't exist.
     *
     * @param GulpBundleAssetsCompiledBundles $bundles
     *
     * @return void
     *
     * @depends testConstruct
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
     *
     * @return void
     *
     * @depends testConstruct
     */
    public function testGetJsBundleAssets(GulpBundleAssetsCompiledBundles $bundles)
    {
        $this->assertEquals($bundles->getJsBundleAssets('test'), [
            'test-930fa5c1ee.js',
        ]);
    }

    /**
     * Tests that getJsBundleAssets method throws an exception when requested bundle doesn't exist.
     *
     * @param GulpBundleAssetsCompiledBundles $bundles
     *
     * @return void
     *
     * @depends testConstruct
     */
    public function testGetJsBundleAssetsOutOfRange(GulpBundleAssetsCompiledBundles $bundles)
    {
        $this->expectException(\OutOfRangeException::class);
        $bundles->getJsBundleAssets('owls');
    }
}
