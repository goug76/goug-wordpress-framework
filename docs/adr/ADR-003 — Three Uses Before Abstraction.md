# ADR-003: Three Uses Before Abstraction

**Status:** Accepted

**Date:** July 2026

---

# Context

Many frameworks become increasingly difficult to maintain because abstractions are introduced before patterns have been proven.

Premature abstraction often results in unnecessary complexity and reduced readability.

---

# Decision

GOUG Framework follows the principle:

> **Three uses before abstraction.**

Reusable helpers, components, or services should normally be introduced only after the same pattern has naturally appeared in multiple places.

---

# Consequences

Benefits:

- simpler architecture
- fewer unnecessary abstractions
- improved readability
- easier maintenance

Trade-offs:

- occasional temporary duplication
- slightly slower initial development

The framework accepts these trade-offs because proven abstractions consistently produce better long-term designs.

---

# Examples

Examples within GOUG Framework include:

- Dashboard Cards
- Status Cards
- Quick Actions Service
- Development Service

Each abstraction emerged after repeated implementation rather than speculation.

---

# Guiding Principle

Duplicate first.

Abstract later.