<?php

/*
 * UserFrosting Assets (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/assets
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Jordan Mele
 * @license   https://github.com/userfrosting/assets/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Assets\AssetBundles;

use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\Support\Exception\JsonException;
use UserFrosting\Support\Repository\Loader\YamlFileLoader;
use UserFrosting\Support\Repository\Repository;

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
     * Constructor.
     *
     * @param string $filePath Path to gulp-bundle-assets file.
     */
    public function __construct($filePath)
    {
        if (!is_string($filePath)) {
            throw new \InvalidArgumentException('$filePath must of type string but was '.gettype($filePath)); // @codeCoverageIgnore
        }

        // Initalise bundles.
        $this->jsBundles = [];
        $this->cssBundles = [];
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * @param string $path          Path to schema file.
     * @param bool   $useRepository Deprecated, will be removed in a future release. Setting this to true is recommended for an easier migration.
     *
     * @throws FileNotFoundException if file cannot be found.
     * @throws JsonException         if file cannot be parsed as JSON.
     *
     * @return mixed|Repository Returns file contents parsed by json_decode or a Repository if $useRepository is true.
     */
    protected function readSchema($path, $useRepository = false)
    {
        if ($useRepository === true) {
            // Read schmea using Repository
            try {
                $loader = new YamlFileLoader($path);

                return new Repository($loader->load(false));
            } catch (FileNotFoundException $e) {
                throw new FileNotFoundException('The schema file could not be found.', 0, $e);
            } catch (JsonException $e) {
                throw new JsonException('The schema file could not be found.', 0, $e);
            }
        } else {
            // @codeCoverageIgnoreStart
            // This code path exists to facilitate an easier migration to v5 of this package
            // It cannot be properly tested here without increasing technical debt substantially and such such it will be removed in the future
            trigger_error('Usage of \'readSchema\' with repository result disabled is deprecated and will be removed in future releases.', E_USER_DEPRECATED);

            // Read schema without abstractions
            if (!file_exists($path)) {
                throw new FileNotFoundException("The schema '$path' could not be found.");
            }

            $doc = file_get_contents($path);
            if ($doc === false) {
                throw new FileNotFoundException("The schema '$path' could not be found.");
            }

            $schema = json_decode($doc);
            if ($schema === null) {
                throw new JsonException("The schema '$path' does not contain a valid JSON document.  JSON error: ".json_last_error());
            }

            return $schema;
            // @codeCoverageIgnoreEnd
        }
    }
}
