<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @package   userfrosting/assets
 * @link      https://github.com/userfrosting/assets
 * @copyright Copyright (c) 2013-2016 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */ 
namespace UserFrosting\Assets;

use RocketTheme\Toolbox\ResourceLocator\UniformResourceLocator;

/**
 * Asset management class.  Handles rendering of asset reference tags (<link>, <script>, etc) in HTML.
 *
 * Compatible with Twig via the UserFrostingExtension, or by simply passing in your AssetManager as a global Twig variable.
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class AssetManager
{

    /**
     *  The base url for your assets, for example https://example.com/assets-raw/
     *
     * @param string
     */
    protected $baseUrl;

    protected $locator;
    
    /**
     * The bundle schema to use for rendering calls to `js()` and `css()`.
     *
     * @var AssetBundleSchema
     */
    protected $bundleSchema;
    
    /**
     * AssetManager constructor.
     *
     * @param string $baseUrl The base url of the site, for example https://example.com, or http://localhost/myproject/public
     */
    public function __construct(UniformResourceLocator $locator, $baseUrl)
    {
        $this->locator = $locator;

        $this->baseUrl = rtrim($baseUrl, "/\\") . '/';
    }
    
    /**
     * Set the bundle schema to be used for rendering calls to `js()`, `css()`, `url()`, etc.
     *
     * @param AssetBundleSchema
     */
    public function setBundleSchema($bundleSchema)
    {
        $this->bundleSchema = $bundleSchema;
    }
    
    /**
     * Set the desired url path for assets, relative to the baseUrl.  For example, "assets".
     *
     * @param string $assetsPath
     */
    public function setAssetsPath($assetsPath)
    {
        $this->assetsPath = rtrim($assetsPath, '/') . '/';
    }

    /**
     * Generate <script> tag(s) for Javascript assets in an asset bundle.
     *
     * @param string $bundleName
     * @return string The rendered HTML tag(s).
     */     
    public function js($bundleName = 'js/main')
    {
        return $this->bundleSchema->get($bundleName)->renderScripts();
    }
      
    /**
     * Generate <link> tag(s) for CSS assets in an asset bundle.
     *
     * @param string $bundleName
     * @return string The rendered HTML tag(s).     
     */         
    public function css($bundleName = 'css/main')
    {
        return $this->bundleSchema->get($bundleName)->renderStyles();
    }
      
    /**
     * Get the absolute url for an asset as specified by a stream path (e.g. "assets://css/bootstrap.css").
     *
     * @param string $streamPath
     * @return string The absolute URL.
     */    
    public function url($streamPath)
    {
        return $this->getAbsoluteUrl($streamPath);
    }
    
    /**
     * Get the absolute url for an asset as specified by a stream path (e.g. "assets://css/bootstrap.css"), relative to a base URL.
     *
     * If the stream is `http` or `https`, this simply returns the full URL.
     * @param string $baseUrl The base url to use when constructing the full URL.
     * @param string $streamPath The stream path to resolve.
     * @return string The absolute URL.
     */    
    protected function getAbsoluteUrl($streamPath)
    {
        // Resolve the asset URL (either a stream or absolute URL)
        if (is_array($streamPath)) {
            // Support stream lookup in ['asset', 'path/to'] format.
            if (count($streamPath) != 2) {
                throw new \BadMethodCallException('Invalid stream path given.');
            }
            $resolvedScheme = strtolower($streamPath[0]);
            $resolvedPath = $streamPath[1];
        } elseif (strstr($streamPath, '://')) {
            // Support stream lookup in 'asset://path/to' format.
            $stream = explode('://', $streamPath, 2);
            $resolvedScheme = strtolower($stream[0]);
            $resolvedPath = $stream[1];
        } else {
            throw new \BadMethodCallException('Invalid stream path given.');
        }
        
        if ($resolvedScheme == "http" || $resolvedScheme == "https") {
            return $streamPath;
        } else {
            // Use the relative path to the resource as the URL
            $relativeUrl = $this->locator->findResource($streamPath, false);
    
            if ($relativeUrl) {
                $absoluteUrl = $this->baseUrl . $relativeUrl;
            } else {
                $absoluteUrl = '';
            }
    
            return $absoluteUrl;
        }
    }
}