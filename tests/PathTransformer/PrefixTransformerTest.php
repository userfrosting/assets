<?php

use PHPUnit\Framework\TestCase;
use UserFrosting\Assets\PathTransformer\PrefixTransformer;

/**
 * Tests PrefixTransformer class.
 */
class PrefixTransformerTest extends TestCase
{
    /**
     * Tests PrefixTransformer constructor.
     * Returns the created PrefixTransformer instance for use by dependent tests.
     *
     * @return PrefixTransformer
     */
    public function testConstructPrefixTransformer()
    {
        $pt = new PrefixTransformer();
        $this->addToAssertionCount(1);// Emulate expectNoException assertion.
        return $pt;
    }

    /**
     * Test define method with good parameters.
     *
     * @param PrefixTransformer $prefixTransformer
     * @return void
     * 
     * @depends testConstructPrefixTransformer
     */
    public function testDefineWithGoodParameters(PrefixTransformer $prefixTransformer)
    {
        $prefixTransformer->define('foo', 'bar');
        $this->addToAssertionCount(1);// Emulate expectNoException assertion.
        return $prefixTransformer;
    }

    /**
     * Test define method with bad parameters.
     *
     * @param PrefixTransformer $prefixTransformer
     * @return void
     * 
     * @depends testConstructPrefixTransformer
     */
    public function testDefineWithBadParameters(PrefixTransformer $prefixTransformer)
    {
        $this->expectException(\InvalidArgumentException::class);
        $prefixTransformer->define(32, 23);
    }

    /**
     * Tests the pathToUrl method.
     *
     * @param PrefixTransformer $prefixTransformer
     * @return void
     * 
     * @depends testDefineWithGoodParameters
     */
    public function testPathToUrl(PrefixTransformer $prefixTransformer)
    {
        $this->assertEquals($prefixTransformer->pathToUrl('foo/test.file'), 'bar/test.file');
    }

    /**
     * Tests the pathToUrl method when there are no matches.
     *
     * @param PrefixTransformer $prefixTransformer
     * @return void
     * 
     * @depends testDefineWithGoodParameters
     */
    public function testPathToUrlWithNoMatch(PrefixTransformer $prefixTransformer)
    {
        $this->expectException(\OutOfRangeException::class);
        $prefixTransformer->pathToUrl('oo/test.file');
    }

    /**
     * Tests the urlToPath method.
     *
     * @param PrefixTransformer $prefixTransformer
     * @return void
     * 
     * @depends testDefineWithGoodParameters
     */
    public function testUrlToPath(PrefixTransformer $prefixTransformer)
    {
        $this->assertEquals($prefixTransformer->urlToPath('bar/test.file'), 'foo/test.file');
    }

    /**
     * Tests the urlToPath method.
     *
     * @param PrefixTransformer $prefixTransformer
     * @return void
     * 
     * @depends testDefineWithGoodParameters
     */
    public function testUrlToPathWithNoMatch(PrefixTransformer $prefixTransformer)
    {
        $this->expectException(\OutOfRangeException::class);
        $prefixTransformer->urlToPath('ar/test.file');
    }
}