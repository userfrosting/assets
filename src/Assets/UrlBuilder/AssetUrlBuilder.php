<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @package   userfrosting/assets
 * @link      https://github.com/userfrosting/assets
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Assets\UrlBuilder;

use RocketTheme\Toolbox\ResourceLocator\UniformResourceLocator;
use UserFrosting\Support\Util\Util;
use UserFrosting\Support\Exception\FileNotFoundException;

/**
 * Builds a URL for an asset by finding the highest-priority instance in the assets:// stream, and prefixing with a base url.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class AssetUrlBuilder implements AssetUrlBuilderInterface
{
    /**
     * @var string The base url for your assets, for example https://example.com/assets-raw/
     */
    protected $baseUrl;

    /**
     * @var UniformResourceLocator Locator service to use when searching for asset files.
     */
    protected $locator;

    /**
     * @var string A prefix to find and remove from the matched file path, before constructing the url.
     */
    protected $removePrefix;

    /**
     * @var string Stream wrapper scheme to use when searching for the asset.
     */
    protected $scheme;

    /**
     * AssetUrlBuilder constructor.
     *
     * @param UniformResourceLocator $locator
     * @param string $baseUrl
     * @param string $removePrefix
     * @param string $scheme
     */
    public function __construct(UniformResourceLocator $locator, $baseUrl, $removePrefix = '', $scheme = 'assets')
    {
        $this->locator = $locator;

        $this->baseUrl = rtrim($baseUrl, '/') . '/';

        $this->removePrefix = $removePrefix ? rtrim($removePrefix, "/\\") . '/' : '';

        $this->scheme = $scheme;
    }

    /**
     * Generate an absolute URL for an asset, based on the asset path and the bundle's baseUrl.
     *
     * @param string $path Path to search for in the stream.
     * @param string|null $declarationSource A string describing the config file and bundle in which this asset was referenced.
     * @return string Fully qualified http(s) url for the asset.
     */
    public function getAssetUrl($path, $declarationSource = null)
    {
        $relativeUrl = $this->locator->findResource($this->scheme . '://' . $path, false);

        if ($relativeUrl) {
            $relativeUrl = Util::stripPrefix($relativeUrl, $this->removePrefix);
            $absoluteUrl = $this->baseUrl . $relativeUrl;
        } else {
            $message = "The asset '$path' could not be found." . ($declarationSource ? " Referenced in '$declarationSource'." : '');
            throw new FileNotFoundException($message);
        }

        return $absoluteUrl;
    }
}
