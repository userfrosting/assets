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
use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\Support\Exception\JsonException;
use UserFrosting\Assets\Exception\InvalidBundlesFileException;

/**
 * Represents a collection of asset bundles, loaded from a gulp-bundle-assets configuration file.
 * 
 * @author Alex Weissman (https://alexanderweissman.com)
 * @author Jordan Mele
 * 
 * @todo Many of the more advanced features available in gulp-bundle-assets configuration are not supported. (EG: Specifying the pre-minified versions of assets)
 */
class GulpBundleAssetsRawBundles extends GulpBundleAssetsBundles
{
    /**
     * @inheritDoc
     */
    public function __construct($filePath)
    {
        parent::__construct($filePath);

        // Read file
        $bundlesFile = $this->readSchema($filePath);

        // Process bundles.
        if (isset($bundlesFile->bundle)) {
            foreach ($bundlesFile->bundle as $bundleName => $bundle) {
                if (isset($bundle->styles)) {
                    // Attempt to add CSS bundle
                    try {
                        $this->cssBundles[$bundleName] = $this->standardiseBundle($bundle->styles);
                    }
                    catch (\Exception $e) {
                        throw new InvalidBundlesFileException("Encountered issue processing styles property for '$bundleName' for file '$filePath'", 0, $e);
                    }
                }
                if (isset($bundle->scripts)) {
                    // Attempt to add JS bundle
                    try {
                        $this->jsBundles[$bundleName] = $this->standardiseBundle($bundle->scripts);
                    }
                    catch (\Exception $e) {
                        throw new InvalidBundlesFileException("Encountered issue processing scripts property for '$bundleName' for file '$filePath'", 0, $e);
                    }
                }
            }
        }
    }

    
    /**
     * Validates bundle data and returns standardised data.
     *
     * @param string|string[] $data
     * @return string[]
     */
    protected function standardiseBundle($bundle)
    {
        if (is_string($bundle)) {
            return [$bundle];
        } elseif (is_array($bundle)) {
            foreach ($bundle as $asset) {
                if (!is_string($asset)) {
                    throw new \InvalidArgumentException("Input was array, so string expected but encountered " . gettype($asset));
                }
            }
            return $bundle;
        } else {
            throw new \InvalidArgumentException("Expected string or string[] but input was " . gettype($bundle));
        }
    }
}