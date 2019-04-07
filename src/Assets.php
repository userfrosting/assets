<?php

/*
 * UserFrosting Assets (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/assets
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Jordan Mele
 * @license   https://github.com/userfrosting/assets/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Assets;

use UserFrosting\Assets\AssetBundles\AssetBundlesInterface;
use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\Support\Util\Util;
use UserFrosting\UniformResourceLocator\ResourceLocatorInterface;

/**
 * Facilitates convenient access to assets and asset bundles within PHP code.
 * Useful for production and development scenarios.
 *
 * @see AssetsTemplatePlugin for template engine integration.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 * @author Jordan Mele (https://blog.djmm.me)
 */
class Assets
{
    /** @var ResourceLocatorInterface Resource locator used to find assets. */
    protected $locator;

    /** @var string Scheme used when finding assets via $locator. */
    protected $locatorScheme;

    /** @var string Sites base URL and optionally assets directory. */
    protected $baseUrl;

    /** @var AssetBundlesInterface[] Objects that serve as an adapter to the various asset bundling systems. */
    protected $assetBundles = [];

    /**
     * Constructor.
     *
     * @param ResourceLocatorInterface $locator       Resource locator used to find assets.
     * @param string                   $locatorScheme Scheme to use in locator.
     * @param string                   $baseUrl       Sites base URL and optionally assets directory to later use when generating absolute path to an asset.
     */
    public function __construct(ResourceLocatorInterface $locator, $locatorScheme, $baseUrl)
    {
        // Set locator
        $this->locator = $locator;

        // Set locator scheme
        $this->setLocatorScheme($locatorScheme);

        // Set base URL
        $this->setBaseUrl($baseUrl);
    }

    /**
     * Add asset bundles.
     *
     * @param AssetBundlesInterface $assetBundles
     */
    public function addAssetBundles(AssetBundlesInterface $assetBundles)
    {
        $this->assetBundles[] = $assetBundles;
    }

    /**
     * Reset asset bundles, removing all registered assetBundles.
     */
    public function resetAssetBundles()
    {
        $this->assetBundles = [];
    }

    /**
     * Returns the list of assetBundles.
     *
     * @return AssetBundlesInterface[]
     */
    public function getAssetBundles()
    {
        return $this->assetBundles;
    }

    /**
     * Get asset paths within specified JS bundle.
     *
     * @param string $bundleName Bundle name.
     *
     * @throws \OutOfRangeException if specified JS asset bundle is not found.
     *
     * @return string[]
     */
    public function getJsBundleAssets($bundleName)
    {
        $assets = [];

        foreach ($this->assetBundles as $assetBundles) {
            try {
                $assets = array_merge($assets, $assetBundles->getJsBundleAssets($bundleName));
            } catch (\OutOfRangeException $e) {
            }
        }

        if (count($assets) === 0) {
            throw new \OutOfRangeException("JS asset bundle '$bundleName' does not exist.");
        }

        // Resolve to url
        foreach ($assets as &$asset) {
            $asset = $this->getAbsoluteUrl($this->getLocatorScheme().$asset);
        }

        return $assets;
    }

    /**
     * Get asset paths within specified CSS bundle.
     *
     * @param string $bundleName Bundle name.
     *
     * @throws \OutOfRangeException if specified CSS asset bundle is not found.
     *
     * @return string[]
     */
    public function getCssBundleAssets($bundleName)
    {
        $assets = [];

        foreach ($this->assetBundles as $assetBundles) {
            try {
                $assets = array_merge($assets, $assetBundles->getCssBundleAssets($bundleName));
            } catch (\OutOfRangeException $e) {
            }
        }

        if (count($assets) === 0) {
            throw new \OutOfRangeException("CSS asset bundle '$bundleName' does not exist.");
        }

        // Resolve to url
        foreach ($assets as &$asset) {
            $asset = $this->getAbsoluteUrl($this->getLocatorScheme().$asset);
        }

        return $assets;
    }

