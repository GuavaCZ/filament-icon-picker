# Changelog

## [5.0.0](https://github.com/GuavaCZ/filament-icon-picker/compare/4.0.0...5.0.0) (2026-08-04)


### ⚠ BREAKING CHANGES

* fuse.js is replaced by @leeoniya/ufuzzy.
* getSetJs(), getIconsJs() and getIconSvgJs() are removed; the package now registers two routes under the _icon-picker prefix.
* getIcon() now returns null for ids with no file, and icon labels drop the set prefix ("O academic cap", not "Heroicon o academic cap").
* CanBeCacheable is removed and cache config moved from icon-picker.cache.* to filament-icon-picker.cache.*.

### Features

* cache icon listings server-side and rename the config namespace ([233c742](https://github.com/GuavaCZ/filament-icon-picker/commit/233c742acf0410e732db1866045bee7fee933d20))
* sanitize uploaded svg icons and restrict icon labels ([dff53e0](https://github.com/GuavaCZ/filament-icon-picker/commit/dff53e068846027c1802afced83f571ba4e5fb60))
* serve icons over token-authorized endpoints instead of Livewire ([4beaf26](https://github.com/GuavaCZ/filament-icon-picker/commit/4beaf260b66275aa15fe96f12024ded086ca616f))
* swap fuse.js for uFuzzy and batch svg fetching on the client ([953904c](https://github.com/GuavaCZ/filament-icon-picker/commit/953904c75c2ba50c574af79283f1402bcf975d30))


### Bug Fixes

* check that custom icons exist instead of matching the set prefix ([3269661](https://github.com/GuavaCZ/filament-icon-picker/commit/3269661fca68996b1195de0010b39fb9fb384f62))
* correct PSR-4 violation in CanBeCacheable and tighten icon manager types ([76a9dd9](https://github.com/GuavaCZ/filament-icon-picker/commit/76a9dd9fd3d00621f5c84cc23710aee11f5ee445))
* enforce scope and allowed sets when resolving icons ([e8c4e51](https://github.com/GuavaCZ/filament-icon-picker/commit/e8c4e51920aa70ca005ea32ed7310efd53425e56))
* use one source of truth for the custom icon set id ([bc4be45](https://github.com/GuavaCZ/filament-icon-picker/commit/bc4be452b297e4e94c1b732752292a16bc7ee4ed))


### Performance

* resolve single icons by file lookup instead of listing the set ([4904607](https://github.com/GuavaCZ/filament-icon-picker/commit/4904607b995fafbe75838986f1b65193b8a7a0ea))


### Refactor

* centralize custom icon scope ids in IconScope ([e8d8e44](https://github.com/GuavaCZ/filament-icon-picker/commit/e8d8e44d6ce4919e87623885db7098e1456e4200))
