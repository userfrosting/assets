<?php

/*
 * UserFrosting Assets (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/assets
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Jordan Mele
 * @license   https://github.com/userfrosting/assets/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Assets\ServeAsset;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use UserFrosting\Assets\AssetLoader;

/**
 * Asset server for applications using Slim. Intended for development scenarios.
 * Requires an instance of AssetLoader to handle loading assets from filesystem or remote source.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 * @author Jordan Mele
 */
class SlimServeAsset
{
    /**
     * @var \UserFrosting\Assets\AssetLoader The asset loader instance
     */
    protected $assetLoader;

    /**
     * Constructor.
     *
     * @param \UserFrosting\Assets\AssetLoader $assetLoader The asset loader instance
     */
    public function __construct(AssetLoader $assetLoader)
    {
        $this->assetLoader = $assetLoader;
    }

    /**
     * Reverses prefix transformations, locates asset via locator and adds asset content to response.
     * On failure, sets response status code to 404.
     * To use, simply hook up to routing with the necessary relative url passed through at 'url'.
     * NOTE: PHP is very inefficent when it comes to serving static assets, and as such this is only recommended for testing purposes.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array             $args
     */
    public function serveAsset(RequestInterface $request, ResponseInterface $response, $args)
    {
        /** @var \UserFrosting\Assets\AssetLoader $assetLoader */
        $assetLoader = $this->assetLoader;

        // Return 404 response if asset can't be loaded
        if (!$assetLoader->loadAsset($args['url'])) {
            return $response->withStatus(404);
        }

        // Generate file last modified
        $lastModified = $assetLoader->getLastModified()->format('D, d M Y H:i:s \G\M\T');

        // Return 304 if asset not modified
        try {
            $clientLastModified = $request->getHeader('If-Modified-Since')[0] ?? false;
            if ($lastModified === $clientLastModified) {
                return $response->withStatus(304);
            }
        } catch (\Exception $e) {
            // Fallback to regular response
        }

        $response->getBody()->write($assetLoader->getContent());
        return $response->withHeader('Content-Type', $assetLoader->getType())
            ->withHeader('Content-Length', $assetLoader->getLength())
            ->withHeader('Cache-Control', 'no-cache')
            ->withHeader('Last-Modified', $lastModified);
    }
}
