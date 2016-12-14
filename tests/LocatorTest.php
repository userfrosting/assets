<?php

use PHPUnit\Framework\TestCase;
use RocketTheme\Toolbox\ResourceLocator\UniformResourceLocator;

class LocatorTest extends TestCase
{
    protected $basePath = __DIR__ . '/data';
    protected $locator;

    public function setUp()
    {
        // Arrange
        $this->locator = new UniformResourceLocator($this->basePath);
        $this->locator->addPath('assets', '', [
            'owls/assets',
            'hawks/assets'
        ]);
    }

    public function testLocatorBasePath()
    {
        // Act
        $path = $this->locator->getBase();

        // Assert
        $this->assertEquals($this->basePath, $path);
    }

    public function testLocatorSimple()
    {
        // Act
        $path = $this->locator->findResource('assets://vendor/bootstrap-3.3.6/js/bootstrap.js');

        $this->assertEquals($this->basePath . '/owls/assets/vendor/bootstrap-3.3.6/js/bootstrap.js', $path);
    }

    public function testLocatorSimpleFail()
    {
        // Act
        $path = $this->locator->findResource('assets://js/ducks.js');

        $this->assertEquals(null, $path);

        $path = $this->locator->findResource('assets://../forbidden.txt');

        $this->assertEquals(null, $path);
    }

    public function testLocatorSimpleRelative()
    {
        $path = $this->locator->findResource('assets://vendor/bootstrap-3.3.6/js/bootstrap.js', false);

        $this->assertEquals('owls/assets/vendor/bootstrap-3.3.6/js/bootstrap.js', $path);

        $path = $this->locator->findResource('assets://vendor/bootstrap-3.3.6/css/bootstrap-theme.css', false);

        $this->assertEquals('hawks/assets/vendor/bootstrap-3.3.6/css/bootstrap-theme.css', $path);

        $path = $this->locator->findResource('assets://vendor/bootstrap-3.3.6/css/bootstrap.css', false);

        $this->assertEquals('owls/assets/vendor/bootstrap-3.3.6/css/bootstrap.css', $path);
    }
}
