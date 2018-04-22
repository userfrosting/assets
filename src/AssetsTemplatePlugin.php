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
 * Generic plugin for template systems. Provides several convenience methods for linking assets within templates.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 * @author Jordan Mele
 *
 * @todo JS and CSS convenience methods. (not bundles)
 */
class AssetsTemplatePlugin
{
    /** @var Assets Resource locator used to find assets. */
    private $assets;

    /**
     * Constructor
     *
     * @param Assets $assets
     */
    public function __construct(Assets $assets)
    {
        $this->assets = $assets;
    }

    /**
     * Returns HTML ready tags for all assets in the requested JS bundle.
     *
     * @param string $bundleName Bundle name.
     * @param array $attributes Attributes generated tags should include.
     * @return string
     */
    public function js($bundleName = 'js/main', $attributes = [])
    {
        $assetPaths = $this->assets->getJsBundleAssets($bundleName);
        $assetsTagged = '';

        foreach ($assetPaths as $assetPath) {
            $assetsTagged .= $this->makeRegularTag('script', array_merge(['src' => $assetPath], $attributes));
        }

        return $assetsTagged;
    }

    /**
     * Returns HTML ready tags for all assets in the requested CSS bundle.
     *
     * @param string $bundleName Bundle name.
     * @param array $attributes Attributes generated tags should include.
     * @return string
     */
    public function css($bundleName = 'css/main', $attributes = [])
    {
        $attributes = array_merge(['rel' => 'stylesheet', 'type' => 'text/css'], $attributes);

        $assetPaths = $this->assets->getCssBundleAssets($bundleName);
        $assetsTagged = '';

        foreach ($assetPaths as $assetPath) {
            $assetsTagged .= $this->makeSelfClosingTag('link', true, array_merge(['href' => $assetPath], $attributes));
        }

        return $assetsTagged;
    }

    /**
     * Returns URL to asset specified by stream.
     *
     * @param string|string[] $streamPath A valid stream. EG: 'assets://path.to' or ['assets', 'path.to']
     * @return void
     */
    public function url($streamPath)
    {
        return $this->assets->getAbsoluteUrl($streamPath);
    }

        /**
     * Converts an array of attributes into a form readily usable within XML.
     *
     * @param mixed[] $attributes Attributes to convert.
     * @return string
     */
    private function convertAttributes(array $attributes)
    {
        $output = '';

        foreach ($attributes as $name => $value) {
            // If $name is not set, then its a value-less attribute. (EG: 'async' and 'defer')
            if (is_integer($name)) {
                $output .= "$value ";
            } else {
                $output .= "$name=\"$value\" ";
            }
        }

        return trim($output);
    }

    /**
     * Generates a self closing tag.
     *
     * @param string $tagName Tag name.
     * @param bool $closingSlash If a closing slash should be included. Defaults to true.
     * @param mixed[]|null $attributes Attributes to add to tag. Optional.
     * @return string
     */
    private function makeSelfClosingTag($tagName, $closingSlash = true, array $attributes = null)
    {
        if (!is_string($tagName)) {
            throw new \InvalidArgumentException("Expected \$tagName to be type string but was " . gettype($tagName) . '.');
        }
        if (!is_bool($closingSlash)) {
            throw new \InvalidArgumentException("Expected \$closingSlash to be type bool but was " . gettype($closingSlash) . '.');
        }

        $output = "<$tagName";
        if ($attributes !== null) {
            $output .= ' ' . $this->convertAttributes($attributes);
        }
        if ($closingSlash) {
            $output .= " />";
        } else {
            $output .= ">";
        }

        return $output;
    }

    /**
     * Generates a tag pair.
     *
     * @param string $tagName Tag name.
     * @param mixed[]|null $attributes Attributes to add to tag. Optional.
     * @param string $content Content generated tags will wrap around.
     * @return string
     */
    private function makeRegularTag($tagName, array $attributes = null, $content = '')
    {

        $output = "<$tagName";
        if ($attributes !== null) {
            $output .= ' ' . $this->convertAttributes($attributes);
        }
        $output .= ">$content</$tagName>";

        return $output;
    }
}