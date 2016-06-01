<?php

/**
 * Represents a Javascript asset.
 *
 * @package userfrosting/assets 
 * @author  Alexander Weissman
 * @license MIT
 */
namespace UserFrosting\Assets;

class JavascriptAsset extends Asset
{

    /**
     * Render this JavascriptAsset as a <script> tag, using the specified base url.
     *
     * @param string $base_url The base url to use, for example https://example.com/assets/, or http://localhost/myproject/public/assets/
     * @return string The rendered <script> tag.
     */    
    public function render($base_url)
    {
        $base_url = rtrim($base_url, "/\\") . '/';
        $absolute_url = $base_url . $this->path;
        
        $options = $this->options;
        
        $attributes = [];
        
        if (isset($options['async']) && $options['async'] = true)
            $attributes[] = 'async';
        if (isset($options['defer']) && $options['defer'] = true)
            $attributes[] = 'defer';
        if (isset($options['id']))
            $attributes[] = 'id="' . $options['id'] . '"';
        if (isset($options['type']))
            $attributes[] = 'type="' . $options['type'] . '"';
            
        return '<script src="' . $absolute_url . '" ' . implode(' ', $attributes) . '></script>';
    }
}