<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @package   userfrosting/assets
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2013-2016 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Assets;

/**
 * Asset loader class.
 *
 * Loads an asset from the filesystem.
 * @author Alex Weissman (https://alexanderweissman.com)
 * @author RocketTheme (http://www.rockettheme.com/)
 */
class AssetLoader
{
    /**
     * @var string The base filesystem path in which to look for the asset file.  Can be an absolute path or stream.
     */
    protected $basePath;

    /**
     * @var string The fully constructed path to the file.
     */
    protected $fullPath;

    protected $pattern;
    
    /**
     * Create a new AssetLoader object.
     *
     * @param string $basePath The base absolute file path to use for retrieving the asset.
     * @param string $startPattern A regex pattern to represent the allowed subset of paths under basePath which are accessible.
     */
    public function __construct($basePath, $pattern)
    {
        $this->basePath = rtrim($basePath, "/\\") . '/';

        $this->pattern = $pattern;

        $this->fullPath = '';
    }

    /**
     * Compute the full filesystem path for the specified relative path (usually extracted from a URL).
     *
     * Also checks to make sure that the file actually exists.
     * @param string $relativePath
     * @return bool True if the file exists, false otherwise
     */
    public function loadAsset($relativePath)
    {
        // 1. Remove any query string
        $relativePath = preg_replace('/\?.*/', '', $relativePath);

        // 2. Normalize path, to prevent directory traversal
        $relativePath = $this->normalize($relativePath);

        // 3. Check that the beginning of the path matches the allowed paths pattern
        if (!preg_match($this->pattern, $relativePath)) {
            return false;
        }

        // 4. Build full path to file
        $this->fullPath = $this->basePath . $relativePath;

        // Return false if file does not exist
        if (!file_exists($this->fullPath)) {
            return false;
        }

        return true;
    }

    /**
     * Get the raw contents of the currently targeted file.
     *
     * @return string
     */
    public function getContent()
    {
        return file_get_contents($this->fullPath);
    }

    /**
     * Get the length in bytes of the currently targeted file.
     *
     * @return int
     */
    public function getLength()
    {
        return filesize($this->fullPath);
    }

    /**
     * Get the best-guess MIME type of the currently targeted file, based on the file extension.
     *
     * @return string
     */
    public function getType()
    {
        return MimeType::detectByFilename($this->fullPath);
    }

    /**
     * Returns the canonicalized URI on success. The resulting path will have no '/./' or '/../' components.
     * Trailing delimiter `/` is kept.
     *
     * By default (if $throwException parameter is not set to true) returns false on failure.
     *
     * @see https://github.com/rockettheme/toolbox/blob/develop/ResourceLocator/src/UniformResourceLocator.php
     * @param string $uri
     * @param bool $throwException
     * @param bool $splitStream
     * @return string|array|bool
     * @throws \BadMethodCallException
     */
    public function normalize($uri, $throwException = false, $splitStream = false)
    {
        if (!is_string($uri)) {
            if ($throwException) {
                throw new \BadMethodCallException('Invalid parameter $uri.');
            } else {
                return false;
            }
        }

        $uri = preg_replace('|\\\|u', '/', $uri);
        $segments = explode('://', $uri, 2);
        $path = array_pop($segments);
        $scheme = array_pop($segments) ?: 'file';

        if ($path) {
            $path = preg_replace('|\\\|u', '/', $path);
            $parts = explode('/', $path);

            $list = [];
            foreach ($parts as $i => $part) {
                if ($part === '..') {
                    $part = array_pop($list);
                    if ($part === null || $part === '' || (!$list && strpos($part, ':'))) {
                        if ($throwException) {
                            throw new \BadMethodCallException('Invalid parameter $uri.');
                        } else {
                            return false;
                        }
                    }
                } elseif (($i && $part === '') || $part === '.') {
                    continue;
                } else {
                    $list[] = $part;
                }
            }

            if (($l = end($parts)) === '' || $l === '.' || $l === '..') {
                $list[] = '';
            }

            $path = implode('/', $list);
        }

        return $splitStream ? [$scheme, $path] : ($scheme !== 'file' ? "{$scheme}://{$path}" : $path);
    }
}
