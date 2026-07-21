# ADR-008 – Controller Pattern

**Status:** Accepted

## Context

Interactive dashboard features introduced form submissions, nonce validation, capability checks, and redirects.

Embedding request handling directly inside dashboard coordinators would violate the Single Responsibility Principle and reduce maintainability.

## Decision

Dedicated controller classes are responsible for processing all dashboard requests.

Controllers perform:

- Request validation
- Capability checks
- Nonce verification
- Input sanitization
- Delegation to services
- Redirects

Controllers never contain business logic.

Business logic remains within framework services.

## Consequences

### Positive

- Consistent request lifecycle.
- Improved separation of concerns.
- Easier testing.
- Reusable request-handling pattern.

### Negative

- Additional classes to maintain.

---

## Maintaining ADRs

Architecture Decision Records are intended to document significant architectural decisions that affect the long-term structure of GOUG Framework.

ADRs are permanent historical records. Existing records should not be rewritten to reflect new decisions. Instead, new ADRs should supersede earlier decisions while preserving the architectural history of the project.