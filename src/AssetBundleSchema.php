<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @package   userfrosting/assets
 * @link      https://github.com/userfrosting/assets
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Assets;

use UserFrosting\Assets\UrlBuilder\AssetUrlBuilderInterface;
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
     * @var AssetBundle[] The array of AssetBundle items that this schema represents.
     */
    protected $bundles;

    /**
     * @var AssetUrlBuilderInterface Url builder for constructing absolute URLs for each asset in this schema.
     */
    protected $assetUrlBuilder;

    /**
     * AssetBundleSchema constructor.
     *
     * @param AssetUrlBuilderInterface $assetUrlBuilder
     */
    public function __construct(AssetUrlBuilderInterface $assetUrlBuilder)
    {
        $this->assetUrlBuilder = $assetUrlBuilder;
    }

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
     * @todo Support linting of JSON documents?
     */
    public function loadRawSchemaFile($file)
    {
        $schema = $this->readSchema($file);

        if (!isset($schema['bundle'])) {
            throw new \OutOfBoundsException("The specified JSON document does not contain a 'bundle' key.");
        }

        $this->loadBundles($schema['bundle'], $file);
    }

    /**
     * Load a JSON object (as an associative array) that describes asset bundles, populating info for this schema's bundles.
     *
     * The format of this object should match the formats described for bundles in gulp-bundle-assets
     * @see https://github.com/dowjones/gulp-bundle-assets
     * @param mixed[] $schema An associative array (usually converted from a JSON object)
     * @param string $fileName The config file in which this bundle is defined.
     * @todo can a bundle be defined as a string instead of an object/array?
     */
    protected function loadBundles($schema, $fileName = '')
    {
        foreach ($schema as $bundleName => $bundleSchema) {
            // Construct full declaration source
            $declarationSource = $fileName . " [$bundleName]";

            if (!isset($this->bundles[$bundleName])) {
                $this->bundles[$bundleName] = new AssetBundle($this->assetUrlBuilder, $bundleName);
            } else {
                // Bundle already defined, handle as per collision rules.
                $collisionRule = (isset($bundleSchema['options']['sprinkle']['onCollision']) ? $bundleSchema['options']['sprinkle']['onCollision'] : 'replace');
                switch ($collisionRule) {
                    case 'replace':
                        $this->bundles[$bundleName] = new AssetBundle($this->assetUrlBuilder, $bundleName);
                        break;
                    case 'merge':
                        // Nothing extra needs to be done.
                        // This simply exists to prevent falling through to default.
                        break;
                    case 'ignore':
                        unset($bundleSchema);
                        break;
                    case 'error':
                        throw new \ErrorException("The bundle '$bundleName' is already defined.");
                        break;
                    default:
                        throw new \OutOfBoundsException("Invalid value '$collisionRule' provided for 'onCollision' key in bundle '$bundleName'.");
                        break;
                }
            }

            // Load scripts
            if (isset($bundleSchema['scripts'])) {
                if (is_array($bundleSchema['scripts'])) {
                    foreach ($bundleSchema['scripts'] as $script) {
                        $this->addBundleScript($this->bundles[$bundleName], $script, $declarationSource);
                    }
                } elseif (is_string($bundleSchema['scripts'])) {
                    $this->addBundleScript($this->bundles[$bundleName], $bundleSchema['scripts'], $declarationSource);
                }
            }

            // Load styles
            if (isset($bundleSchema['styles'])) {
                if (is_array($bundleSchema['styles'])) {
                    foreach ($bundleSchema['styles'] as $style) {
                        $this->addBundleStyle($this->bundles[$bundleName], $style, $declarationSource);
                    }
                } elseif (is_string($bundleSchema['styles'])) {
                    $this->addBundleStyle($this->bundles[$bundleName], $bundleSchema['styles'], $declarationSource);
                }
            }
        }
    }

    /**
     * Add a Javascript asset element to a bundle.
     *
     * @param AssetBundle $bundle A reference to the target bundle.
     * @param mixed[]|string $schema A string or associative array containing this asset's info (usually converted from a JSON object)
     * @param string $declarationSource A string describing the config file and bundle in which this asset was referenced.
     */
    protected function addBundleScript(&$bundle, $schema, $declarationSource = '')
    {
        if (is_array($schema) && isset($schema['src'])) {
            $asset = new Asset($schema['src'], $declarationSource);
        } elseif (is_string($schema)) {
            $asset = new Asset($schema, $declarationSource);
        } else {
            return;
        }

        $bundle->addJavascriptAsset($asset);
    }

    /**
     * Add a CSS asset element to a bundle.
     *
     * @param AssetBundle $bundle A reference to the target bundle.
     * @param mixed[]|string $schema A string or associative array containing this asset's info (usually converted from a JSON object)
     * @param string $declarationSource A string describing the config file and bundle in which this asset was referenced.
     */
    protected function addBundleStyle(&$bundle, $schema, $declarationSource = '')
    {
        if (is_array($schema) && isset($schema['src'])) {
            $asset = new Asset($schema['src'], $declarationSource);
        } elseif (is_string($schema)) {
            $asset = new Asset($schema, $declarationSource);
        } else {
            return;
        }

        $bundle->addCssAsset($asset);
    }

    /**
     * Load a JSON schema file that describes asset bundles.
     *
     * @param string $file Path to the schema file.
     * @return mixed[]
     */
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
