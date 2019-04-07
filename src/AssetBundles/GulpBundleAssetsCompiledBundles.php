<?php
/**
 * UserFrosting Assets (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/assets
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Jordan Mele
 * @license   https://github.com/userfrosting/assets/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Assets\AssetBundles;

use UserFrosting\Assets\Exception\InvalidBundlesFileException;
use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\Support\Exception\JsonException;

/**
 * Represents a collection of asset bundles, loaded from a gulp-bundle-assets results file.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 * @author Jordan Mele
 */
class GulpBundleAssetsCompiledBundles extends GulpBundleAssetsBundles
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

        // Process
        foreach ($schema->all() as $bundleName => $_) {
            $styles = $schema["$bundleName.styles"];
            if (is_string($styles)) {
                $this->cssBundles[$bundleName][] = $styles;
            } elseif ($styles !== null) {
                throw new InvalidBundlesFileException("Expected styles property for '$bundleName' to be of type string but was '" . gettype($styles) . "' for '$path'");
            }

            $scripts = $schema["$bundleName.scripts"];
            if (is_string($scripts)) {
                $this->jsBundles[$bundleName][] = $scripts;
            } elseif ($scripts !== null) {
                throw new InvalidBundlesFileException("Expected scripts property for '$bundleName' to be of type string but was '" . gettype($scripts) . "' for '$path'");
            }
        }
    }
}
