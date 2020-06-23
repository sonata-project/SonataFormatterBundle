# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [4.2.0](https://github.com/sonata-project/SonataFormatterBundle/compare/4.1.3...4.2.0) - 2020-06-23
### Added
- [[#417](https://github.com/sonata-project/SonataFormatterBundle/pull/417)]
  Added new `Formatter` interface ([@core23](https://github.com/core23))
- [[#417](https://github.com/sonata-project/SonataFormatterBundle/pull/417)]
  Added new `ExtendableFormatter` interface
([@core23](https://github.com/core23))

### Fixed
- [[#411](https://github.com/sonata-project/SonataFormatterBundle/pull/411)]
  Fix possibility to easily switch the toolbar configuration by using the
`full`, `standard` or `basic` keyword as toolbar with `SimpleFormatterType`
([@mkiszka](https://github.com/mkiszka))
- [[#435](https://github.com/sonata-project/SonataFormatterBundle/pull/435)]
  Potential crash in `SecurityPolicyContainerAware::checkMethodAllowed()`
([@greg0ire](https://github.com/greg0ire))

### Removed
- [[#464](https://github.com/sonata-project/SonataFormatterBundle/pull/464)]
  Support for Symfony < 4.3 ([@wbloszyk](https://github.com/wbloszyk))
- [[#459](https://github.com/sonata-project/SonataFormatterBundle/pull/459)]
  Remove SonataCoreBundle dependencies
([@wbloszyk](https://github.com/wbloszyk))

## [4.1.3](https://github.com/sonata-project/SonataFormatterBundle/compare/4.1.2...4.1.3) - 2019-06-13

### Fixed
- Removed Twig deprecations
- Fix deprecation for symfony/config 4.2+

## [4.1.2](https://github.com/sonata-project/SonataFormatterBundle/compare/4.1.1...4.1.2) - 2018-10-08
### Fixed
- Wrong typehint on `setFormTheme` method

## [4.1.1](https://github.com/sonata-project/SonataFormatterBundle/compare/4.1.0...4.1.1) - 2018-10-04
### Fixed
- Catch null values in FormatterListener

## [4.1.0](https://github.com/sonata-project/SonataFormatterBundle/compare/4.0.0...4.1.0) - 2018-09-30

### Added
- the `final` Sonata\FormatterBundle\Formatter\Pool class now implements a `Sonata\FormatterBundle\Formatter\PoolInterface`

## [4.0.0](https://github.com/sonata-project/SonataFormatterBundle/compare/3.5.0...4.0.0) - 2018-09-29

### Added
- PHP 7.1 type hints

### Removed
- ckeditor assets
- support for PHP 5.5 through 7.0 was dropped
- support for Symfony 2.8 through 3.3 was dropped

## [3.5.0](https://github.com/sonata-project/SonataFormatterBundle/compare/3.4.1...3.5.0) - 2018-05-22
### Added
- Added support for stylesSet (ivory_ck_editor configuration)
- Added egeloen/ckeditor-bundle 6.0 dependency to composer.json

### Deprecated
- using of IvoryCKEditorBundle

### Fixed
- deprecations from the admin bundle about `render` vs `renderWithExtraParams`
- Ckeditor toolbar config not loading

## [3.4.1](https://github.com/sonata-project/SonataFormatterBundle/compare/3.4.0...3.4.1) - 2018-02-02
### Changed
- Switch all templates references to Twig namespaced syntax
- Switch from templating service to sonata.templating

### Fixed
- Fixed form initialisation in sf3+

## [3.4.0](https://github.com/sonata-project/SonataFormatterBundle/compare/3.3.0...3.4.0) - 2017-12-07
### Added
- Possibility to configure size of the image returned to ckeditor after upload.
- Added Russian translations

### Changed
- Rollback to PHP 5.6 as minimum support.
- Changed internal folder structure to `src`, `tests` and `docs`

### Fixed
- It is now allowed to install Symfony 4
- Fix for getRuntime on Symfony older than 3.4

### Removed
- Removed BC layer for older symfony versions
 
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
