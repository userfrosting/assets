# Change Log

## 4.2.0

Replace most logic to better use the locator power
- Removed `PathTransformer`.
- Absolutes URL returns a locator friendly path that can be converted back to a locator uri. For example, the `assets://vendor/bootstrap/js/bootstrap.js` uri will now returns `http://example.com/assets/vendor/bootstrap/js/bootstrap.js` instead of `http://example.com/vendor/bootstrap/js/bootstrap.js`. This means multiple stream scheme can be used at once, for example `assets://` scheme can be used as well as `images://` scheme. This also means the sprinkle location is not required anymore in URL. The locator stream location will abstract the sprinkle. The only drawback is it may be harder to debug which asset is loaded (actually, from which sprinkle) in case of sprinkle collision.
- Removed `overwriteBasePath`. This should be done in the locator if necessary.
- Added `resetAssetBundles` to delete all registered bundles and `getAssetBundles` to get a list of bundles.
- Made `assetBundles` exclusively array for simplet code.
- Separated `urlPathToAbsolutePath` into `urlPathToStreamUri` so it's possible to do a `uri` <-> `url` conversion in addition to `uri` -> `url` -> `absolute path`.
- Added getter/setter pairs for `locatorScheme` & `baseUrl` properties.
- Removed unneeded class use and fixed some doc blocks
- Integrated development asset server from main UserFrosting project
- Implemented unit testing and continuous integration via AppVeyor and Travis
- Support for alternate bundling systems

## 4.1.0

- Bump dependencies, factor out Util

## 4.0.1

- Update composer dependencies
