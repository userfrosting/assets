<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @package   userfrosting/assets
 * @link      https://github.com/userfrosting/assets
 * @copyright Copyright (c) 2013-2016 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Assets;

/**
 * Represents a Javascript asset.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class JavascriptAsset extends Asset
{

    /**
     * Render this JavascriptAsset as a <script> tag, using the specified base url.
     *
     * @param string $baseUrl The base url to use, for example https://example.com/assets/, or http://localhost/myproject/public/assets/
     * @return string The rendered <script> tag.
     */    
    public function render($baseUrl)
    {
        $baseUrl = rtrim($baseUrl, "/\\") . '/';
        $absoluteUrl = $baseUrl . $this->path;

        $options = $this->options;

        $attributes = [];

        if (isset($options['async']) && $options['async'] = true) {
            $attributes[] = 'async';
        }

        if (isset($options['defer']) && $options['defer'] = true) {
            $attributes[] = 'defer';
        }

        if (isset($options['id'])) {
            $attributes[] = 'id="' . $options['id'] . '"';
        }

        if (isset($options['type'])) {
            $attributes[] = 'type="' . $options['type'] . '"';
        }

        return '<script src="' . $absoluteUrl . '" ' . implode(' ', $attributes) . '></script>';
    }
}
