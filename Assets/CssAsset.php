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
 * Represents a CSS asset.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class CssAsset extends Asset
{

    /**
     * Render this CssAsset as a <link> tag, using the specified base url.
     *
     * @param string $baseUrl The base url to use, for example https://example.com/assets/, or http://localhost/myproject/public/assets/
     * @return string The rendered <link> tag.
     */
    public function render($baseUrl)
    {
        $baseUrl = rtrim($baseUrl, "/\\") . '/';
        $absoluteUrl = $baseUrl . $this->path;
        
        $options = $this->options;
        
        $attributes = [];
        
        if (isset($options['id'])) {
            $attributes[] = 'id="' . $options['id'] . '"';
        }

        if (isset($options['media'])) {
            $attributes[] = 'media="' . $options['media'] . '"';
        }

        $rel = isset($options['rel']) ? $options['rel'] : "stylesheet";
        $type = isset($options['type']) ? $options['type'] : "text/css";

        return '<link rel="' . $rel . '" type="' . $type . '" href="' . $absoluteUrl . '" ' . implode(' ', $attributes) . '>';
    }
}
