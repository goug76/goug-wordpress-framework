# Changelog

All notable changes to GOUG Framework are documented in this file.

The format is inspired by **Keep a Changelog**, with entries organized by release milestone.

---

## [0.6.0-dev] - July 2026

### Added

#### Dashboard Personalization

- Dashboard Settings Controller
- User Preferences Service
- User Settings Service integration
- Dashboard Preferences modal
- Dashboard density preferences
- Greeting visibility preferences
- Motion preference support
- Panel visibility preferences
- Preference summary panel

#### Architecture

- Controller layer
- Settings framework
- Constructor dependency injection
- Dashboard composition root
- Dashboard coordinator pattern
- Generic user settings infrastructure

#### Dashboard

- Dynamic panel visibility
- Registry-driven preference generation
- Prepared dashboard data filtering
- Multi-user preference persistence

#### Documentation

- Expanded Architecture guide
- Architecture Decision Records
- Updated Roadmap
- Expanded Coding Standards
- Initial Changelog

---

### Changed

#### Dashboard

- Dashboard_Data now prepares only visible panels.
- Dashboard preferences now flow through the User_Preferences_Service facade.
- Dashboard controllers now coordinate request handling instead of embedding request logic within dashboard classes.

#### Architecture

- Rendering and personalization responsibilities were formally separated.
- Services are now composed through dependency injection.
- Coordinators are responsible for subsystem composition.

#### Documentation

- Documentation updated to reflect the 0.6 architecture.
- Project roadmap reorganized around development milestones.
- ADRs expanded to document major architectural decisions.

---

### Fixed

- Multi-user preference persistence.
- Panel visibility synchronization.
- Dashboard preference save workflow.
- Registry integration for preference generation.
- Preference summary accuracy.

---

## Previous Milestones

### 0.5.x

Foundation release introducing:

- Dashboard Framework
- Dashboard Registry
- Service Layer
- Panel Architecture
- Template System
- Shared Components
- Modern Asset Pipeline
- Initial Documentation

---

## Upcoming

The next milestone (0.7) will focus on dashboard interaction improvements, including:

- Collapsible panels
- Persistent panel state
- Drag-and-drop panel ordering
- Additional dashboard customization
- Improved developer APIs