<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @package   userfrosting/assets
 * @link      https://github.com/userfrosting/assets
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Assets\UrlBuilder;

/**
 * Builds a URL for a compiled asset by simply prefixing a path with a base url.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class CompiledAssetUrlBuilder implements AssetUrlBuilderInterface
{
    /**
     * @param string The base url for your assets, for example https://example.com/assets/
     */
    protected $baseUrl;

    /**
     * CompiledAssetUrlBuilder constructor.
     *
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
    }

    /**
     * Generate an absolute URL for an asset, by simply concatenating the baseUrl and the specified path.
     *
     * @param string $path Relative path to the desired asset
     */
    public function getAssetUrl($path)
    {
        $absoluteUrl = $this->baseUrl . $path;
        return $absoluteUrl;
    }
}
