# Assets module for UserFrosting

## Usage

This module works in tandem with [gulp-bundle-assets](https://github.com/dowjones/gulp-bundle-assets) as a PHP solution for automagically rendering asset tags (`<script>`, `<link>`, etc) for assets managed through Gulp.  `UserFrosting\Assets` can read both the input and output files used by `gulp-bundle-assets` to render appropriate tags for raw or compiled assets.

To get started, define a bundle configuration file as defined in [gulp-bundle-assets](https://github.com/dowjones/gulp-bundle-assets#basic-usage):


*asset-bundles.json*

```
{
  "bundle": {
    "js/main": {
        "scripts": [
            {
                "src": "js/bootstrap-3.3.1.js"
            },
            "js/crud.js"
        ],
        "options": {
            "result": {
                "type": {
                  "scripts": "plain"
                }
            }
        }
    },
    "css/main": {
        "styles": [
            "vendor/font-awesome-4.5.0/css/font-awesome.css",
            "css/bootstrap-3.3.1.css",
            "css/bootstrap-custom.css",
            "css/paper.css"
        ],
        "options": {
            "result": {
                "type": {
                  "styles": "plain"
                }
            }
        }
    }
  },
  "copy": [
    {
        "src": "images/**/*",
        "base": "."
    },
    {
        "src": "vendor/font-awesome-4.5.0/fonts/**/*",
        "base": "vendor/font-awesome-4.5.0/"
    }
  ]
}
```

Notice a small difference between this example and the example provided in gulp-bundle-assets: We define **only** the JSON object, rather than using the `module.exports=` syntax, in our configuration file.  This is fine and `gulp-bundle-assets` will be able to process it in the same way.

Also note the setting of `options -> result -> type -> styles/scripts` for each bundle to `plain`.  This is important to do, so that `Assets` can parse the output of `gulp-bundle-assets` and correctly render tags for compiled assets.  The output of your Gulp task should look something like:

*bundle.result.json*

```
{
  "js/main": {
    "scripts": "js/main-8881456f8e.js"
  },
  "css/main": {
    "styles": "css/main-c72ce38fba.css"
  }
}
```

Notice that each bundle is processed into a single Javascript or CSS file.  You may have bundles that contain both Javascript and CSS, but we recommend you split them into separate bundles and use the `js/` and `css/` prefixes to distinguish them.

To find raw asset files, you will also need to create an instance of `AssetUrlBuilder`.  This in turn requires an instance of `UniformResourceLocator`, where you can add your desired search paths:

```
<?php

use RocketTheme\Toolbox\ResourceLocator\UniformResourceLocator;
use UserFrosting\Assets\AssetManager;
use UserFrosting\Assets\AssetBundleSchema;
use UserFrosting\Assets\UrlBuilder\AssetUrlBuilder;

$basePath = __DIR__;
$locator = new UniformResourceLocator($basePath);
$locator->addPath('assets', '', [
    'owls/assets',
    'hawks/assets'
]);

$baseUrl = 'http://example.com/public/assets';
$aub = new AssetUrlBuilder($locator, $baseUrl);
```

Once you have an instance of `AssetUrlBuilder`, you can create an instance of an `AssetBundleSchema` for a raw schema:

```
$as = new AssetBundleSchema($aub);
$as->loadRawSchemaFile('/path/to/asset-bundles.json');
```

By creating an `AssetManager`, you can easily render any bundle defined in your schema, in raw or compiled mode.

```
$am = new AssetManager($aub, $as);
```

You can now render the Javascript or CSS assets for any bundle:

`echo $am->css('css/main');`

In raw mode, this will output (assuming that all the assets were found in (`owls/assets/`):

```
<link rel="stylesheet" type="text/css" href="http://localhost/myproject/assets-raw/owls/assets/vendor/font-awesome-4.5.0/css/font-awesome.css" >
<link rel="stylesheet" type="text/css" href="http://localhost/myproject/assets-raw/owls/assets/css/bootstrap-3.3.1.css" >
<link rel="stylesheet" type="text/css" href="http://localhost/myproject/assets-raw/owls/assets/css/bootstrap-custom.css" >
<link rel="stylesheet" type="text/css" href="http://localhost/myproject/assets-raw/owls/assets/css/paper.css" >   
```

To load and render compiled assets, you create an instance of `CompiledAssetUrlBuilder` instead:

```
$aub = new CompiledAssetUrlBuilder($baseUrl);
$as = new AssetBundleSchema($aub);
// Notice we use the compiled schema file here instead
$as->loadCompiledSchemaFile('/path/to/bundle.result.json');
```

This outputs:

```
<link rel="stylesheet" type="text/css" href="http://localhost/myproject/assets/css/main-c72ce38fba.css" >
```

If you are using Twig, you can pass your `AssetManager` as a global variable to Twig.  You can then use any of its rendering methods to automatically insert the tags into your template:

`{{ am.js('js/mybundle') | raw }}`

### Advanced Usage

For all *script* and *style* methods, options can be provided to modify the produced tag.

`async` for instance can be applied to a Javascript tag:

_In PHP_
`echo $am.js('js/mybundle', [ 'async' => true ])`

_In TWIG_
`{{ am.js('js/bundle', { 'async': true })}}`

## Sample Gulp file

Assuming you have `npm` and `gulp-bundle-assets` installed, you can use the following Gulp file and run the `gulp bundle` task to compile your assets:

*build/gulpfile.js*

```
var gulp = require('gulp');
var gulpLoadPlugins = require('gulp-load-plugins');
var plugins = gulpLoadPlugins();

// The directory where the bundle task should look for the raw assets, as specified in asset-bundles.json
var sourceDirectory = '../public/assets-raw/';

// The directory where the bundle task should place compiled assets.  The names of assets in bundle.result.json
// will be specified relative to this path.
var destDirectory = '../public/assets/';

gulp.task('bundle', function() {
    fb = gulp.src('./asset-bundles.json')
        .pipe(plugins.bundleAssets({
            base: sourceDirectory
        }))
        .pipe(plugins.bundleAssets.results({
            dest: './'  // destination of bundle.result.json
        })) 
        .pipe(gulp.dest(destDirectory));
    return fb;
});
```

## Testing

```
phpunit --bootstrap tests/bootstrap.php tests
```
