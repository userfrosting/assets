<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/assets
 * @license   https://github.com/userfrosting/assets/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Assets\AssetBundles;

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
     * {@inheritdoc}
     * @throws FileNotFoundException       if file cannot be found.
     * @throws JsonException               if file cannot be parsed as JSON.
     * @throws InvalidBundlesFileException if unexpected value encountered.
     */
    public function __construct($path)
    {
        parent::__construct($path);

        // Read file
        $schema = $this->readSchema($path, true);

        // Process bundles.
        foreach ($schema['bundle'] as $bundleName => $_) {
            $styles = $schema["bundle.$bundleName.styles"];
            if ($styles !== null) {
                // Attempt to add CSS bundle
                try {
                    $this->cssBundles[$bundleName] = $this->standardiseBundle($styles);
                } catch (\Exception $e) {
                    throw new InvalidBundlesFileException("Encountered issue processing styles property for '$bundleName' for file '$path'", 0, $e);
                }
            }
            $scripts = $schema["bundle.$bundleName.scripts"];
            if ($scripts !== null) {
                // Attempt to add CSS bundle
                try {
                    $this->jsBundles[$bundleName] = $this->standardiseBundle($scripts);
                } catch (\Exception $e) {
                    throw new InvalidBundlesFileException("Encountered issue processing styles property for '$bundleName' for file '$path'", 0, $e);
                }
            }
        }
    }

    /**
     * Validates bundle data and returns standardised data.
     *
     * @param  string|string[] $bundle
     * @return string[]
     */
    protected function standardiseBundle($bundle)
    {
        if (is_string($bundle)) {
            return [$bundle];
        } elseif (is_array($bundle)) {
            foreach ($bundle as $asset) {
                if (!is_string($asset)) {
                    throw new \InvalidArgumentException('Input was array, so string expected but encountered ' . gettype($asset));
                }
            }
            return $bundle;
        } else {
            throw new \InvalidArgumentException('Expected string or string[] but input was ' . gettype($bundle));
        }
    }
}
