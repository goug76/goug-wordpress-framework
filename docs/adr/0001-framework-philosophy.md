# ADR-0001: Framework Philosophy

- **Status:** Accepted
- **Date:** 2026-07-21
- **Decision Type:** Foundational

---

# Context

The GOUG Dashboard began as a custom WordPress dashboard built specifically for GeneratePress child themes.

As development progressed, it became clear that much of the code being written was not dashboard-specific. Instead, it consisted of reusable infrastructure including:

- View rendering
- Asset management
- Settings handling
- Registries
- Bootstrapping
- Shared UI components

At the same time, new ideas emerged for additional functionality that had nothing to do with the dashboard itself, including:

- Role Manager
- Maintenance Mode
- Activity Log
- Redirect Manager
- Future developer tools

Attempting to build these features directly into the dashboard would have created a monolithic codebase with tightly coupled functionality.

A new architectural approach was required.

---

# Decision

The project will evolve from a custom dashboard into a modular framework.

The framework will provide only shared architectural infrastructure.

All user-facing functionality will exist as independent modules built on top of that infrastructure.

The Dashboard becomes the first official module rather than the center of the system.

---

# Principles

The framework follows these guiding principles:

- WordPress remains the platform.
- Core provides architecture, not features.
- Modules own business logic.
- Public APIs are intentional.
- Shared abstractions must be earned.
- Performance is a feature.
- Simplicity is preferred over unnecessary flexibility.
- The framework should embrace WordPress rather than replace it.

---

# Consequences

## Positive

- Clear separation of concerns.
- Easier maintenance.
- Reusable infrastructure.
- Consistent development patterns.
- Independent feature modules.
- Smaller Core over time.

## Trade-offs

- Additional upfront architectural planning.
- Strong discipline required when deciding what belongs in Core.
- Public APIs become long-term compatibility commitments.

These trade-offs are considered worthwhile to create a stable, extensible framework.

---

# Alternatives Considered

## Continue building the dashboard directly

Rejected because unrelated features would become tightly coupled.

---

## Build a monolithic plugin

Rejected because it would blur the line between infrastructure and business logic.

---

## Build a Laravel-style application framework

Rejected because the goal is to extend WordPress, not replace its architecture.

---

# Result

The GOUG Framework will be developed as a lightweight architectural layer for WordPress.

Core will remain intentionally small.

Modules will provide functionality.

Future architectural decisions will be evaluated against this philosophy.

---

# Related Documents

- `docs/FRAMEWORK.md`
- `docs/ARCHITECTURE.md`