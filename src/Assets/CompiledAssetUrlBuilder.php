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

use UserFrosting\Assets\Util;

/**
 * Represents an asset bundle, as defined in https://github.com/dowjones/gulp-bundle-assets
 *
 * An asset bundle can contain any number of JavascriptAssets and CssAssets, and represent either raw or compiled assets.
 * @see https://github.com/dowjones/gulp-bundle-assets.
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class CompiledAssetUrlBuilder implements AssetUrlBuilderInterface
{
    /**
     *  The base url for your assets, for example https://example.com/assets-raw/
     *
     * @param string
     */
    protected $baseUrl;

    /**
     * AssetBundle constructor.
     *
     * @param string $baseUrl The base url to use, for example https://example.com/assets/, or http://localhost/myproject/public/assets/
     */
    public function __construct($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
    }

    /**
     * Generate an absolute URL for an asset, by simply concatenating the baseUrl and the specified path.
     */
    public function getAssetUrl($path)
    {
        $absoluteUrl = $this->baseUrl . $path;

        return $absoluteUrl;
    }
}
