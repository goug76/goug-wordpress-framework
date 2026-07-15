# GOUG Framework Architecture Decisions

> **Version:** 0.5.0-alpha  
> **Last Updated:** July 2026

---

# Overview

GOUG Framework uses Architecture Decision Records (ADRs) to document significant architectural choices made during the evolution of the framework.

Unlike commit messages or release notes, ADRs explain **why** important decisions were made, the alternatives that were considered, and the long-term consequences of those choices.

These records provide institutional knowledge for future contributors and help ensure the framework evolves consistently over time.

---

# Decision Index

| ADR | Title | Status |
|------|-------|--------|
| ADR-001 | Dashboard Architecture | Accepted |
| ADR-002 | Semantic Dashboard Layout | Accepted |
| ADR-003 | Three Uses Before Abstraction | Accepted |
| ADR-004 | Dashboard Registry | Accepted |
| ADR-005 | Modern Asset Pipeline | Accepted |

---

# When to Create an ADR

Create an ADR when a decision:

- changes the framework architecture
- introduces a new development pattern
- establishes a long-term convention
- affects future extensibility
- replaces a previously accepted design

Do **not** create ADRs for:

- bug fixes
- cosmetic changes
- refactoring without architectural impact
- feature additions that follow existing patterns

---

# Philosophy

Architecture decisions should be rare.

If a decision is important enough that future contributors may ask *"Why was it done this way?"*, it probably deserves an ADR.