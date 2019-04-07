# UserFrosting/Assets

[![Latest Version](https://img.shields.io/github/release/userfrosting/assets.svg)](https://github.com/userfrosting/assets/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Join the chat at https://chat.userfrosting.com/channel/support](https://demo.rocket.chat/images/join-chat.svg)](https://chat.userfrosting.com/channel/support)
[![Donate](https://img.shields.io/badge/Open%20Collective-Donate-blue.svg)](https://opencollective.com/userfrosting#backer)

| Branch | Status |
| ------ | ------ |
| master | [![codecov](https://codecov.io/gh/userfrosting/assets/branch/master/graph/badge.svg)](https://codecov.io/gh/userfrosting/userfrosting/branch/master) [![Build Status](https://travis-ci.org/userfrosting/assets.svg?branch=master)](https://travis-ci.org/userfrosting/assets) [![Windows Build](https://ci.appveyor.com/api/projects/status/github/userfrosting/assets?branch=master&svg=true)](https://ci.appveyor.com/project/userfrosting/assets)  |
| develop | [![codecov](https://codecov.io/gh/userfrosting/assets/branch/develop/graph/badge.svg)](https://codecov.io/gh/userfrosting/assets/branch/develop) [![Build Status](https://travis-ci.org/userfrosting/assets.svg?branch=develop)](https://travis-ci.org/userfrosting/assets) [![Windows Build](https://ci.appveyor.com/api/projects/status/github/userfrosting/assets?svg=true&branch=develop)](https://ci.appveyor.com/project/userfrosting/assets) |

**Assets** is a library originally created for UserFrosting 4 to make it much easier to reference frontend assets in both production and development contexts.

Out of the box it can:

- Provide an easy way to generate an absolute url to an asset via a locator.
- Provide a basic level of integration with *gulp-bundle-assets*, making it easy to reference asset bundles.
- Integrate via a common interface with virtually any bundling system.
- Integrate with Slim to serve assets that are inaccessible from the public folder, in the development context.
- Perform url-to-path transformations. Useful for making debuggable URLs that can be reverted back to the path to be used by the Slim asset server.
- Integrate with your preferred (and extendable) templating engine to provide easy access to asset bundles (that get wrapped with the appropriate tags) and individual assets.

## Installation

```bash
composer require userfrosting/assets
```

## Usage

To use Assets, you will need:

- An instance of `ResourceLocator`, where you can add your desired search paths.
- The locator scheme (if it exists) you wish to look for assets in.
- The base url (used in generating URLs to assets).
- The base path (used in trimming the absolute path returned by the locator).

```php
<?php

use UserFrosting\UniformResourceLocator\ResourceLocator;
use UserFrosting\Assets\Assets;

$basePath = __DIR__;
$baseUrl = 'https://assets.userfrosting.com/';
$locator = new ResourceLocator($basePath);
$locator->registerStream('assets', '', [
    'owls/assets',
    'hawks/assets'
]);

$assets = new Assets($locator, 'assets', $baseUrl);
```

> Optionally 1 more argument can be passed into the `Assets` constructor.
> - An instance of `PrefixTransformer`.
> Have a look at UserFrosting in dev mode to see this in action!

### Asset Bundles

To access asset bundles from an `Assets` instance, it must first be passed an instance of `AssetBundlesInterface` via `addAssetBundles`. An example of this follows:

*Directory Tree*

```txt
/
├build/
│ └asset-bundles.json
└public/
  └index.php

```

*build/asset-bundles.json*

```json
{
  "bundle": {
    "js/main": {
        "scripts": [
            "js/bootstrap-3.3.1.js",
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

*public/index.php*

```php
use UserFrosting\Assets\GulpBundleAssetsRawBundles;

// Load asset bundles.
$assetBundles = new GulpBundleAssetsRawBundles("../build/asset-bundles.json");

// Send loaded asset bundles to Assets instance.
$assets->addAssetBundles($assetBundles);

// Grab an asset bundle.
$assets->getJsBundleAssets("js/main");
// Outputs ["js/bootstrap-3.3.1.js", "js/crud.js"]
```

See [gulp-bundle-assets](https://github.com/dowjones/gulp-bundle-assets) for how to use the bundler this example demonstrates integration with.

Just keep in mind that this integration can only work when the gulp-bundle-assets configuration is stored in a separate JSON file, not a JavaScript file.

Also note the setting of `options -> result -> type -> styles/scripts` for each bundle to `plain`.  This is important to do, so that `Assets` can parse the output of `gulp-bundle-assets` and correctly render tags for compiled assets.  The output of your Gulp task should look something like:

*bundle.result.json*

```json
{
  "js/main": {
    "scripts": "js/main-8881456f8e.js"
  },
  "css/main": {
    "styles": "css/main-c72ce38fba.css"
  }
}
```

Using this results file would be done with the `GulpBundleAssetsCompiledBundles` class.

### The Template Plugin

The template plugin is easy initialized by giving it the `Assets` instance, and simply gets passed into the templating engine environment of your choice to be used.

```php
use UserFrosting\Assets\AssetsTemplatePlugin;

$assetsPlugin = new AssetsTemplatePlugin($assets);

// Some code that passes it to Twig rendering environment.
```

```twig
{# Gets replaced at runtime with the following. Additional argument is optional. #}
{{assets.js("js/main", { defer: true })}}
```

```html
<script src="https://assets.userfrosting.com/assets/bootstrap/js/bootstrap.js" defer="true"></script>
<script src="https://assets.userfrosting.com/assets/bootstrap/js/npm.js" defer="true"></script>
```

## [Style Guide](STYLE_GUIDE.md)

## [Testing](RUNNING_TESTS.md)
