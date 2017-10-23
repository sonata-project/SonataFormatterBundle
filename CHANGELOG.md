# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [3.3.0](https://github.com/sonata-project/SonataFormatterBundle/compare/3.2.3...3.3.0) - 2017-10-22
### Removed
- Support for old versions of PHP and Symfony.

## [3.2.3](https://github.com/sonata-project/SonataFormatterBundle/compare/3.2.2...3.2.3) - 2017-09-19
### Fixed
- Fixed passing null to transformer

## [3.2.2](https://github.com/sonata-project/SonataFormatterBundle/compare/3.2.1...3.2.2) - 2017-06-14
### Fixed
- Fix the files browser view
- Fixed twig 2 bug when updating rich text

## [3.2.1](https://github.com/sonata-project/SonataFormatterBundle/compare/3.2.0...3.2.1) - 2017-03-23
### Added
- Added compatibility with 4.0 of admin bundle and media bundle
- Add compatibility with `egeloen/ckeditor-bundle 5.0`

### Fixed
- Fixed issue with saving `sonata.formatter.block.formatter` when using within SonataAdminBundle (or Sonata sandbox project)
- Twig 2.0 compatibility
- Use `request_stack`if it is present on `CkEditorAdminController`
- Improve the way we set custom `formTheme` for twig
- Port minor fixes already applied on `SonataMediaBundle` media list

## [3.2.0](https://github.com/sonata-project/SonataFormatterBundle/compare/3.1.0...3.2.0) - 2017-03-01
### Added
- Added the possibility to use `templates` defined in your `ivory_ckeditor.yml` in the `sonata_formatter_type` and `sonata_simple_formatter_type`
- support for Twig 2.0

### Deprecated
- not specifying the `default_formatter` configuration node is deprecated
- specifying a logger through the `SonataFormatterBundle\Formatter\Pool` constructor is deprecated in favor of specifying it through `setLogger` method
- not specifying a default formatter to the `SonataFormatterBundle\Formatter\Pool` constructor

### Fixed
- Broken markitup header for rawhtml format in sonata_simple_formatter_type_widget
- Remove wrong implements from `TextFormatterExtension`

## [3.1.0](https://github.com/sonata-project/SonataFormatterBundle/compare/3.0.1...3.1.0) - 2016-11-30
### Added
- Added `SonataAdminBundle` to suggest
- Added `SonataMediaBundle` to suggest
- Added support for `PluginManagerInterface` in `FormatterType` and `SimpleFormatterType`
- Italian translation

### Fixed
- Fix overwriting formatter selection
- Field with `sonata_simple_formatter_type` widget in a block in sonata page's composer doesn't update
- Fixed `FormatterType` for Symfony >=3.0
- Fixed duplicate translation on the format selector
- Removed deprecation warning for `AdminExtension` usage.
- Fixed usage of `choice_translation_domain` in `FormatterType`
- `SimpleFormatterType` now compatible with Symfony 3

### Removed
- internal test classes are now excluded from the autoloader

## [3.0.1](https://github.com/sonata-project/SonataFormatterBundle/compare/3.0.0...3.0.1) - 2016-05-22
### Fixed
- Remove not existent `CKEditorCompilerPass` from bundle build.

## [3.0.0](https://github.com/sonata-project/SonataFormatterBundle/compare/2.3.4...3.0.0) - 2016-05-21
### Changed
- Update [IvoryCKEditorBundle](https://github.com/egeloen/IvoryCKEditorBundle) to version 3.x.
- Bump `egeloen/ckeditor-bundle` required version to 4.0.
