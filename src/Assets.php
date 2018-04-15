<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @package   userfrosting/assets
 * @link      https://github.com/userfrosting/assets
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Assets;

use RocketTheme\Toolbox\ResourceLocator\ResourceLocatorInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use UserFrosting\Support\Util\Util;
use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\Assets\AssetBundles\AssetBundlesInterface;
use UserFrosting\Assets\PathTransformer\PathTransformerInterface;

/**
 * Facilitates convenient access to assets and asset bundles within PHP code.
 * Useful for production and development scenarios.
 *
 * @see AssetsTemplatePlugin for template engine integration.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 * @author Jordan Mele
 */
class Assets
{
    /** @var ResourceLocatorInterface Resource locator used to find assets. */
    private $locator;

    /** @var string Scheme used when finding assets via $locator. */
    private $locatorScheme;

    /** @var string[] Paths accessible via locator scheme. */
    private $locatorPaths;

    /** @var string Sites base URL and optionally assets directory. */
    private $baseUrl;

    /** @var string Base path, typically applied to locator instance. */
    private $basePath;

    /** @var PathTransformerInterface */
    private $pathTransformer;

    /** @var AssetBundlesInterface|AssetBundlesInterface[]|null Objects that serve as an adapter to the various asset bundling systems. */
    private $assetBundles;

    /**
     * Constructor
     *
     * @param ResourceLocatorInterface $locator Resource locator used to find assets.
     * @param string $locatorScheme Scheme to use in locator.
     * @param string $baseUrl Sites base URL and optionally assets directory to later use when generating absolute path to an asset.
     * @param string $pathTransformer
     */
    public function __construct(ResourceLocatorInterface $locator, $locatorScheme, $baseUrl, PathTransformerInterface $pathTransformer = null)
    {
        // Set locator
        $this->locator = $locator;

        // Set locator scheme
        if (!is_string($locatorScheme)) {
            throw new \InvalidArgumentException('$locateScheme must be of type string but was ' . gettype($locatorScheme));
        } elseif ($locatorScheme == "") {
            throw new \InvalidArgumentException('$locatorScheme must not be an empty string.');
        }
        $this->locatorScheme = $locatorScheme . "://";

        // Set locator paths
        $this->locatorPaths = [];
        // Normalize and add paths
        // NOTE : getPaths doesn't exist anymore in locator since paths depends on stream and location.
        //        Could be added to locator or fixed in another way. This doesn't seams necesarry later on...
        /*foreach ($locator->getPaths($locatorScheme) as $pathSet) {
            foreach ($pathSet as $path) {
                $this->locatorPaths[] = $path;
            }
        }*/

        // Set base URL
        if (!is_string($baseUrl)) {
            throw new \InvalidArgumentException('$baseUrl must be of type string but was ' . gettype($baseUrl));
        }
        // Make sure url ends with a slash
        $baseUrl = rtrim($baseUrl, '/') . '/';
        $this->baseUrl = $baseUrl;

        // Set base path
        // NOTE : NO. This class doesn't need to know this. IT should ask the locator to find stuff.
        $this->basePath = $locator->getBasePath();

        // Initialize asset bundles
        $this->assetBundles = null;

        // Set path transformer
        $this->pathTransformer = $pathTransformer;
    }

    /**
     * Overrides the current base path.
     *
     * @param string $basePath A valid directory to replace the current base path. Default is determined by provided ResourceLocator.
     * @return void
     */
    public function overrideBasePath($basePath)
    {
        if (!is_string($basePath)) {
            throw new \InvalidArgumentException('$basePath must be of type string but was ' . gettype($basePath));
        }

        // Normalize base path using same method as locator.
        // TODO : Use locator normalize ? Paass to locator
        $this->basePath = rtrim(str_replace('\\', '/', $basePath ?: getcwd()), '/');
    }

    /**
     * Sets the path transformer used to convert relative file paths into the intended URL ready form.
     *
     * @param PathTransformerInterface $pathTransformer
     * @return void
     */
    public function setPathTransformer(PathTransformerInterface $pathTransformer)
    {
        $this->pathTransformer = $pathTransformer;
    }

    /**
     * Add asset bundles.
     *
     * @param AssetBundlesInterface $assetBundles
     * @return void
     */
    public function addAssetBundles(AssetBundlesInterface $assetBundles)
    {
        if ($this->assetBundles == null) {
            $this->assetBundles = $assetBundles;
        } elseif (!is_array($this->assetBundles)) {
            $this->assetBundles = [
                $this->assetBundles,
                $assetBundles
            ];
        } else {
            $this->assetBundles[] = $assetBundles;
        }
    }

