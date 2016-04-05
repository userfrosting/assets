<?php

namespace UserFrosting\Assets;

/**
 * Asset management class.  Handles rendering of asset reference tags (<link>, <script>, etc) in HTML.
 *
 * Compatible with Twig via the UserFrostingExtension, or by simply passing in your AssetManager as a global Twig variable.
 * @author  Alexander Weissman
 * @license MIT
 */
class AssetManager
{

    /**
     *  The base url of the site, for example https://example.com, or http://localhost/myproject/public
     *
     * @param string
     */
    protected $base_url;
    
    /**
     * Decides whether AssetManager should render raw versions of asset bundles.
     * If set to `true`, this overrides any bundle-specific setting.
     *
     * @var bool
     */
    protected $use_raw_assets;
    
    /**
     * The URL path fragment to use for rendering compiled assets, relative to $base_url.
     *
     * @var string
     */    
    protected $assets_path;
    
    /**
     * The URL path fragment to use for rendering raw assets, relative to $base_url.
     *
     * @var string
     */        
    protected $raw_assets_path;
    
    /**
     * The bundle schema to use for rendering calls to `js()` and `css()`.
     *
     * @var AssetBundleSchema
     */
    protected $bundle_schema;
    
    /**
     * AssetManager constructor.
     *
     * @param string $base_url The base url of the site, for example https://example.com, or http://localhost/myproject/public
     * @param bool $use_raw_assets Decides whether AssetManager should render raw versions of asset bundles.
     */
    public function __construct($base_url, $use_raw_assets = false)
    {
        $this->base_url = rtrim($base_url, '/') . '/';
        $this->use_raw_assets = $use_raw_assets;
    }
    
    /**
     * Set the bundle schema to be used for rendering calls to `js()`, `css()`, `url()`, etc.
     *
     * @param AssetBundleSchema
     */
    public function setBundleSchema($bundle_schema)
    {
        $this->bundle_schema = $bundle_schema;
    }
    
    /**
     * Set the desired url path for compiled assets, relative to the base_url.  For example, "assets".
     *
     * This should match an actual path in your public directory, to allow compiled assets to be directly served by your web server.
     * Allowing your compiled assets to be served directly, without going through the Slim app, will make the response much faster.
     * @param string $assets_path
     */
    public function setCompiledAssetsPath($assets_path)
    {
        $this->assets_path = rtrim($assets_path, '/') . '/';
    }
    
    /**
     * Set the desired url path for raw assets, relative to the base_url.  For example, "assets-raw".
     *
     * @param string $raw_assets_path
     */
    public function setRawAssetsPath($raw_assets_path)
    {
        $this->raw_assets_path = rtrim($raw_assets_path, '/') . '/';
    }
    
    /**
     * Generate <script> tag(s) for Javascript assets in an asset bundle.
     *
     * If $raw is set to true, or the asset manager has been set to $use_raw_assets, this will generate tags for the raw assets.
     * Otherwise, it will generate tag(s) for the compiled assets.
     *
     * @param string $bundle_name
     * @param bool $raw
     * @return string The rendered HTML tag(s).
     */     
    public function js($bundle_name = 'js/main', $raw = false)
    {
        if ($this->use_raw_assets || $raw) {
            return $this->bundle_schema->get($bundle_name)->renderScripts($this->base_url . $this->raw_assets_path, true);
        } else {
            return $this->bundle_schema->get($bundle_name)->renderScripts($this->base_url . $this->assets_path, false);
        }
    }
      
    /**
     * Generate <link> tag(s) for CSS assets in an asset bundle.
     *
     * If $raw is set to true, or the asset manager has been set to $use_raw_assets, this will generate tags for the raw assets.
     * Otherwise, it will generate tag(s) for the compiled assets.
     *
     * @param string $bundle_name
     * @param bool $raw
     * @return string The rendered HTML tag(s).     
     */         
    public function css($bundle_name = 'css/main', $raw = false)
    {
        if ($this->use_raw_assets || $raw) {
            return $this->bundle_schema->get($bundle_name)->renderStyles($this->base_url . $this->raw_assets_path, true);
        } else {
            return $this->bundle_schema->get($bundle_name)->renderStyles($this->base_url . $this->assets_path, false);
        }
    }
      
    /**
     * Get the absolute url for an asset as specified by a stream path (e.g. "assets://css/bootstrap.css").
     * If $raw is set to true, or the asset manager has been set to $use_raw_assets, this will return a url for the raw version of the asset.
     * Otherwise, it will return a url pointing to the compiled asset.
     *
     * @param string $stream_path
     * @param bool $raw
     * @return string The absolute URL.
     */    
    public function url($stream_path, $raw = false) {
        if ($this->use_raw_assets || $raw) {
            return $this->getAbsoluteUrl($this->base_url . $this->raw_assets_path, $stream_path);
        } else {
            return $this->getAbsoluteUrl($this->base_url . $this->assets_path, $stream_path);
        }
    }
    
    /**
     * Get the absolute url for an asset as specified by a stream path (e.g. "assets://css/bootstrap.css"), relative to a base URL.
     *
     * If the stream is `http` or `https`, this simply returns the full URL.
     * @param string $base_url The base url to use when constructing the full URL.
     * @param string $stream_path The stream path to resolve.
     * @return string The absolute URL.
     */    
    protected function getAbsoluteUrl($base_url, $stream_path)
    {
        // Resolve the asset URL (either a stream or absolute URL)
        if (is_array($stream_path)) {
            // Support stream lookup in ['asset', 'path/to'] format.
            if (count($stream_path) != 2) {
                throw new \BadMethodCallException('Invalid stream path given.');
            }
            $resolved_scheme = strtolower($stream_path[0]);
            $resolved_path = $stream_path[1];
        } elseif (strstr($stream_path, '://')) {
            // Support stream lookup in 'asset://path/to' format.
            $stream = explode('://', $stream_path, 2);
            $resolved_scheme = strtolower($stream[0]);
            $resolved_path = $stream[1];
        } else {
            throw new \BadMethodCallException('Invalid stream path given.');
        }
        
        if ($resolved_scheme == "http" || $resolved_scheme == "https"){
            return $stream_path;
        } else {            
            return $base_url . $resolved_path;
        }
    }
}
