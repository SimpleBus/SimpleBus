# Change log

## [Unreleased][unreleased]

### Added

- Added a change log

## [2.0.1] - 20-01-2015

### Changed

- When domain events are collected from entities, they are now also erased, to prevent processing the same events again.
- Tests were added, which uncovered a serious bug, which has been fixed as well.

## [2.0.0] - 19-01-2015

### Changed

- Instead of SimpleBus/CommandBus and SimpleBus/EventBus 1.0 this library now uses SimpleBus/MessageBus 1.0.

[unreleased]: https://github.com/simple-bus/doctrine-orm-bridge/compare/v2.0.1...HEAD
[2.0.1]: https://github.com/simple-bus/doctrine-orm-bridge/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/simple-bus/doctrine-orm-bridge/compare/v1.0.0...v2.0.0
