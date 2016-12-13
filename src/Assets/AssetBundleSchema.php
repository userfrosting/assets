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

use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\Support\Exception\JsonException;

/**
 * Asset bundle schema class.  An asset bundle schema contains information about one or more asset bundles.
 *
 * Assumes a schema format compatible with gulp-bundle-assets.
 * @see https://github.com/dowjones/gulp-bundle-assets.
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class AssetBundleSchema
{
    /**
     * The array of AssetBundle items that this schema represents.
     *
     * @var array
     */
    protected $bundles;

    /**
     * Gets a bundle from this schema.
     *
     * @param string $bundle The name of the bundle.  E.g. "js/main".
     * @return AssetBundle The requested bundle object.
     */
    public function get($bundle)
    {
        if (isset($this->bundles[$bundle])) {
            return $this->bundles[$bundle];
        } else {
            throw new \OutOfBoundsException("Bundle '$bundle' not found in loaded bundles.");
        }
    }

    /**
     * Load a JSON schema file that describes compiled asset bundles, populating compiled asset info for this schema's bundles.
     *
     * Loads information about compiled asset bundles from a JSON file (e.g. bundle.result.json).
     * The format of this file should match the output of gulp-bundle-assets
     * @see https://github.com/dowjones/gulp-bundle-assets
     * @param string $file Path to the schema file.
     * @todo See how this behaves when called multiple times on different files.  It should merge in multiple bundle schemas.
     * @todo Support linting of JSON documents?
     */
    public function loadCompiledSchemaFile($file)
    {
        $schema = $this->readSchema($file);
        $this->loadBundles($schema);
    }

    /**
     * Load a JSON schema file that describes raw asset bundles, populating raw asset info for this schema's bundles.
     *
     * Loads information about raw asset bundles from a JSON file (e.g. bundle.config.json).
     * The format of this file should match the input format of gulp-bundle-assets
     * @see https://github.com/dowjones/gulp-bundle-assets
     * @param string $file Path to the schema file.
     * @todo See how this behaves when called multiple times on different files.  It should merge in multiple bundle schemas.
     * @todo Support linting of JSON documents?
     */
    public function loadRawSchemaFile($file)
    {
        $schema = $this->readSchema($file);

        if (!isset($schema['bundle'])) {
            throw new \OutOfBoundsException("The specified JSON document does not contain a 'bundle' key.");
        }

        $this->loadBundles($schema['bundle']);
    }

    /**
     * Load a JSON object (as an associative array) that describes asset bundles, populating info for this schema's bundles.
     *
     * The format of this object should match the formats described for bundles in gulp-bundle-assets
     * @see https://github.com/dowjones/gulp-bundle-assets
     * @param array $schema An associative array (usually converted from a JSON object)
     * @todo See how this behaves when called multiple times on different schema.  It should merge in multiple bundle schemas.
     */
    protected function loadBundles($schema)
    {
        foreach ($schema as $bundleName => $bundleSchema) {
            if (!isset($this->bundles[$bundleName])) {
                $this->bundles[$bundleName] = new AssetBundle();
            }

            // TODO: can a bundle be defined as a string instead of an object/array?

            // Load scripts
            if (isset($bundleSchema['scripts'])) {
                if (is_array($bundleSchema['scripts'])) {
                    foreach ($bundleSchema['scripts'] as $script) {
                        $this->addBundleScript($this->bundles[$bundleName], $script);
                    }
                } elseif (is_string($bundleSchema['scripts'])) {
                    $this->addBundleScript($this->bundles[$bundleName], $bundleSchema['scripts']);
                }
            }

            // Load styles
            if (isset($bundleSchema['styles'])) {
                if (is_array($bundleSchema['styles'])) {
                    foreach ($bundleSchema['styles'] as $style) {
                        $this->addBundleStyle($this->bundles[$bundleName], $style);
                    }
                } elseif (is_string($bundleSchema['styles'])) {
                    $this->addBundleStyle($this->bundles[$bundleName], $bundleSchema['styles']);
                }
            }

            // TODO: load options

        }
    }

    /**
     * Add a Javascript asset element to a bundle.
     *
     * @param AssetBundle $bundle A reference to the target bundle.
     * @param array|string $schema A string or associative array containing this asset's info (usually converted from a JSON object)
     */
    protected function addBundleScript(&$bundle, $schema)
    {
        if (is_array($schema) and isset($schema['src'])) {
            $asset = new Asset($schema['src']);
        } elseif (is_string($schema)) {
            $asset = new Asset($schema);
        } else {
            return;
        }

        $bundle->addJavascriptAsset($asset);
    }

    /**
     * Add a CSS asset element to a bundle.
     *
     * @param AssetBundle $bundle A reference to the target bundle.
     * @param array|string $schema A string or associative array containing this asset's info (usually converted from a JSON object)
     */
    protected function addBundleStyle(&$bundle, $schema)
    {
        if (is_array($schema) and isset($schema['src'])) {
            $asset = new Asset($schema['src']);
        } elseif (is_string($schema)) {
            $asset = new Asset($schema);
        } else {
            return;
        }

        $bundle->addCssAsset($asset);
    }

    protected function readSchema($file)
    {
        $doc = file_get_contents($file);
        if ($doc === false) {
            throw new FileNotFoundException("The schema '$file' could not be found.");
        }

        $schema = json_decode($doc, true);
        if ($schema === null) {
            throw new JsonException("The schema '$file' does not contain a valid JSON document.  JSON error: " . json_last_error());
        }

        return $schema;
    }
}