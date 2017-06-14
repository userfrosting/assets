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

/**
 * Asset management class.  Handles rendering of asset reference tags (<link>, <script>, etc) in HTML.
 *
 * Compatible with Twig via the UserFrostingExtension, or by simply passing in your AssetManager as a global Twig variable.
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class AssetManager
{
    /**
     * @var AssetUrlBuilderInterface Url builder for constructing absolute URLs for each asset in this bundle.
     */
    protected $assetUrlBuilder;

    /**
     * @var AssetBundleSchema The bundle schema to use for rendering calls to `js()` and `css()`.
     */
    protected $bundleSchema;

    /**
     * AssetManager constructor.
     *
     * @param AssetUrlBuilderInterface $assetUrlBuilder
     * @param AssetBundleSchema $bundleSchema
     */
    public function __construct(AssetUrlBuilderInterface $assetUrlBuilder, AssetBundleSchema $bundleSchema)
    {
        $this->assetUrlBuilder = $assetUrlBuilder;
        $this->setBundleSchema($bundleSchema);
    }

    /**
     * Set the bundle schema to be used for rendering calls to `js()`, `css()`, `url()`, etc.
     *
     * @param AssetBundleSchema $bundleSchema
     */
    public function setBundleSchema($bundleSchema)
    {
        $this->bundleSchema = $bundleSchema;
    }

    /**
     * Generate <script> tag(s) for Javascript assets in an asset bundle.
     *
     * @param string $bundleName
     * @param mixed[] $options
     * @return string The rendered HTML tag(s).
     */
    public function js($bundleName = 'js/main', $options = [])
    {
        return $this->bundleSchema->get($bundleName)->renderScripts($options);
    }

    /**
     * Generate <link> tag(s) for CSS assets in an asset bundle.
     *
     * @param string $bundleName
     * @param mixed[] $options
     * @return string The rendered HTML tag(s).
     */
    public function css($bundleName = 'css/main', $options = [])
    {
        return $this->bundleSchema->get($bundleName)->renderStyles($options);
    }

    /**
     * Get the absolute url for an asset as specified by a stream path (e.g. "assets://css/bootstrap.css").
     *
     * @param string $streamPath
     * @return string The absolute URL.
     */
    public function url($streamPath)
    {
        return $this->getAbsoluteUrl($streamPath);
    }

    /**
     * Get the absolute url for an asset as specified by a stream path (e.g. "assets://css/bootstrap.css"), relative to a base URL.
     *
     * If the stream is `http` or `https`, this simply returns the full URL.
     * @param string $streamPath The stream path to resolve.
     * @return string The absolute URL.
     */
    protected function getAbsoluteUrl($streamPath)
    {
        // Resolve the asset URL (either a stream or absolute URL)
        if (is_array($streamPath)) {
            // Support stream lookup in ['asset', 'path/to'] format.
            if (count($streamPath) != 2) {
                throw new \BadMethodCallException('Invalid stream path given.');
            }
            $resolvedScheme = strtolower($streamPath[0]);
            $resolvedPath = $streamPath[1];
        } elseif (strstr($streamPath, '://')) {
            // Support stream lookup in 'asset://path/to' format.
            $stream = explode('://', $streamPath, 2);
            $resolvedScheme = strtolower($stream[0]);
            $resolvedPath = $stream[1];
        } else {
            throw new \BadMethodCallException('Invalid stream path given.');
        }

        if ($resolvedScheme == "http" || $resolvedScheme == "https") {
            return $streamPath;
        } else {
            // Use the relative path to the resource as the URL
            return $this->assetUrlBuilder->getAssetUrl($resolvedPath);
        }
    }
}
