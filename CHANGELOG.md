# Changelog

All notable changes to `radio-deck` will be documented in this file.

## Unreleased

### Added

* Filament v5 enum support: `options(Enum::class)` now reads `HasDescription`, `HasIcon` and `HasColor` automatically
* `relationship()` for `BelongsTo` and `BelongsToMany`, with `pivotData()` and `getOption{Label,Description,Icon,Color}FromRecordUsing()`
* `searchable()`, `bulkToggleable()`, `minItems()` and `maxItems()` for multiple selection
* `boolean()`, `inline()`, `tooltips()` and `selectionIndicator()`
* `Heroicon` enum and `Htmlable` icons, and `Color` palette arrays
* Stable `fi-fo-radio-deck-*` CSS hook classes

### Fixed

* Submitted values are now validated against the enabled options
* Selected values are cast with Filament state casts, so enum and array model casts work
* Cards are keyboard accessible: inputs are visually hidden instead of `display: none`, with a focus ring, `radiogroup`/`group` roles and ARIA attributes
* Cards show a danger ring when the field has a validation error
* `gridDirection(GridDirection::Column)` is now respected (rows remain the default)
* Icons, descriptions and colors are no longer re-evaluated for every option

### Deprecated

* `JaOcero\RadioDeck\Contracts\HasDescriptions` and `HasIcons`. Use Filament's `HasDescription` and `HasIcon`

### Removed

* The unused Tailwind CSS v3 build pipeline and the empty `TestsRadioDeck` testing mixin

## v1.4 - 2025-08-28

### What's Changed

* Reverted back to the commit id : c5083ce by @shovann-notebrains in https://github.com/199ocero/radio-deck/pull/37

### New Contributors

* @shovann-notebrains made their first contribution in https://github.com/199ocero/radio-deck/pull/37

**Full Changelog**: https://github.com/199ocero/radio-deck/compare/v1.3...v1.4

## v1.2.10 - 2025-02-27

### What's Changed

* add support for laravel 12 by @atmonshi in https://github.com/199ocero/radio-deck/pull/29

**Full Changelog**: https://github.com/199ocero/radio-deck/compare/v1.2.9...v1.2.10

## v1.2.9 - 2024-12-07

### What's Changed

* Import `Closure` by @austincarpenter in https://github.com/199ocero/radio-deck/pull/26

### New Contributors

* @austincarpenter made their first contribution in https://github.com/199ocero/radio-deck/pull/26

**Full Changelog**: https://github.com/199ocero/radio-deck/compare/v1.2.8...v1.2.9

## v1.2.8 - 2024-06-25

### What's Changed

* Fix state when multiple is enabled by @stvtk in https://github.com/199ocero/radio-deck/pull/21

### New Contributors

* @stvtk made their first contribution in https://github.com/199ocero/radio-deck/pull/21

**Full Changelog**: https://github.com/199ocero/radio-deck/compare/v1.2.7...v1.2.8

## v1.2.7 - 2024-05-30

### What's Changed

* Adds checkbox multiple support by @dmason30 in https://github.com/199ocero/radio-deck/pull/18
* update read me by @199ocero in https://github.com/199ocero/radio-deck/pull/19

### New Contributors

* @dmason30 made their first contribution in https://github.com/199ocero/radio-deck/pull/18

**Full Changelog**: https://github.com/199ocero/radio-deck/compare/v1.2.6...v1.2.7

## v1.2.6 - 2024-05-27

### What's Changed

* Correct type hint by @juliangums in https://github.com/199ocero/radio-deck/pull/17

**Full Changelog**: https://github.com/199ocero/radio-deck/compare/v1.2.5...v1.2.6

## v1.2.5 - 2024-05-27

### What's Changed

* Remove broken facade by @juliangums in https://github.com/199ocero/radio-deck/pull/16

### New Contributors

* @juliangums made their first contribution in https://github.com/199ocero/radio-deck/pull/16

**Full Changelog**: https://github.com/199ocero/radio-deck/compare/v1.2.4...v1.2.5

## v1.2.4 - 2024-03-15

### What's Changed

* Allow customization of the card element by @OrangeJuiced in https://github.com/199ocero/radio-deck/pull/13

### New Contributors

* @OrangeJuiced made their first contribution in https://github.com/199ocero/radio-deck/pull/13

**Full Changelog**: https://github.com/199ocero/radio-deck/compare/v1.2.3...v1.2.4

## v1.2.3 - 2024-02-28

### What's Changed

* add Laravel 11 support by @atmonshi in https://github.com/199ocero/radio-deck/pull/12

### New Contributors

* @atmonshi made their first contribution in https://github.com/199ocero/radio-deck/pull/12

**Full Changelog**: https://github.com/199ocero/radio-deck/compare/v1.2.2...v1.2.3

## v1.2.2 - 2024-02-12

### What's Changed

* match ring color from filament input fields by @199ocero in https://github.com/199ocero/radio-deck/pull/11

**Full Changelog**: https://github.com/199ocero/radio-deck/compare/v1.2.1...v1.2.2

## v1.2.1 - 2024-01-19

### What's Changed

* Additional Method to customize the icon sizes by @AlexisSerneels in https://github.com/199ocero/radio-deck/pull/7

**Full Changelog**: https://github.com/199ocero/radio-deck/compare/v1.2.0...v1.2.1

## v1.2.0 - 2024-01-19

### What's Changed

* Additional methods to allow more customization by @AlexisSerneels in https://github.com/199ocero/radio-deck/pull/6

### New Contributors

* @AlexisSerneels made their first contribution in https://github.com/199ocero/radio-deck/pull/6

**Full Changelog**: https://github.com/199ocero/radio-deck/compare/v1.1.2...v1.2.0

## v1.1.2 - 2024-01-04

### What's Changed

* support enums for descriptions and icons by @199ocero in https://github.com/199ocero/radio-deck/pull/5

### New Contributors

* @199ocero made their first contribution in https://github.com/199ocero/radio-deck/pull/5

**Full Changelog**: https://github.com/199ocero/radio-deck/compare/v1.1.1...v1.1.2

## v1.1.1 - 2024-01-02

### What's Changed

* Fix icon not visible for boolean fields by @CodeWithDennis in https://github.com/199ocero/radio-deck/pull/3
* Fix ring color not showing in light mode

**Full Changelog**: https://github.com/199ocero/radio-deck/compare/v1.1.0...v1.1.1

## Version 1.1.0: Added Support for Custom Colors and Code Cleanup - 2024-01-01

Clean up some files, remove unnecessary variables and also make some minor performance nice tip thanks to @ralphjsmit at discord. This release also supports the custom color that will be visible to card ring and icons.

### What's Changed

* Cleanup files by @CodeWithDennis in https://github.com/199ocero/radio-deck/pull/1

### New Contributors

* @CodeWithDennis made their first contribution in https://github.com/199ocero/radio-deck/pull/1

**Full Changelog**: https://github.com/199ocero/radio-deck/compare/v1.0.0-beta...v1.1.0

## v1.0.0-beta - 2023-12-28

First beta release

## 1.0.0 - 202X-XX-XX

- initial release
