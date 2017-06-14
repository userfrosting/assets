<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @package   userfrosting/assets
 * @link      https://github.com/userfrosting/assets
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Assets;

use UserFrosting\Assets\UrlBuilder\AssetUrlBuilderInterface;

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
     * @var AssetUrlBuilderInterface Url builder for constructing absolute URLs for each asset in this bundle.
     */
    protected $assetUrlBuilder;

    /**
     * @var Asset[] This bundle's CSS assets, indexed by bundle name.
     */
    protected $cssAssets;

    /**
     * @var Asset[] This bundle's Javascript assets, indexed by bundle name.
     */
    protected $jsAssets;

    /**
     * @var string
     */
    protected $name;

    /**
     * AssetBundle constructor.
     *
     * @param AssetUrlBuilderInterface $assetUrlBuilder
     * @param string $name
     */
    public function __construct(AssetUrlBuilderInterface $assetUrlBuilder, $name = "")
    {
        $this->assetUrlBuilder = $assetUrlBuilder;
        $this->cssAssets = [];
        $this->jsAssets = [];
        $this->name = $name;
    }

    /**
     * Adds a CSS asset to this bundle.
     *
     * @param Asset $asset
     */
    public function addCssAsset(Asset $asset)
    {
        $this->cssAssets[] = $asset;
        return $this;
    }

    /**
     * Adds a Javascript asset to this bundle.
     *
     * @param Asset $asset
     */
    public function addJavascriptAsset(Asset $asset)
    {
        $this->jsAssets[] = $asset;
        return $this;
    }

    /**
     * Render an asset as a JS `script` tag.
     *
     * @param Asset $asset
     * @param mixed[] $options additional attributes to be inserted in the tag.
     * @return string The rendered asset tag.
     */
    public function renderScript($asset, $options = [])
    {
        $path = $asset->getPath();
        $declarationSource = $asset->getDeclarationSource();
        $absoluteUrl = $this->assetUrlBuilder->getAssetUrl($path, $declarationSource);

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
     * @param mixed[] $options additional attributes to be inserted in the tag.
     * @return string The rendered tag(s), separated by newlines.
     */
    public function renderScripts($options = [])
    {
        $result = [];
        foreach ($this->jsAssets as $asset) {
            $result[] = $this->renderScript($asset, $options);
        }

        return implode("\n", $result);
    }

    /**
     * Render an asset as a CSS `link` tag.
     *
     * @param Asset $asset
     * @param mixed[] $options additional attributes to be inserted in the tag.
     * @return string The rendered asset tag.
     */
    public function renderStyle($asset, $options = [])
    {
        $path = $asset->getPath();
        $declarationSource = $asset->getDeclarationSource();
        $absoluteUrl = $this->assetUrlBuilder->getAssetUrl($path, $declarationSource);

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
     * @param mixed[] $options additional attributes to be inserted in the tag.
     * @return string The rendered tag(s), separated by newlines.
     */
    public function renderStyles($options = [])
    {
        $result = [];
        foreach ($this->cssAssets as $asset) {
            $result[] = $this->renderStyle($asset, $options);
        }

        return implode("\n", $result);
    }
}
