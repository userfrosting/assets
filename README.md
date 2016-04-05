# Assets module for UserFrosting

## Usage

This module works in tandem with [gulp-bundle-assets](https://github.com/dowjones/gulp-bundle-assets) as a PHP solution for automagically rendering asset tags (`<script>`, `<link>`, etc) for assets managed through Gulp.  `UserFrosting\Assets` can read both the input and output files used by `gulp-bundle-assets` to render appropriate tags for raw or compiled assets.

To get started, define a bundle configuration file as defined in [gulp-bundle-assets](https://github.com/dowjones/gulp-bundle-assets#basic-usage):


*bundle.config.json*

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

In your PHP application, you can now load this file into an `AssetBundleSchema`:

```
<?php
    
use UserFrosting\Assets\AssetManager;
use UserFrosting\Assets\AssetBundleSchema;    

$as = new AssetBundleSchema();
$as->loadRawSchemaFile('/path/to/bundle.config.json');
$as->loadCompiledSchemaFile('/path/to/bundle.result.json');
```

By creating an `AssetManager`, you can easily render any bundle defined in the schema, in raw or compiled mode.

```
// Setting the second parameter to 'true' will tell our AssetManager to render all asset bundles in raw mode.
$am = new AssetManager('http://localhost/myproject/', true);
$am->setRawAssetsPath('assets-raw');
$am->setCompiledAssetsPath('assets');
$am->setBundleSchema($as);
```

You can now render the Javascript or CSS assets for any bundle:

`echo $am->css('css/main');`

In raw mode, this will output:

```
<link rel="stylesheet" type="text/css" href="http://localhost/myproject/assets-raw/vendor/font-awesome-4.5.0/css/font-awesome.css" >
<link rel="stylesheet" type="text/css" href="http://localhost/myproject/assets-raw/css/bootstrap-3.3.1.css" >
<link rel="stylesheet" type="text/css" href="http://localhost/myproject/assets-raw/css/bootstrap-custom.css" >
<link rel="stylesheet" type="text/css" href="http://localhost/myproject/assets-raw/css/paper.css" >   
```

In compiled mode, this will instead output:

```
<link rel="stylesheet" type="text/css" href="http://localhost/myproject/assets/css/main-c72ce38fba.css" >
```

If you are using Twig, you can pass your `AssetManager` as a global variable to Twig.  You can then use any of its rendering methods to automatically insert the tags into your template:

`{{ am.js('js/mybundle') | raw }}`

## Sample Gulp file

Assuming you have `npm` and `gulp-bundle-assets` installed, you can use the following Gulp file and run the `gulp bundle` task to compile your assets:

*build/gulpfile.js*

```
var gulp = require('gulp');
var gulpLoadPlugins = require('gulp-load-plugins');
var plugins = gulpLoadPlugins();

// The directory where the bundle task should look for the raw assets, as specified in bundle.config.json
var sourceDirectory = '../public/assets-raw/';

// The directory where the bundle task should place compiled assets.  The names of assets in bundle.result.json
// will be specified relative to this path.
var destDirectory = '../public/assets/';

gulp.task('bundle', function() {
    fb = gulp.src('./bundle.config.json')
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