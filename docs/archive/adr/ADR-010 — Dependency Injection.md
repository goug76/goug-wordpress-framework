# ADR-010 – Dependency Injection

**Status:** Accepted

## Context

Early versions of the framework instantiated shared services directly inside dependent classes.

As the framework expanded, this increased coupling and duplicated object creation.

## Decision

Shared framework services are injected through constructors whenever practical.

Coordinator classes compose the object graph and provide dependencies to consumers.

Classes should never construct shared services that they do not own.

## Consequences

### Positive

- Reduced coupling.
- Easier testing.
- Clear ownership.
- Consistent composition model.
- Better long-term maintainability.

### Negative

- Constructors become larger as the framework grows.

---

## Maintaining ADRs

Architecture Decision Records are intended to document significant architectural decisions that affect the long-term structure of GOUG Framework.

ADRs are permanent historical records. Existing records should not be rewritten to reflect new decisions. Instead, new ADRs should supersede earlier decisions while preserving the architectural history of the project.