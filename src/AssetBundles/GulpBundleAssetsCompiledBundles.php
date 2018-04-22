<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @package   userfrosting/assets
 * @link      https://github.com/userfrosting/assets
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Assets\AssetBundles;

use UserFrosting\Assets\AssetBundles\GulpBundleAssetsBundles;
use UserFrosting\Assets\Exception\InvalidBundlesFileException;

/**
 * Represents a collection of asset bundles, loaded from a gulp-bundle-assets results file.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 * @author Jordan Mele
 */
class GulpBundleAssetsCompiledBundles extends GulpBundleAssetsBundles
{
    /**
     * @inheritDoc
     */
    public function __construct($filePath)
    {
        parent::__construct($filePath);

        // Read file
        $bundlesFile = $this->readSchema($filePath);

        // Process
        foreach ($bundlesFile as $bundleName => $bundleFiles) {
            if (isset($bundleFiles->styles)) {
                if (is_string($bundleFiles->styles)) {
                    $this->cssBundles[$bundleName][] = $bundleFiles->styles;
                } else {
                    throw new InvalidBundlesFileException("Expected styles property for '$bundleName' to be of type string but was " . gettype($bundleFiles->styles) . ". For '$filePath'");
                }
            }
            if (isset($bundleFiles->scripts)) {
                if (is_string($bundleFiles->scripts)) {
                    $this->jsBundles[$bundleName][] = $bundleFiles->scripts;
                } else {
                    throw new InvalidBundlesFileException("Expected scripts property for '$bundleName' to be of type string but was " . gettype($bundleFiles->scripts) . ". For '$filePath'");
                }
            }
        }
    }
}
