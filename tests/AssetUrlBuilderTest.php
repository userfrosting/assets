<?php

use PHPUnit\Framework\TestCase;
use RocketTheme\Toolbox\ResourceLocator\UniformResourceLocator;
use UserFrosting\Assets\UrlBuilder\AssetUrlBuilder;
use UserFrosting\Support\Exception\FileNotFoundException;

class AssetUrlBuilderTest extends TestCase
{
    protected $locator;
    protected $baseUrl;

    public function setUp()
    {
        $basePath = __DIR__ . '/data';
        $this->locator = new UniformResourceLocator($basePath);
        $this->locator->addPath('assets', '', [
            'owls/assets',
            'hawks/assets'
        ]);

        $this->baseUrl = 'http://example.com/assets-raw';
    }

    public function testGetUrl()
    {
        $assetUrlBuilder = new AssetUrlBuilder($this->locator, $this->baseUrl);

        $path = 'vendor/bootstrap-3.3.6/css/bootstrap.css';

        $url = $assetUrlBuilder->getAssetUrl($path);

        $this->assertEquals('http://example.com/assets-raw/owls/assets/vendor/bootstrap-3.3.6/css/bootstrap.css', $url);
    }

    public function testGetUrlRemovePrefix()
    {
        $assetUrlBuilder = new AssetUrlBuilder($this->locator, $this->baseUrl, 'owls');

        $path = 'vendor/bootstrap-3.3.6/css/bootstrap.css';

        $url = $assetUrlBuilder->getAssetUrl($path);

        $this->assertEquals('http://example.com/assets-raw/assets/vendor/bootstrap-3.3.6/css/bootstrap.css', $url);
    }

    public function testBadFilePathException()
    {
        $assetUrlBuilder = new AssetUrlBuilder($this->locator, $this->baseUrl);

        $path = 'some/bad/path.txt';

        $this->expectException(FileNotFoundException::class);
        $url = $assetUrlBuilder->getAssetUrl($path);
    }
}
