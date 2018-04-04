# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/).

## [1.7.3] - 2018-04-04
### Fixed
 - Fix invalid column name in findOne() condition #118

## [1.7.2] - 2018-02-23
### Minor
 - Restrict version constraints for Yii dependencies in composer.json #116

## [1.7.1] - 2017-10-30
### Fixed
 - Fix url creation in translate action #108
 - Fix incorrect md5 hash for non-English characters in javascripts #114

## [1.7.0] - 2017-04-30
### Added
 - Allow to override default scanners via module configuration #87
 - Namespaced migration support #101

### Security
 - Limit access to Import/Export actions #89

### Misc
 - Module access enhancements #86
 - Add PHP-CS-Fixer config and fix coding style #95
 - Add usage hint for the search empty command #102

## [1.6.0] - 2016-07-12
### Added
 - Add db connection to module settings #85

## [1.5.4] - 2016-04-10
### Added
 - Add option to specify whether scan the parent directory of the root, or the root directory itself #73
 - Add multiple root directory scan #77

## [1.5.3] - 2016-04-05
### Added
 - Add TranslateBehavior

### Changed
 - Move headers (h1) from views to layout #53

### Fixed
 - Fix translation completeness percentage calculation #51
 - Load the correct translation in DialogAction #57
 - Fix PHP7 incompatibility in Database Scanner #58
 - Fix multiple translators detection in scanners #59
 - Run JavaScript generators for active languages after import #68

## [1.5.2] - 2016-02-12
### Added
 - Add ability to define category of database translations #41

### Changed
 - Display enabled languages firsts in language list #44

### Fixed
 - Fix wrong translation returned #43
 - Restore searchEmptyCommand functionality #50

## [1.5.1] - 2015-11-16
### Added
 - Ability to change source language on translation page

### Changed
 - Optimizations in language search model

### Fixed
 - Error if extend Module #38
 - Filter case sensitive in PostgreSQL #39
 - Fixes in language search model

## [1.5.0]  - 2015-09-01
### Added
 - Import/export feature #31

### Changed
 - Autocofus translation textarea in frontend translation dialog #33

### Fixed
 - Round error in translation statistic

[1.7.3]: https://github.com/lajax/yii2-translate-manager/compare/1.7.2...1.7.3
[1.7.2]: https://github.com/lajax/yii2-translate-manager/compare/1.7.1...1.7.2
[1.7.1]: https://github.com/lajax/yii2-translate-manager/compare/1.7.0...1.7.1
[1.7.0]: https://github.com/lajax/yii2-translate-manager/compare/1.6.0...1.7.0
[1.6.0]: https://github.com/lajax/yii2-translate-manager/compare/1.5.4...1.6.0
[1.5.4]: https://github.com/lajax/yii2-translate-manager/compare/1.5.3...1.5.4
[1.5.3]: https://github.com/lajax/yii2-translate-manager/compare/1.5.2...1.5.3
[1.5.2]: https://github.com/lajax/yii2-translate-manager/compare/1.5.1...1.5.2
[1.5.1]: https://github.com/lajax/yii2-translate-manager/compare/1.5.0...1.5.1
[1.5.0]: https://github.com/lajax/yii2-translate-manager/compare/1.4.9...1.5.0

