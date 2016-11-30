# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

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
