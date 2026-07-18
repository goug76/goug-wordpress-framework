# ADR-001: Dashboard Architecture

**Status:** Accepted

**Date:** July 2026

---

# Context

Early versions of the dashboard mixed business logic, data collection, and presentation within individual dashboard widgets.

As the framework expanded, this approach became increasingly difficult to maintain.

The framework required a consistent architecture that clearly separated responsibilities while remaining familiar to WordPress developers.

---

# Decision

GOUG Framework adopts a layered dashboard architecture.

```text
Services

↓

Panels

↓

Templates

↓

Components
```

Each layer has a single responsibility.

## Services

Services collect and normalize data.

They:

- query WordPress
- calculate values
- cache results
- return prepared structures

Services never render HTML.

---

## Panels

Panels register dashboard widgets.

Panels:

- declare metadata
- request service data
- provide templates with prepared information

Panels never perform business logic.

---

## Templates

Templates render HTML.

Templates:

- validate prepared data
- escape output
- render markup

Templates never query WordPress.

---

## Components

Components render repeated interface patterns.

Components eliminate duplicated presentation while remaining generic.

---

# Consequences

Positive:

- predictable architecture
- reusable services
- reusable components
- simple testing
- easier maintenance
- improved readability

Trade-offs:

- additional files
- more initial structure

The framework accepts these trade-offs because long-term maintainability outweighs the small increase in project size.

---

# Alternatives Considered

## Fat Panels

Rejected.

Panels became difficult to maintain and duplicated business logic.

---

## Fat Templates

Rejected.

Presentation should not contain WordPress queries or calculations.

---

## Service Locator

Rejected.

Explicit dependency injection produces clearer relationships between classes.

---

# Guiding Principle

Business logic belongs in services.

Presentation belongs in templates.

Panels coordinate the two.

## Implementation

This decision is implemented through:

- Dashboard Services
- Dashboard Panels
- Dashboard Registry
- Dashboard Templates
- Shared Components

---

## Maintaining ADRs

Architecture Decision Records are intended to document significant architectural decisions that affect the long-term structure of GOUG Framework.

ADRs are permanent historical records. Existing records should not be rewritten to reflect new decisions. Instead, new ADRs should supersede earlier decisions while preserving the architectural history of the project.