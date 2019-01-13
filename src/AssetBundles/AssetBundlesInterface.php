<?php
/**
 * UserFrosting Assets (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/assets
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Jordan Mele
 * @license   https://github.com/userfrosting/assets/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Assets\AssetBundles;

/**
 * Represents a collection of asset bundles.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 * @author Jordan Mele
 */
interface AssetBundlesInterface
{
    /**
     * Gets assets in specified CSS bundle.
     *
     * @param  string               $bundleName Name of bundle.
     * @throws \OutOfRangeException if requested bundle does not exist.
     * @return string[]
     */
    public function getCssBundleAssets($bundleName = '');

    /**
     * Gets assets in specified JS bundle.
     *
     * @param  string               $bundleName Name of bundle.
     * @throws \OutOfRangeException if requested bundle does not exist.
     * @return string[]
     */
    public function getJsBundleAssets($bundleName = '');
}
