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
 
/**
 * Asset management class.  Handles rendering of asset reference tags (<link>, <script>, etc) in HTML.
 *
 * Compatible with Twig via the UserFrostingExtension, or by simply passing in your AssetManager as a global Twig variable.
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class AssetManager
{

    /**
     *  The base url of the site, for example https://example.com, or http://localhost/myproject/public
     *
     * @param string
     */
    protected $baseUrl;
    
    /**
     * Decides whether AssetManager should render raw versions of asset bundles.
     * If set to `true`, this overrides any bundle-specific setting.
     *
     * @var bool
     */
    protected $useRawAssets;
    
    /**
     * The URL path fragment to use for rendering compiled assets, relative to $baseUrl.
     *
     * @var string
     */    
    protected $assetsPath;
    
    /**
     * The URL path fragment to use for rendering raw assets, relative to $baseUrl.
     *
     * @var string
     */        
    protected $rawAssetsPath;
    
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
     * @param bool $useRawAssets Decides whether AssetManager should render raw versions of asset bundles.
     */
    public function __construct($baseUrl, $useRawAssets = false)
    {
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
        $this->useRawAssets = $useRawAssets;
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
     * Set the desired url path for compiled assets, relative to the base_url.  For example, "assets".
     *
     * This should match an actual path in your public directory, to allow compiled assets to be directly served by your web server.
     * Allowing your compiled assets to be served directly, without going through the Slim app, will make the response much faster.
     * @param string $assetsPath
     */
    public function setCompiledAssetsPath($assetsPath)
    {
        $this->assetsPath = rtrim($assetsPath, '/') . '/';
    }
    
    /**
     * Set the desired url path for raw assets, relative to the base_url.  For example, "assets-raw".
     *
     * @param string $rawAssetsPath
     */
    public function setRawAssetsPath($rawAssetsPath)
    {
        $this->rawAssetsPath = rtrim($rawAssetsPath, '/') . '/';
    }
    
    /**
     * Generate <script> tag(s) for Javascript assets in an asset bundle.
     *
     * If $raw is set to true, or the asset manager has been set to $useRawAssets, this will generate tags for the raw assets.
     * Otherwise, it will generate tag(s) for the compiled assets.
     *
     * @param string $bundleName
     * @param bool $raw
     * @return string The rendered HTML tag(s).
     */     
    public function js($bundleName = 'js/main', $raw = false)
    {
        if ($this->useRawAssets || $raw) {
            return $this->bundleSchema->get($bundleName)->renderScripts($this->baseUrl . $this->rawAssetsPath, true);
        } else {
            return $this->bundleSchema->get($bundleName)->renderScripts($this->baseUrl . $this->assetsPath, false);
        }
    }
      
    /**
     * Generate <link> tag(s) for CSS assets in an asset bundle.
     *
     * If $raw is set to true, or the asset manager has been set to $useRawAssets, this will generate tags for the raw assets.
     * Otherwise, it will generate tag(s) for the compiled assets.
     *
     * @param string $bundleName
     * @param bool $raw
     * @return string The rendered HTML tag(s).     
     */         
    public function css($bundleName = 'css/main', $raw = false)
    {
        if ($this->useRawAssets || $raw) {
            return $this->bundleSchema->get($bundleName)->renderStyles($this->baseUrl . $this->rawAssetsPath, true);
        } else {
            return $this->bundleSchema->get($bundleName)->renderStyles($this->baseUrl . $this->assetsPath, false);
        }
    }
      
    /**
     * Get the absolute url for an asset as specified by a stream path (e.g. "assets://css/bootstrap.css").
     * If $raw is set to true, or the asset manager has been set to $useRawAssets, this will return a url for the raw version of the asset.
     * Otherwise, it will return a url pointing to the compiled asset.
     *
     * @param string $streamPath
     * @param bool $raw
     * @return string The absolute URL.
     */    
    public function url($streamPath, $raw = false) {
        if ($this->useRawAssets || $raw) {
            return $this->getAbsoluteUrl($this->baseUrl . $this->rawAssetsPath, $streamPath);
        } else {
            return $this->getAbsoluteUrl($this->baseUrl . $this->assetsPath, $streamPath);
        }
    }
    
    /**
     * Get the absolute url for an asset as specified by a stream path (e.g. "assets://css/bootstrap.css"), relative to a base URL.
     *
     * If the stream is `http` or `https`, this simply returns the full URL.
     * @param string $baseUrl The base url to use when constructing the full URL.
     * @param string $streamPath The stream path to resolve.
     * @return string The absolute URL.
     */    
    protected function getAbsoluteUrl($baseUrl, $streamPath)
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
            return $baseUrl . $resolvedPath;
        }
    }
}
