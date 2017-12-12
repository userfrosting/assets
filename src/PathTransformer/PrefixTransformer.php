<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @package   userfrosting/assets
 * @link      https://github.com/userfrosting/assets
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Assets\PathTransformer;

use UserFrosting\Assets\PathTransformer\PathTransformerInterface;
use UserFrosting\Support\Util\Util;

/**
 * Transforms a relative file path prefix to a more URL friendly version, in a reversible fashion.
 * Typically used in development scenarios in conjunction with an asset server to produce readable and debuggable asset URLs.
 *
 * @author Jordan Mele
 */
class PrefixTransformer implements PathTransformerInterface
{
    /**
     * Stores string pairs used to transform the prefix of paths to a more URL friendly version, and reverse the process.
     * $pathPrefix => $urlPrefix
     *
     * @var string[string]
     */
    private $definitions = [];

    /**
     * Defines a reversible prefix transformation.
     *
     * @param string $pathPrefix The file path prefix.
     * @param string $urlPrefix The URL prefix.
     * @return void
     */
    public function define($pathPrefix, $urlPrefix)
    {
        // Make sure path prefix is a string and not empty
        if (!is_string($pathPrefix)) {
            throw new \InvalidArgumentException("Path prefix must be a string (currently: " . gettype($pathPrefix) . ") and not empty (currently contains: '$pathPrefix').");
        }

        // Make sure url prefix is a string and not empty
        if (!is_string($urlPrefix)) {
            throw new \InvalidArgumentException("URL prefix must be a string (currently: " . gettype($urlPrefix) . ") and not empty (currently contains: '$urlPrefix').");
        }


        // Make sure path prefix is unique.
        if (array_key_exists($pathPrefix, $this->definitions)) {
            throw new \InvalidArgumentException("Irreversible prefix transformation detected. Provided path prefix '$pathPrefix' already has a transformation definition.");
        }

        // Make sure URL prefix is unique.
        if (in_array($urlPrefix, $this->definitions)) {
            throw new \InvalidArgumentException("Irreversible prefix transformation detected. Provided URL prefix '$urlPrefix' already has a transformation definition.");
        }

        // Add transformation definition
        $this->definitions[$pathPrefix] = $urlPrefix;
    }

    /**
     * @inheritDoc
     */
    public function pathToUrl($relativePath)
    {
        $prefixPair = $this->getTransformation($relativePath, true);
        return $prefixPair[1] . Util::stripPrefix($relativePath, $prefixPair[0]);
    }

    /**
     * @inheritDoc
     */
    public function urlToPath($relativeUrl)
    {
        $prefixPair = $this->getTransformation($relativeUrl, false);
        return $prefixPair[0] . Util::stripPrefix($relativeUrl, $prefixPair[1]);
    }

    /**
     * Returns matched prefix transformation definition for the provided subject.
     *
     * @param string $subject String to match prefix on.
     * @param bool $path Match on path prefix, or url prefix.
     * @return string[]
     * 
     * @throws \OutOfRangeException if no prefix transformation pair matches the subject.
     */
    private function getTransformation($subject, $path)
    {
        foreach ($this->definitions as $pathPrefix => $urlPrefix) {
            // Select applicable prefix.
            $prefix = $path ? $pathPrefix : $urlPrefix;
            // Check if this is the subjects prefix.
            if (strpos($subject, $prefix) === 0) {
                return [$pathPrefix, $urlPrefix];
            }
        }
        throw new \OutOfRangeException("No prefix transformation pair matched the subject.");
    }
}
