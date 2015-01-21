# Change log

## [2.0.1]

### Changed

- When domain events are collected from entities, they are now also erased, to prevent processing the same events again.
- Tests were added, which uncovered a serious bug, which has been fixed as well.

## [2.0.0]

### Changed

- Instead of SimpleBus/CommandBus and SimpleBus/EventBus 1.0 this library now uses SimpleBus/MessageBus 1.0.

