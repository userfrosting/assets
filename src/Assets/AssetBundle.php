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
use UserFrosting\Assets\Util;

/**
 * Represents an asset bundle, as defined in https://github.com/dowjones/gulp-bundle-assets
 *
 * An asset bundle can contain any number of JavascriptAssets and CssAssets, and represent either raw or compiled assets.
 * @see https://github.com/dowjones/gulp-bundle-assets.
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class AssetBundle
{

    /**
     * This bundle's CSS assets, indexed by bundle name.
     *
     * @var CssAsset[]
     */
    protected $cssAssets;

    /**
     * This bundle's Javascript assets, indexed by bundle name.
     *
     * @var JavascriptAsset[]
     */
    protected $jsAssets;

    protected $locator;

    protected $baseUrl;

    protected $removePrefix;

    protected $scheme;

    /**
     * AssetBundle constructor.
     *
     * @param string $baseUrl The base url to use, for example https://example.com/assets/, or http://localhost/myproject/public/assets/
     */
    public function __construct(UniformResourceLocator $locator, $baseUrl, $removePrefix = '', $scheme = 'assets')
    {
        $this->cssAssets = [];
        $this->jsAssets = [];

        $this->locator = $locator;

        $this->baseUrl = rtrim($baseUrl, '/') . '/';

        $this->removePrefix = $removePrefix ? rtrim($removePrefix, "/\\") . '/' : '';

        $this->scheme = $scheme;
    }

    /**
     * Adds a CSS asset to this bundle.
     *
     * @param Asset $asset
     */
    public function addCssAsset(Asset $asset)
    {
        $this->cssAssets[] = $asset;
    }

    /**
     * Adds a Javascript asset to this bundle.
     *
     * @param Asset $asset
     */
    public function addJavascriptAsset(Asset $asset)
    {
        $this->jsAssets[] = $asset;
    }

    /**
     * Generate an absolute URL for an asset, based on the asset path and the bundle's baseUrl.
     */
    public function getAssetUrl($path)
    {
        $relativeUrl = $this->locator->findResource($this->scheme . '://' . $path, false);

        if ($relativeUrl) {
            error_log("Stripping {$this->removePrefix} from $relativeUrl");
            $relativeUrl = Util::stripPrefix($relativeUrl, $this->removePrefix);
            $absoluteUrl = $this->baseUrl . $relativeUrl;
        } else {
            $absoluteUrl = '';
        }

        return $absoluteUrl;
    }

    /**
     * Render an asset as a JS `script` tag.
     *
     * @return string The rendered asset tag.
     */
    public function renderScript($asset)
    {
        $path = $asset->getPath();
        $absoluteUrl = $this->getAssetUrl($path);

        $options = $asset->getOptions();

        $attributes = [];

        if (isset($options['async']) && $options['async'] = true) {
            $attributes[] = 'async';
        }

        if (isset($options['defer']) && $options['defer'] = true) {
            $attributes[] = 'defer';
        }

        if (isset($options['id'])) {
            $attributes[] = 'id="' . $options['id'] . '"';
        }

        if (isset($options['type'])) {
            $attributes[] = 'type="' . $options['type'] . '"';
        }

        return '<script src="' . $absoluteUrl . '" ' . implode(' ', $attributes) . '></script>';
    }

    /**
     * Generate <script> tag(s) for Javascript assets in this asset bundle.
     *
     * @param string $baseUrl The base url of the assets, for example https://example.com/assets/, or http://localhost/myproject/public/assets/
     * @return string The rendered tag(s), separated by newlines.
     */
    public function renderScripts()
    {
        $result = [];
        foreach ($this->jsAssets as $asset) {
            $result[] = $this->renderScript($asset);
        }

        return implode("\n", $result);
    }

    /**
     * Render an asset as a CSS `link` tag.
     *
     * @return string The rendered asset tag.
     */
    public function renderStyle($asset)
    {
        $path = $asset->getPath();
        $absoluteUrl = $this->getAssetUrl($path);

        $options = $asset->getOptions();

        $attributes = [];

        if (isset($options['id'])) {
            $attributes[] = 'id="' . $options['id'] . '"';
        }

        if (isset($options['media'])) {
            $attributes[] = 'media="' . $options['media'] . '"';
        }

        $rel = isset($options['rel']) ? $options['rel'] : "stylesheet";
        $type = isset($options['type']) ? $options['type'] : "text/css";

        return '<link rel="' . $rel . '" type="' . $type . '" href="' . $absoluteUrl . '" ' . implode(' ', $attributes) . '>';
    }

    /**
     * Generate <link> tag(s) for CSS assets in this asset bundle.
     *
     * @return string The rendered tag(s), separated by newlines.
     */
    public function renderStyles()
    {
        $result = [];
        foreach ($this->cssAssets as $asset) {
            $result[] = $this->renderStyle($asset);
        }

        return implode("\n", $result);
    }
}
