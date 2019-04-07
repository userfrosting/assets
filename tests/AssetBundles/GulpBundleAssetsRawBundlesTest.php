<?php

/*
 * UserFrosting Assets (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/assets
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Jordan Mele
 * @license   https://github.com/userfrosting/assets/blob/master/LICENSE.md (MIT License)
 */

use PHPUnit\Framework\TestCase;
use UserFrosting\Assets\AssetBundles\GulpBundleAssetsRawBundles;
use UserFrosting\Assets\Assets;
use UserFrosting\Assets\Exception\InvalidBundlesFileException;
use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\Support\Exception\JsonException;

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
    public function testConstruct()
    {
        $bundles = new GulpBundleAssetsRawBundles(__DIR__.'/../data/bundle.config.json');
        $this->assertInstanceOf(GulpBundleAssetsRawBundles::class, $bundles);

        return $bundles;
    }

    /**
     * Tests GulpBundleAssetsRawBundles constructor with a config that has no bundles property.
     */
    public function testConstructStubConfig()
    {
        $bundles = new GulpBundleAssetsRawBundles(__DIR__.'/../data/bundle.config.stub.json');
        $this->assertInstanceOf(GulpBundleAssetsRawBundles::class, $bundles);
    }

    /**
     * Tests GulpBundleAssetsRawBundles constructor with config containing invalid syntax.
     */
    public function testConstructInvalidSyntax()
    {
        $this->expectException(JsonException::class);
        new GulpBundleAssetsRawBundles(__DIR__.'/../data/bundle.config.invalid-syntax.json');
    }

    /**
     * Tests GulpBundleAssetsRawBundles constructor with missing config.
     */
    public function testConstructNotFound()
    {
        $this->expectException(FileNotFoundException::class);
        new GulpBundleAssetsRawBundles(__DIR__.'/../data/bundle.config.not-here.json');
    }

    /**
     * Tests GulpBundleAssetsRawBundles constructor when the bundle property is the incorrect type.
     */
    public function testConstructInvalidBundlesPropertyType()
    {
        $this->expectException(InvalidBundlesFileException::class);
        new GulpBundleAssetsRawBundles(__DIR__.'/../data/bundle.config.bad-bundle.json');
    }

    /**
     * Tests GulpBundleAssetsRawBundles constructor when a bundle contains an invalid styles property.
     */
    public function testConstructInvalidStylesBundle()
    {
        $this->expectException(InvalidBundlesFileException::class);
        new GulpBundleAssetsRawBundles(__DIR__.'/../data/bundle.config.bad-styles.json');
    }

    /**
     * Tests GulpBundleAssetsRawBundles constructor when a bundle contains an invalid scripts property.
     */
    public function testConstructInvalidJsBundle()
    {
        $this->expectException(InvalidBundlesFileException::class);
        new GulpBundleAssetsRawBundles(__DIR__.'/../data/bundle.config.bad-scripts.json');
    }

    /**
     * Tests getCssBundleAssets method.
     *
     * @param GulpBundleAssetsRawBundles $bundles
     *
     * @return void
     *
     * @depends testConstruct
     */
    public function testGetCssBundleAssets(GulpBundleAssetsRawBundles $bundles)
    {
        $this->assertEquals($bundles->getCssBundleAssets('test'), [
            'vendor/bootstrap/css/bootstrap.css',
        ]);
    }

    /**
     * Tests that getCssBundleAssets method throws an exception when requested bundle doesn't exist.
     *
     * @param GulpBundleAssetsRawBundles $bundles
     *
     * @return void
     *
     * @depends testConstruct
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
     *
     * @return void
     *
     * @depends testConstruct
     */
    public function testGetJsBundleAssets(GulpBundleAssetsRawBundles $bundles)
    {
        $this->assertEquals($bundles->getJsBundleAssets('test'), [
            'vendor/bootstrap/js/bootstrap.js',
            'vendor/bootstrap/js/npm.js',
        ]);
    }

    /**
     * Tests that getJsBundleAssets method throws an exception when requested bundle doesn't exist.
     *
     * @param GulpBundleAssetsRawBundles $bundles
     *
     * @return void
     *
     * @depends testConstruct
     */
    public function testGetJsBundleAssetsOutOfRange(GulpBundleAssetsRawBundles $bundles)
    {
        $this->expectException(\OutOfRangeException::class);
        $bundles->getJsBundleAssets('owls');
    }
}
