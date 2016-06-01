<?php

/**
 * Represents a CSS asset.
 *
 * @package userfrosting/assets
 * @author  Alexander Weissman
 * @license MIT
 */
namespace UserFrosting\Assets;
 
class CssAsset extends Asset
{

    /**
     * Render this CssAsset as a <link> tag, using the specified base url.
     *
     * @param string $base_url The base url to use, for example https://example.com/assets/, or http://localhost/myproject/public/assets/
     * @return string The rendered <link> tag.
     */
    public function render($base_url)
    {
        $base_url = rtrim($base_url, "/\\") . '/';
        $absolute_url = $base_url . $this->path;
        
        $options = $this->options;
        
        $attributes = [];
        
        if (isset($options['id']))
            $attributes[] = 'id="' . $options['id'] . '"';
        if (isset($options['media']))
            $attributes[] = 'media="' . $options['media'] . '"';
        
        $rel = isset($options['rel']) ? $options['rel'] : "stylesheet";
        $type = isset($options['type']) ? $options['type'] : "text/css";
            
        return '<link rel="' . $rel . '" type="' . $type . '" href="' . $absolute_url . '" ' . implode(' ', $attributes) . '>';
    }
}
