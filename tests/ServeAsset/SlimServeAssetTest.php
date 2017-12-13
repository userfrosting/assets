<?php

use PHPUnit\Framework\TestCase;
use RocketTheme\Toolbox\ResourceLocator\UniformResourceLocator;
use UserFrosting\Assets\ServeAsset\SlimServeAsset;
use UserFrosting\Assets\Assets;
use Slim\Container;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Tests SlimServeAsset class.
 */
class SlimServeAssetTest extends TestCase
{
    /** @var Container */
    private $container;

    /**
     * Initializes test environment.
     *
     * @return void
     */
    public function setUp()
    {
        $basePath = __DIR__ . '/../data';
        $baseUrl = "https://assets.userfrosting.com/";
        $locatorScheme = "assets";
        $locator = new UniformResourceLocator($basePath);
        $locator->addPath($locatorScheme, '', [
            'sprinkles/hawks/assets',
            'sprinkles/owls/assets'
        ]);
        $locator->addPath($locatorScheme, 'vendor', 'assets');

        // Initialize Assets
        $assets = new Assets($locator, $locatorScheme, $baseUrl);

        // Initialize container
        $this->container = new Container();

        // Add Assets
        $this->container['assets'] = function ($ci) use ($assets) {
            return $assets;
        };
    }

    /**
     * Tests SlimServeAsset constructor.
     *
     * @return SlimServeAsset
     */
    public function testConstructor()
    {
        $server = new SlimServeAsset($this->container);
        $this->addToAssertionCount(1);// Emulate expectNoException assertion.
        return $server;
    }

    /**
     * Test with non-existent asset.
     *
     * @return void
     * 
     * @depends testConstructor
     */
    public function testInaccessibleAsset(SlimServeAsset $controller)
    {
        // Create environment.
        $environment = Environment::mock([]);

        // Create request and response objects.
        $request = Request::createFromEnvironment($environment);
        $response = new Response();

        // Invoke controller method.
        $response = $controller->serveAsset($request, $response, [
            'url' => 'sprinkles/hawks/forbidden.txt'
        ]);

        // Assert 404 response
        $this->assertSame($response->getStatusCode(), 404);

        // Assert empty response body
        $this->assertSame($response->getBody()->getContents(), '');
    }

    /**
     * Test with existent asset.
     *
     * @return void
     * 
     * @depends testConstructor
     */
    public function testAssetMatchesExpectations($controller)
    {
        // Create environment.
        $environment = Environment::mock([]);

        // Create request and response objects.
        $request = Request::createFromEnvironment($environment);
        $response = new Response();

        // Invoke controller method.
        $response = $controller->serveAsset($request, $response, [
            'url' => 'sprinkles/hawks/assets/allowed.txt'
        ]);

        // Assert 200 response
        $this->assertSame($response->getStatusCode(), 200);

        // Assert response body matches file
        $this->assertSame($response->getBody()->__toString(), file_get_contents(__DIR__ . '/../data/sprinkles/hawks/assets/allowed.txt'));

        // Assert correct MIME
        $this->assertSame($response->getHeader('Content-Type'), ['text/plain']);
    }
}