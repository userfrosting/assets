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
 * Represents an asset bundle, as defined in https://github.com/dowjones/gulp-bundle-assets
 *
 * An asset bundle can contain any number of JavascriptAssets and CssAssets.
 * Each of these can be either a raw or compiled asset.
 * @see https://github.com/dowjones/gulp-bundle-assets.
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class AssetBundle
{

    /**
     * This bundle's compiled CSS assets, indexed by bundle name.
     *
     * @var CssAsset[]
     */ 
    protected $cssAssets;

    /**
     * This bundle's raw CSS assets, indexed by bundle name.
     *
     * @var CssAsset[]
     */ 
    protected $cssAssetsRaw;
    
    /**
     * This bundle's compiled Javascript assets, indexed by bundle name.
     *
     * @var JavascriptAsset[]
     */
    protected $jsAssets;

    /**
     * This bundle's raw Javascript assets, indexed by bundle name.
     *
     * @var JavascriptAsset[]
     */  
    protected $jsAssetsRaw;
    
    /**
     * AssetBundle constructor.
     *
     */
    public function __construct()
    {
        $this->cssAssets = [];
        $this->cssAssetsRaw = [];
        $this->jsAssets = [];
        $this->jsAssetsRaw = [];
    }

    /**
     * Adds a raw CSS asset to this bundle.
     *
     * @param CssAsset $asset
     */
    public function addRawCssAsset(CssAsset $asset)
    {
        $this->cssAssetsRaw[] = $asset;
    }
    
    /**
     * Adds a compiled CSS asset to this bundle.
     *
     * @param CssAsset $asset
     */    
    public function addCompiledCssAsset(CssAsset $asset)
    {
        $this->cssAssets[] = $asset;
    }   
    
    /**
     * Adds a raw Javascript asset to this bundle.
     *
     * @param JavascriptAsset $asset
     */    
    public function addRawJavascriptAsset(JavascriptAsset $asset)
    {
        $this->jsAssetsRaw[] = $asset;
    }
    
    /**
     * Adds a compiled Javascript asset to this bundle.
     *
     * @param JavascriptAsset $asset
     */        
    public function addCompiledJavascriptAsset(JavascriptAsset $asset)
    {
        $this->jsAssets[] = $asset;
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
        $assets = $raw ? $this->jsAssetsRaw : $this->jsAssets;
        return $this->renderAssets($assets, $base_url);
    }
    
    /**
     * Generate <link> tag(s) for CSS assets in this asset bundle.
     *
     * If $raw is set to true, this will generate tags for the raw assets.
     * Otherwise, it will generate tag(s) for the compiled assets.
     * @param string $baseUrl The base url of the assets, for example https://example.com/assets/, or http://localhost/myproject/public/assets/
     * @param bool $raw
     * @return string The rendered tag(s), separated by newlines.     
     */        
    public function renderStyles($baseUrl, $raw = false)
    {
        $assets = $raw ? $this->cssAssetsRaw : $this->cssAssets;
        return $this->renderAssets($assets, $baseUrl);
    }
    
    /**
     * Generate appropriate tag(s) for a collection of assets.
     *
     * @param (JavascriptAsset|CssAsset)[] $assets The assets to render.
     * @param string $baseUrl The base url of the assets, for example https://example.com/assets/, or http://localhost/myproject/public/assets/
     * @return string The rendered tag(s), separated by newlines. 
     */       
    protected function renderAssets($assets, $baseUrl)
    {
        $result = [];
        foreach ($assets as $asset) {
            $result[] = $asset->render($baseUrl);
        }
        
        return implode("\n", $result);    
    }
}