    /**
     * Get asset paths within specified JS bundle.
     *
     * @param string $bundleName Bundle name.
     * @return string[]
     *
     * @throws \OutOfRangeException if specified JS asset bundle is not found.
     */
    public function getJsBundleAssets($bundleName)
    {
        $assets = [];
        if ($this->assetBundles === null) {
            throw new \OutOfRangeException("JS asset bundle '$bundleName' does not exist.");
        } elseif ($this->assetBundles instanceof AssetBundlesInterface) {
            $assets = array_merge($assets, $this->assetBundles->getJsBundleAssets($bundleName));
        } else {
            foreach ($this->assetBundles as $assetBundles) {
                try {
                    $assets = array_merge($assets, $assetBundles->getJsBundleAssets($bundleName));
                } catch (\OutOfRangeException $e) {}
            }
            if (count($assets) === 0) {
                throw new \OutOfRangeException("JS asset bundle '$bundleName' does not exist.");
            }
        }

        // Resolve to url
        foreach ($assets as &$asset) {
            $asset = $this->getAbsoluteUrl($this->locatorScheme . $asset);
        }

        return $assets;
    }

    /**
     * Get asset paths within specified CSS bundle.
     *
     * @param string $bundleName Bundle name.
     * @return string[]
     *
     * @throws \OutOfRangeException if specified CSS asset bundle is not found.
     */
    public function getCssBundleAssets($bundleName)
    {
        $assets = [];
        if ($this->assetBundles === null) {
            throw new \OutOfRangeException("CSS asset bundle '$bundleName' does not exist.");
        } elseif ($this->assetBundles instanceof AssetBundlesInterface) {
            $assets = array_merge($assets, $this->assetBundles->getCssBundleAssets($bundleName));
        } else {
            foreach ($this->assetBundles as $assetBundles) {
                try {
                    $assets = array_merge($assets, $assetBundles->getCssBundleAssets($bundleName));
                } catch (\OutOfRangeException $e) {}
            }
            if (count($assets) === 0) {
                throw new \OutOfRangeException("CSS asset bundle '$bundleName' does not exist.");
            }
        }

        // Resolve to url
        foreach ($assets as &$asset) {
            $asset = $this->getAbsoluteUrl($this->locatorScheme . $asset);
        }

        return $assets;
    }

    /**
     * Returns URL for asset at specified stream path.
     *
     * @param string $streamPath
     * @return string
     */
    public function getAbsoluteUrl($streamPath)
    {
        // Support stream lookup in ['asset', 'path/to'] or 'asset://path/to' form.
        if (is_array($streamPath)) {// Convert to 'asset://path/to' form
            if (count($streamPath) != 2) {
                throw new \BadMethodCallException('Invalid stream path given.');
            }
            $streamPath = "$streamPath[0]://$streamPath[1]";
        }

        // Make sure stream path meets minimum requirements (1 character for scheme, 1 character for path)
        $schemeEndPos = strpos($streamPath, '://');
        if ($schemeEndPos == 0 && strlen($streamPath) <= 3 + $schemeEndPos) {
            throw new \BadMethodCallException('Invalid stream path given.');
        }
        unset($schemeEndPos);

        // Get asset path from locator
        $assetPath = $this->locator->__invoke($streamPath); // NOTE : NO. No no no no
        if ($assetPath === false) {
            throw new FileNotFoundException("No file could be resolved for the stream path '$streamPath'.");
        }

        // Trim to relative file path.
        $assetPath = Util::stripPrefix($assetPath, $this->basePath);
        $assetPath = Util::stripPrefix($assetPath, '/'); // Remove directory separator

        // Perform prefix transformations
        if ($this->pathTransformer !== null) {
            $assetPath = $this->pathTransformer->pathToUrl($assetPath);
        }

        // NOTE : Why is this done twice ??
        $assetPath = Util::stripPrefix($assetPath, '/'); // Remove directory separator

        // Attach baseURL.
        $assetPath = $this->baseUrl . $assetPath;

        return $assetPath;
    }

    /**
     * Processes a relative path from a URL to an absolute path. Returns null if no file exists at the generated path.
     * Applies protections against attempts to access restricted files.
     *
     * @param string $uncleanRelativePath Potentially dangerous relative path.
     * @return null|string
     *
     * @todo Better unit test coverage
     */
    public function urlPathToAbsolutePath($uncleanRelativePath)
    {
        // NOTE :: Not sure this is the right way to do it. Locator is there
        // for a reason and as the right method to do this.

        // Normalize path to prevent directory traversal.
        $relativePath = Util::normalizePath($uncleanRelativePath);

        // Remove any query string.
        $relativePath = preg_replace('/\?.*/', '', $relativePath);

        // Reverse prefix transformations if required.
        if ($this->pathTransformer !== null) {
            $relativePath = $this->pathTransformer->urlToPath($relativePath);
        }

        // Make sure accessible from locator
        $allowed = false;
        foreach ($this->locatorPaths as $locatorPath) {
            if (strpos($relativePath, $locatorPath) === 0) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            return null;
        }

        // Make path absolute.
        $absolutePath = realpath($this->basePath . '/' . $relativePath);

        // Return path or null depending on existence.
        if (file_exists($absolutePath)) {
            return $absolutePath;
        } else {
            return null;
        }
    }
}