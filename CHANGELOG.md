# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- Option to recieve a `UserFrosting\Support\Repository\Repository` instance from UserFrosting\Assets\AssetBundles\GulpBundleAssetsBundles->readSchema` function by providing true for the second argument.

### Changed
- `GulpBundleAssets*Bundles` classes simplified using ``UserFrosting\Support\Repository\Repository` returned by extended `readSchema`.

## [4.2.0] - 2019-01-10
### Added
- Unit testing.
- Automated testing via AppVeyor and Travis.
- `UserFrosting\Assets\Assets->resetAssetBundles` to clear all registered bundles.
- `UserFrosting\Assets\Assets->getAssetBundles` to get list of all registered bundles.
- Getter/Setter pairs for `locatorScheme` & `baseUrl` properties on `UserFrosting\Assets\Assets`.
- Integrated development asset server from the main UserFrosting project.
- Support for integration of alternate bundling systems.

### Changed
- Simplified `UserFrosting\Assets\Assets` implementation to improve maintainability.
- Extracted part of logic in `UserFrosting\Assets\Assets->urlPathToAbsolutePath` into `UserFrosting\Assets\Assets->urlPathToStreamUri` to make it possible to perform a `uri <-> url` conversion in addition to `uri -> url -> absolute path`.
- Returned absolute URLs revised to allow transformation back into underlying locator URI. For example, the `assets://vendor/bootstrap/js/bootstrap.js` URI will now return `http://example.com/assets/vendor/bootstrap/js/bootstrap.js` instead of `http://example.com/vendor/bootstrap/js/bootstrap.js`. This permits the usage of multiple locator schemas like `assets://` and `images://`. As this change makes the generated URL an analog of the used locator URI, some verbosity (such as an indication of which sprinkle a resource is coming from in UserFrosting) is now unavailable.

### Fixed
- Various internal documentation errors.

### Removed
- `overwriteBasePath` method which should be addressed within the locator is required.
- `PathTransformer` class.
- Unnecessary `use` statements.

## [4.1.0] - 2017-06-19
Bump dependencies, factor out Util.

## [4.0.1] - 2017-03-09
Update composer dependencies.

## [4.0.0] - 2017-02-19
Initial 4.x release.

[4.2.0]: https://github.com/userfrosting/assets/compare/4.1.0...4.2.0
[4.1.0]: https://github.com/userfrosting/assets/compare/4.0.1...4.1.0
[4.0.1]: https://github.com/userfrosting/assets/compare/4.0.0...4.0.1
