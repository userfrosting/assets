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
 * Generically represents an asset (Javascript file, CSS file, etc)
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 */
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
     * @param string $baseUrl The base url to use, for example https://example.com/assets/, or http://localhost/myproject/public/assets/
     * @return string The rendered asset tag.
     */    
    abstract public function render($baseUrl);
}
