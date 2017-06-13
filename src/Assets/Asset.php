<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @package   userfrosting/assets
 * @link      https://github.com/userfrosting/assets
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Assets;

/**
 * Generically represents an asset (Javascript file, CSS file, etc)
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class Asset
{

    /**
     * @var string A string describing the config file and bundle in which this asset was referenced.
     */
    protected $declarationSource;

    /**
     * Any additional HTML attributes, as key->value pairs, to render in the asset's tag.
     *
     * @var string[]
     */
    protected $options;

    /**
     * Url subpath and file name for the asset, relative to the base url specified when rendering.
     *
     * @var string
     */
    protected $path;

    /**
     * Create a new asset with the specified subpath.
     *
     * @param string $path The url subpath and file name for this asset (e.g. "vendor/bootstrap/js/bootstrap.js")
     * @param string $declarationSource A string describing the config file and bundle in which this asset was referenced.
     */
    public function __construct($path, $declarationSource = '')
    {
        $this->path = ltrim($path, "/\\");
        $this->declarationSource = $declarationSource;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getDeclarationSource()
    {
        return $this->declarationSource;
    }
}
