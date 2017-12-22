# Change log

## [Unreleased][unreleased]

- Update change log

## [3.0.0] - 23-01-2015

### Added

- Added a change log

### Changed

- When a database transaction fails, the entity manager closes itself down. This used to make the command bus useless.
Now the command bus is able to restore itself by asking the manager registry to reset the manager.

## [2.0.1] - 20-01-2015

### Changed

- When domain events are collected from entities, they are now also erased, to prevent processing the same events again.
- Tests were added, which uncovered a serious bug, which has been fixed as well.

## [2.0.0] - 19-01-2015

### Changed

- Instead of SimpleBus/CommandBus and SimpleBus/EventBus 1.0 this library now uses SimpleBus/MessageBus 1.0.

[unreleased]: https://github.com/SimpleBus/DoctrineORMBridge/compare/v3.0.0...HEAD
[3.0.0]: https://github.com/SimpleBus/DoctrineORMBridge/compare/v2.0.1...v3.0.0
[2.0.1]: https://github.com/SimpleBus/DoctrineORMBridge/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/SimpleBus/DoctrineORMBridge/compare/v1.0.0...v2.0.0
