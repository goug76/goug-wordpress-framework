# ADR-004: Dashboard Registry

**Status:** Accepted

**Date:** July 2026

---

# Context

As dashboard panels increased, layout information became scattered throughout the framework.

Without a central registry, ordering, sizing, visibility, and capabilities would become difficult to manage consistently.

---

# Decision

All dashboard panels register through a central Dashboard Registry.

The registry owns:

- panel normalization
- capability filtering
- visibility
- layout metadata
- row ordering
- width validation
- rendering order

Panels provide metadata.

The registry coordinates the dashboard.

---

# Consequences

Benefits:

- consistent APIs
- deterministic rendering
- centralized validation
- easier future customization

---

# Guiding Principle

Panels describe themselves.

The registry organizes them.