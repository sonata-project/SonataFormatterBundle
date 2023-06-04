# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [5.3.0](https://github.com/sonata-project/SonataFormatterBundle/compare/5.2.0...5.3.0) - 2023-06-04
### Added
- [[#762](https://github.com/sonata-project/SonataFormatterBundle/pull/762)] Support for SonataBlockBundle 5.0 ([@jordisala1991](https://github.com/jordisala1991))

## [5.2.0](https://github.com/sonata-project/SonataFormatterBundle/compare/5.1.0...5.2.0) - 2023-05-13
### Added
- [[#759](https://github.com/sonata-project/SonataFormatterBundle/pull/759)] Support for `sonata-project/form-extensions` 2.0 ([@jordisala1991](https://github.com/jordisala1991))

## [5.1.0](https://github.com/sonata-project/SonataFormatterBundle/compare/5.0.2...5.1.0) - 2023-04-25
### Removed
- [[#749](https://github.com/sonata-project/SonataFormatterBundle/pull/749)] Support for Symfony 4 ([@jordisala1991](https://github.com/jordisala1991))
- [[#749](https://github.com/sonata-project/SonataFormatterBundle/pull/749)] Support for Twig 2 ([@jordisala1991](https://github.com/jordisala1991))
- [[#744](https://github.com/sonata-project/SonataFormatterBundle/pull/744)] Drop support for PHP 7.4 ([@jordisala1991](https://github.com/jordisala1991))
- [[#739](https://github.com/sonata-project/SonataFormatterBundle/pull/739)] Support for Symfony 6.0 and 6.1 ([@SonataCI](https://github.com/SonataCI))

## [5.0.2](https://github.com/sonata-project/SonataFormatterBundle/compare/5.0.1...5.0.2) - 2023-01-08
### Fixed
- [[#735](https://github.com/sonata-project/SonataFormatterBundle/pull/735)] Fix sonata_formatter_type_widget with single format option template rendering ([@azlotnikov](https://github.com/azlotnikov))

## [5.0.1](https://github.com/sonata-project/SonataFormatterBundle/compare/5.0.0...5.0.1) - 2022-09-28
### Fixed
- [[#722](https://github.com/sonata-project/SonataFormatterBundle/pull/722)] Fix `FormatterType` listener option, now it should update `target_field` when listener is set to "true" (its default value). ([@jordisala1991](https://github.com/jordisala1991))
- [[#721](https://github.com/sonata-project/SonataFormatterBundle/pull/721)] Fix FormatterBlockService to work with current form. ([@haivala](https://github.com/haivala))

## [5.0.0](https://github.com/sonata-project/SonataFormatterBundle/compare/5.0.0-alpha-1...5.0.0) - 2022-07-12
- No significant changes

## [5.0.0-alpha-1](https://github.com/sonata-project/SonataFormatterBundle/compare/4.x...5.0.0-alpha-1) - 2022-06-25
### Added
- [[#641](https://github.com/sonata-project/SonataFormatterBundle/pull/641)] Added support for Symfony 5 / Sonata Admin 4 / Twig 3. ([@jorrit](https://github.com/jorrit))
- [[#683](https://github.com/sonata-project/SonataFormatterBundle/pull/683)] Add validations to form type options. ([@jordisala1991](https://github.com/jordisala1991))
- [[#696](https://github.com/sonata-project/SonataFormatterBundle/pull/696)] Added `MediaExtension` to render Media inside Formatters. ([@jordisala1991](https://github.com/jordisala1991))
- [[#696](https://github.com/sonata-project/SonataFormatterBundle/pull/696)] Added ability to use Twig runtimes on your Formatter Extensions via `getAllowedRuntimes`. ([@jordisala1991](https://github.com/jordisala1991))
- [[#663](https://github.com/sonata-project/SonataFormatterBundle/pull/663)] Added support for CKeditorBundle. ([@VincentLanglet](https://github.com/VincentLanglet))
- [[#653](https://github.com/sonata-project/SonataFormatterBundle/pull/653)] Added support for `psr/log` ^2.0 and ^3.0. ([@jordisala1991](https://github.com/jordisala1991))
- [[#646](https://github.com/sonata-project/SonataFormatterBundle/pull/646)] Support for Symfony 6 ([@VincentLanglet](https://github.com/VincentLanglet))

### Changed
- [[#699](https://github.com/sonata-project/SonataFormatterBundle/pull/699)] Changed `gist` tag to be a function instead. ([@jordisala1991](https://github.com/jordisala1991))
- [[#691](https://github.com/sonata-project/SonataFormatterBundle/pull/691)] `format_text` filter is now lazy loaded. ([@jordisala1991](https://github.com/jordisala1991))

### Fixed
- [[#679](https://github.com/sonata-project/SonataFormatterBundle/pull/679)] Fix ckeditor browser and upload actions. ([@jordisala1991](https://github.com/jordisala1991))
- [[#664](https://github.com/sonata-project/SonataFormatterBundle/pull/664)] Adding extensions on formatters with config ([@VincentLanglet](https://github.com/VincentLanglet))

### Removed
- [[#683](https://github.com/sonata-project/SonataFormatterBundle/pull/683)] Remove `event_dispatcher` on FormatterType, since we provide it internally. ([@jordisala1991](https://github.com/jordisala1991))
- [[#701](https://github.com/sonata-project/SonataFormatterBundle/pull/701)] Removed template configuration. ([@jordisala1991](https://github.com/jordisala1991))
- [[#694](https://github.com/sonata-project/SonataFormatterBundle/pull/694)] Remove `Sonata\FormatterBundle\Extension\BaseProxyExtension`. ([@jordisala1991](https://github.com/jordisala1991))
- [[#691](https://github.com/sonata-project/SonataFormatterBundle/pull/691)] Clean up deprecated methods for Twig. ([@jordisala1991](https://github.com/jordisala1991))
- [[#687](https://github.com/sonata-project/SonataFormatterBundle/pull/687)] Removed `ExtendableFormatter` and `BaseFormatter`. ([@jordisala1991](https://github.com/jordisala1991))
- [[#686](https://github.com/sonata-project/SonataFormatterBundle/pull/686)] All container parameters that were used to customize classes. ([@jordisala1991](https://github.com/jordisala1991))
- [[#678](https://github.com/sonata-project/SonataFormatterBundle/pull/678)] Remove support for Symfony 5.3. ([@jordisala1991](https://github.com/jordisala1991))

## [4.7.0](https://github.com/sonata-project/SonataFormatterBundle/compare/4.6.1...4.7.0) - 2022-06-25
### Changed
- [[#672](https://github.com/sonata-project/SonataFormatterBundle/pull/672)] Bump minimum SonataAdminBundle and SonataMediaBundle versions required. ([@jordisala1991](https://github.com/jordisala1991))

### Fixed
- [[#672](https://github.com/sonata-project/SonataFormatterBundle/pull/672)] Fix deprecation with Twig 2. ([@jordisala1991](https://github.com/jordisala1991))

## [4.6.1](https://github.com/sonata-project/SonataFormatterBundle/compare/4.6.0...4.6.1) - 2021-09-14
### Fixed
- [[#607](https://github.com/sonata-project/SonataFormatterBundle/pull/607)] Fix `sonata.formatter.block.formatter` service registration ([@core23](https://github.com/core23))

## [4.6.0](https://github.com/sonata-project/SonataFormatterBundle/compare/4.5.0...4.6.0) - 2021-09-13
### Added
- [[#506](https://github.com/sonata-project/SonataFormatterBundle/pull/506)] Add support for `sonata-project/block-bundle` 4.x ([@core23](https://github.com/core23))

## [4.5.0](https://github.com/sonata-project/SonataFormatterBundle/compare/4.4.0...4.5.0) - 2021-04-19
### Added
- [[#570](https://github.com/sonata-project/SonataFormatterBundle/pull/570)] Added php 8 support ([@jordisala1991](https://github.com/jordisala1991))

## [4.4.0](https://github.com/sonata-project/SonataFormatterBundle/compare/4.3.0...4.4.0) - 2021-02-15
### Added
- [[#523](https://github.com/sonata-project/SonataFormatterBundle/pull/523)] Added support for `symfony/validator` ^5.1. ([@jorrit](https://github.com/jorrit))

### Changed
- [[#519](https://github.com/sonata-project/SonataFormatterBundle/pull/519)] Updates dutch translations ([@zghosts](https://github.com/zghosts))

## [4.3.0](https://github.com/sonata-project/SonataFormatterBundle/compare/4.2.0...4.3.0) - 2020-09-25
### Added
- [[#477](https://github.com/sonata-project/SonataFormatterBundle/pull/477)] Added support for symfony/options-resolver:^5.1 ([@phansys](https://github.com/phansys))
- [[#477](https://github.com/sonata-project/SonataFormatterBundle/pull/477)] Added support for symfony/property-access:^5.1 ([@phansys](https://github.com/phansys))

### Changed
- [[#472](https://github.com/sonata-project/SonataFormatterBundle/pull/472)] Allow FOSCKEditor dependency to ^2.0 ([@bmaziere](https://github.com/bmaziere))

### Deprecated
- [[#472](https://github.com/sonata-project/SonataFormatterBundle/pull/472)] Deprecate usage of FOSCKEditor ^1.0 ([@bmaziere](https://github.com/bmaziere))

### Removed
- [[#469](https://github.com/sonata-project/SonataFormatterBundle/pull/469)] Support for Symfony < 4.4 ([@wbloszyk](https://github.com/wbloszyk))

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