    /**
     * Get Asset url.
     * Transform a locator uri to a url accessible to a browser
     * In other words, transform `assets://vendor/bootstrap/js/bootstrap.js` to
     * `http://example.com/vendor/bootstrap/js/bootstrap.js`, replacing the `://` with the base url
     * Make sure the ressource exist in the process.
     *
     * @param string|array $streamPath The asset uri
     *
     * @throws \BadMethodCallException
     * @throws FileNotFoundException
     */
    public function getAbsoluteUrl($streamPath)
    {
        // Support stream lookup in ['asset', 'path/to'] or 'asset://path/to' form.
        if (is_array($streamPath)) { // Convert to 'asset://path/to' form
            if (count($streamPath) != 2) {
                throw new \BadMethodCallException('Invalid stream path given.');
            }
            $streamPath = "$streamPath[0]://$streamPath[1]";
        }

        // Get asset resource from locator.
        // Make sure an asset can be found at this uri before creating and url
        $assetResource = $this->locator->getResource($streamPath);
        if ($assetResource === false) {
            throw new FileNotFoundException("No file could be resolved for the stream path '$streamPath'.");
        }

        // Need to dissociate the scheme from the search query in the stream path
        $streamPathQuery = Util::stripPrefix($streamPath, $this->getLocatorScheme());

        return $this->baseUrl.$streamPathQuery;
    }

    /**
     * Processes a relative path from a URL to an absolute path. Returns null if no file exists at the generated path.
     * Applies protections against attempts to access restricted files.
     *
     * @param string $uncleanRelativePath Potentially dangerous relative path.
     *
     * @return null|string
     */
    public function urlPathToAbsolutePath($uncleanRelativePath)
    {
        if (!$uri = $this->urlPathToStreamUri($uncleanRelativePath)) {
            return;
        }

        // Get resource from stream uri
        $resource = $this->locator->getResource($uri);

        // Make path absolute (and normalise)
        $absolutePath = realpath($resource->getAbsolutePath());

        // Return path or null depending on existence.
        if ($absolutePath && is_file($absolutePath)) {
            return $absolutePath;
        } else {
            return;
        }
    }

    /**
     * Processes a relative path from a URL to a locator stream uri. Returns null if no file exists at the generated path.
     * Applies protections against attempts to access restricted files.
     *
     * @param string $urlPath
     *
     * @return string
     */
    public function urlPathToStreamUri($urlPath)
    {
        // Normalize path to prevent directory traversal.
        $urlPath = Util::normalizePath($urlPath);

        // Remove any query string.
        $urlPath = preg_replace('/\?.*/', '', $urlPath);

        // Remove base url
        $urlPath = Util::stripPrefix($urlPath, $this->baseUrl);

        // Add back the stream scheme
        $uri = $this->getLocatorScheme().$urlPath;

        // Make sure ressource path exist
        if (!$this->locator->getResource($uri)) {
            return;
        }

        return $uri;
    }

    /**
     * Returns base Assets base Url.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set Asset base Url.
     *
     * @param string $baseUrl
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setBaseUrl($baseUrl)
    {
        // Make sure it's a string, until php 7.1
        if (!is_string($baseUrl)) {
            throw new \InvalidArgumentException('$baseUrl must be of type string but was '.gettype($baseUrl)); // @codeCoverageIgnore
        }

        // Make sure url ends with a slash
        $baseUrl = rtrim($baseUrl, '/').'/';
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * Get Asset Locator Scheme.
     *
     * @return string
     */
    public function getLocatorScheme()
    {
        return $this->locatorScheme.'://';
    }

    /**
     * Set Asset locator scheme.
     *
     * @param string $locatorScheme
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function setLocatorScheme($locatorScheme)
    {
        // Make sure it's a string, until php 7.1
        if (!is_string($locatorScheme)) {
            throw new \InvalidArgumentException('$locateScheme must be of type string but was '.gettype($locatorScheme)); // @codeCoverageIgnore
        } elseif ($locatorScheme == '') {
            throw new \InvalidArgumentException('$locatorScheme must not be an empty string.');
        }
        $this->locatorScheme = $locatorScheme;

        return $this;
    }
}
