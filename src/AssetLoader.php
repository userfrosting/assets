<?php

/*
 * UserFrosting Assets (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/assets
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Jordan Mele
 * @license   https://github.com/userfrosting/assets/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Assets;

use UserFrosting\Assets\Util\MimeType;

/**
 * Asset loader class.
 *
 * Loads an asset from the filesystem.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class AssetLoader
{
    /**
     * @var string The fully constructed path to the file.
     */
    protected $fullPath;

    /**
     * @var \UserFrosting\Assets\Assets
     */
    protected $assets;

    /**
     * Create a new AssetLoader object.
     *
     * @param \UserFrosting\Assets\Assets $assets
     */
    public function __construct(Assets $assets)
    {
        $this->assets = $assets;
    }

    /**
     * Compute the full filesystem path for the specified relative path (usually extracted from a URL).
     *
     * Also checks to make sure that the file actually exists.
     *
     * @param string $relativePath
     *
     * @return bool True if the file exists, false otherwise
     */
    public function loadAsset($relativePath)
    {
        $this->fullPath = $this->assets->urlPathToAbsolutePath($relativePath);

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
     * Get the last modified date and time.
     *
     * @return \DateTime
     */
    public function getLastModified(): \DateTime
    {
        return new \DateTime('@'.filemtime($this->fullPath));
    }
}
