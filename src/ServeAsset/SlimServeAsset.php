<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @package   userfrosting/assets
 * @link      https://github.com/userfrosting/assets
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Assets\ServeAsset;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use UserFrosting\Assets\Util\MimeType;

/**
 * Asset server for applications using Slim. Intended for development scenarios.
 * Assumes Assets is added to CI as 'assets'.
 * 
 * @author Alex Weissman (https://alexanderweissman.com)
 * @author Jordan Mele
 */
class SlimServeAsset
{
    /**
     * @var Slim\Container The global container object, which holds all your services.
     */
    protected $ci;

    /**
     * Constructor.
     *
     * @param Slim\Container $ci The global container object, which holds all your services.
     */
    public function __construct($ci)
    {
        $this->ci = $ci;
    }

    /**
     * Reverses prefix transformations, locates asset via locator and adds asset content to response.
     * On failure, sets response status code to 404.
     * To use, simply hook up to routing with the necessary relative url passed through at 'url'.
     * NOTE: PHP is very inefficent when it comes to serving static assets, and as such this is only recommended for testing purposes.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return void
     */
    public function serveAsset(RequestInterface $request, ResponseInterface $response, $args)
    {
        // Get full path.
        $fullPath = $this->ci->assets->urlPathToAbsolutePath($args['url']);

        // If path not set, return 404.
        if (!$fullPath) {
            return $response->withStatus(404);
        }

        // Send back asset data.
        return $response->withHeader('Content-Type', MimeType::detectByFilename($fullPath))
                 ->withHeader('Content-Length', filesize($fullPath))
                 ->write(file_get_contents($fullPath));
    }
}