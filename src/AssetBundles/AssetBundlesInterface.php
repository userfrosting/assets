<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @package   userfrosting/assets
 * @link      https://github.com/userfrosting/assets
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
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
     * @param string $bundleName Name of bundle.
     * @return string[]
     *
     * @throws \OutOfRangeException if requested bundle does not exist.
     */
    public function getCssBundleAssets($bundleName = '');

    /**
     * Gets assets in specified JS bundle.
     *
     * @param string $bundleName Name of bundle.
     * @return string[]
     *
     * @throws \OutOfRangeException if requested bundle does not exist.
     */
    public function getJsBundleAssets($bundleName = '');
}