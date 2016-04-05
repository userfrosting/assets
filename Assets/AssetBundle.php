<?php

namespace UserFrosting\Assets;

/**
 * Represents an asset bundle, as defined in https://github.com/dowjones/gulp-bundle-assets
 *
 * An asset bundle can contain any number of JavascriptAssets and CssAssets.
 * Each of these can be either a raw or compiled asset.
 * @see https://github.com/dowjones/gulp-bundle-assets.
 * @author  Alexander Weissman
 * @license MIT
 */
class AssetBundle
{

    /**
     * This bundle's compiled CSS assets, indexed by bundle name.
     *
     * @var CssAsset[]
     */ 
    protected $css_assets;

    /**
     * This bundle's raw CSS assets, indexed by bundle name.
     *
     * @var CssAsset[]
     */ 
    protected $css_assets_raw;
    
    /**
     * This bundle's compiled Javascript assets, indexed by bundle name.
     *
     * @var JavascriptAsset[]
     */
    protected $js_assets;

    /**
     * This bundle's raw Javascript assets, indexed by bundle name.
     *
     * @var JavascriptAsset[]
     */  
    protected $js_assets_raw;
    
    /**
     * Adds a raw CSS asset to this bundle.
     *
     * @param CssAsset $asset
     */
    public function addRawCssAsset(CssAsset $asset)
    {
        $this->css_assets_raw[] = $asset;
    }
    
    /**
     * Adds a compiled CSS asset to this bundle.
     *
     * @param CssAsset $asset
     */    
    public function addCompiledCssAsset(CssAsset $asset)
    {
        $this->css_assets[] = $asset;
    }   
    
    /**
     * Adds a raw Javascript asset to this bundle.
     *
     * @param JavascriptAsset $asset
     */    
    public function addRawJavascriptAsset(JavascriptAsset $asset)
    {
        $this->js_assets_raw[] = $asset;
    }
    
    /**
     * Adds a compiled Javascript asset to this bundle.
     *
     * @param JavascriptAsset $asset
     */        
    public function addCompiledJavascriptAsset(JavascriptAsset $asset)
    {
        $this->js_assets[] = $asset;
    }    
    
    /**
     * Generate <script> tag(s) for Javascript assets in this asset bundle.
     *
     * If $raw is set to true, this will generate tags for the raw assets.
     * Otherwise, it will generate tag(s) for the compiled assets.
     * @param string $base_url The base url of the assets, for example https://example.com/assets/, or http://localhost/myproject/public/assets/
     * @param bool $raw
     * @return string The rendered tag(s), separated by newlines. 
     */      
    public function renderScripts($base_url, $raw = false)
    {
        $assets = $raw ? $this->js_assets_raw : $this->js_assets;
        return $this->renderAssets($assets, $base_url);
    }
    
    /**
     * Generate <link> tag(s) for CSS assets in this asset bundle.
     *
     * If $raw is set to true, this will generate tags for the raw assets.
     * Otherwise, it will generate tag(s) for the compiled assets.
     * @param string $base_url The base url of the assets, for example https://example.com/assets/, or http://localhost/myproject/public/assets/
     * @param bool $raw
     * @return string The rendered tag(s), separated by newlines.     
     */        
    public function renderStyles($base_url, $raw = false)
    {
        $assets = $raw ? $this->css_assets_raw : $this->css_assets;
        return $this->renderAssets($assets, $base_url);
    }
    
    /**
     * Generate appropriate tag(s) for a collection of assets.
     *
     * @param (JavascriptAsset|CssAsset)[] $assets The assets to render.
     * @param string $base_url The base url of the assets, for example https://example.com/assets/, or http://localhost/myproject/public/assets/
     * @return string The rendered tag(s), separated by newlines. 
     */       
    protected function renderAssets($assets, $base_url)
    {
        $result = [];
        foreach ($assets as $asset) {
            $result[] = $asset->render($base_url);
        }
        
        return implode("\n", $result);    
    }
}