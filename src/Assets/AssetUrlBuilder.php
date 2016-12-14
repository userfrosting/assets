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
class AssetUrlBuilder
{
    protected $locator;

    /**
     *  The base url for your assets, for example https://example.com/assets-raw/
     *
     * @param string
     */
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
        $this->locator = $locator;

        $this->baseUrl = rtrim($baseUrl, '/') . '/';

        $this->removePrefix = $removePrefix ? rtrim($removePrefix, "/\\") . '/' : '';

        $this->scheme = $scheme;
    }

    /**
     * Generate an absolute URL for an asset, based on the asset path and the bundle's baseUrl.
     */
    public function getAssetUrl($path)
    {
        $relativeUrl = $this->locator->findResource($this->scheme . '://' . $path, false);

        if ($relativeUrl) {
            $relativeUrl = Util::stripPrefix($relativeUrl, $this->removePrefix);
            $absoluteUrl = $this->baseUrl . $relativeUrl;
        } else {
            $absoluteUrl = '';
        }

        return $absoluteUrl;
    }
}
