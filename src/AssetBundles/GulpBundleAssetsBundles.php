<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @package   userfrosting/assets
 * @link      https://github.com/userfrosting/assets
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Assets\AssetBundles;

use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\Support\Exception\JsonException;
use UserFrosting\Assets\Exception\InvalidBundlesFileException;

/**
 * Represents a collection of asset bundles, loaded from a gulp-bundle-assets results file.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 * @author Jordan Mele
 */
abstract class GulpBundleAssetsBundles implements AssetBundlesInterface
{
    /** @var array CSS bundles */
    protected $cssBundles;

    /** @var array JS bundles */
    protected $jsBundles;

    /**
     * Constructor
     *
     * @param string $filePath Path to gulp-bundle-assets file.
     *
     * @throws FileNotFoundException if file cannot be found.
     * @throws JsonException if file cannot be parsed as JSON.
     * @throws InvalidBundlesFileException if unexpected value encountered.
     */
    public function __construct($filePath)
    {
        if (!is_string($filePath)) {
            throw new \InvalidArgumentException("\$filePath must of type string but was " . gettype($filePath));
        }

        // Initalise bundles.
        $this->jsBundles = [];
        $this->cssBundles = [];
    }

    /**
     * @inheritDoc
     */
    public function getCssBundleAssets($bundleName = '')
    {
        if (array_key_exists($bundleName, $this->cssBundles)) {
            return $this->cssBundles[$bundleName];
        } else {
            throw new \OutOfRangeException("CSS asset bundle '$bundleName' does not exist.");
        }
    }

    /**
     * @inheritDoc
     */
    public function getJsBundleAssets($bundleName = '')
    {
        if (array_key_exists($bundleName, $this->jsBundles)) {
            return $this->jsBundles[$bundleName];
        } else {
            throw new \OutOfRangeException("JS asset bundle '$bundleName' does not exist.");
        }
    }

    /**
     * Attempts to read the schema file from provided path.
     *
     * @param string $filePath
     * @return object
     * 
     * @throws FileNotFoundException if file cannot be found.
     * @throws JsonException if file cannot be parsed as JSON.
     */
    protected function readSchema($filePath)
    {
        if (!file_exists($filePath)) {
            throw new FileNotFoundException("The bundles file '$filePath' could not be found.");
        }
        $doc = file_get_contents($filePath);
        if ($doc === false) {
            throw new FileNotFoundException("The schema '$file' could not be found.");
        }

        $schema = json_decode($doc);
        if ($schema === null) {
            throw new JsonException("The schema '$file' does not contain a valid JSON document.  JSON error: " . json_last_error());
        }

        return $schema;
    }
}
