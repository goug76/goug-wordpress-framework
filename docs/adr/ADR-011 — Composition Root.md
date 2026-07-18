# ADR-011 – Composition Root

**Status:** Accepted

## Context

As additional services, controllers, and providers were introduced, object creation became increasingly complex.

Allowing arbitrary classes to instantiate their own dependencies would lead to inconsistent object lifecycles and tighter coupling.

## Decision

The Dashboard coordinator serves as the composition root for the dashboard subsystem.

Its responsibilities include:

- Constructing shared services.
- Registering controllers.
- Building Dashboard_Data.
- Bootstrapping the dashboard.

All other classes receive dependencies through constructor injection.

## Consequences

### Positive

- Centralized object creation.
- Predictable dependency graph.
- Lower coupling.
- Easier future refactoring.
- Clear ownership of framework services.

### Negative

- The coordinator grows as new subsystems are introduced.
- Requires discipline to avoid bypassing the composition root.

---

## Maintaining ADRs

Architecture Decision Records are intended to document significant architectural decisions that affect the long-term structure of GOUG Framework.

ADRs are permanent historical records. Existing records should not be rewritten to reflect new decisions. Instead, new ADRs should supersede earlier decisions while preserving the architectural history of the project.