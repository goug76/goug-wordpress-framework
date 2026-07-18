# ADR-009 – Panel Visibility

**Status:** Accepted

## Context

Users needed the ability to hide dashboard panels while maintaining a consistent registry of available panels.

Several approaches were considered:

- Removing panels from the registry
- Hiding panels during rendering
- Filtering prepared dashboard data

## Decision

Panel visibility is implemented during dashboard data preparation.

The Dashboard Registry always contains every registered panel.

User preferences determine which panels are exposed through the prepared Dashboard_Data object.

Templates remain presentation-only and render only the prepared data.

## Consequences

### Positive

- Registry remains immutable.
- Templates stay simple.
- Visibility logic is centralized.
- Provides a foundation for future drag-and-drop layouts.

### Negative

- Requires one preprocessing step before rendering.

---

## Maintaining ADRs

Architecture Decision Records are intended to document significant architectural decisions that affect the long-term structure of GOUG Framework.

ADRs are permanent historical records. Existing records should not be rewritten to reflect new decisions. Instead, new ADRs should supersede earlier decisions while preserving the architectural history of the project.