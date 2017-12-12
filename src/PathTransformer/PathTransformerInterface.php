<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @package   userfrosting/assets
 * @link      https://github.com/userfrosting/assets
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Assets\PathTransformer;

/**
 * Transforms a relative file path prefix to a more URL friendly version, in a reversible fashion.
 * Typically used in development scenarios in conjunction with an asset server to produce readable and debuggable asset URLs.
 *
 * @author Jordan Mele
 */
interface PathTransformerInterface
{
    /**
     * Transforms relative file path into its URL form.
     *
     * @param string $relativeUrl
     * @return string
     */
    public function pathToUrl($relativePath);

    /**
     * Reverts transformations applied to the former relative file path.
     *
     * @param string $relativePath
     * @return string
     */
    public function urlToPath($relativeUrl);
}
