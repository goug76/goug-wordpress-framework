# ADR-006 – Dashboard Preference Architecture

**Status:** Accepted

## Context

As GOUG Framework evolved beyond a static dashboard, user personalization became a first-class feature. Dashboard preferences such as density, greeting visibility, motion preferences, and panel visibility required persistent per-user storage.

Directly storing these values within dashboard classes would tightly couple presentation logic to persistence and make future framework modules difficult to implement.

A reusable solution was required.

## Decision

Dashboard preferences are exposed through a dedicated `User_Preferences_Service`.

This service acts as a façade over the generic `User_Settings_Service`, translating dashboard-specific preference names into framework setting identifiers.

Dashboard components interact exclusively with the façade and remain unaware of the underlying storage implementation.

## Consequences

### Positive

- Separates dashboard logic from persistence.
- Allows the settings framework to be reused by future modules.
- Keeps dashboard APIs stable.
- Makes preference storage independently testable.

### Negative

- Introduces one additional service layer.
- Adds a small amount of indirection during request handling.

---

## Maintaining ADRs

Architecture Decision Records are intended to document significant architectural decisions that affect the long-term structure of GOUG Framework.

ADRs are permanent historical records. Existing records should not be rewritten to reflect new decisions. Instead, new ADRs should supersede earlier decisions while preserving the architectural history of the project.