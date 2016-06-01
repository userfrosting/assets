<?php

/**
 * Generically represents an asset (Javascript file, CSS file, etc)
 *
 * @package userfrosting/assets 
 * @author  Alexander Weissman
 * @license MIT
 */
namespace UserFrosting\Assets;

abstract class Asset
{

    /**
     * Url subpath and file name for the asset, relative to the base url specified when rendering.
     *
     * @var string
     */
    protected $path;
    
    /**
     * Any additional HTML attributes, as key->value pairs, to render in the asset's tag.
     *
     * @var string[]
     */
    protected $options;

    /**
     * Create a new asset with the specified subpath.
     *
     * @param string $path The url subpath and file name for this asset (e.g. "vendor/bootstrap/js/bootstrap.js")
     */
    public function __construct($path)
    {
        $this->path = ltrim($path, "/\\");
    }
    
    /**
     * Render this asset, using the specified base url.
     *
     * @param string $base_url The base url to use, for example https://example.com/assets/, or http://localhost/myproject/public/assets/
     * @return string The rendered asset tag.
     */    
    abstract public function render($base_url);
}