# GOUG Framework Core Specification

> **Version:** 0.1.0
> **Status:** Draft
> **Last Updated:** 2026-07-21

---

# Purpose

This document defines the responsibilities, boundaries, and design principles of the GOUG Framework Core.

Core exists to provide shared infrastructure for modules.

It is intentionally small, stable, and feature-agnostic.

This specification defines:

- What Core is
- What Core owns
- What Core must never own
- How modules interact with Core
- The responsibilities of shared infrastructure
- The long-term stability expectations of Core

---

# Core Philosophy

Core exists so modules do not have to solve the same infrastructure problems repeatedly.

Core provides architecture.

Modules provide functionality.

Core should become more stable—not larger—as the framework matures.

---

# What Is Core?

Core is the architectural foundation of the GOUG Framework.

It coordinates the framework but does not deliver user-facing features.

Core provides:

- Shared infrastructure
- Shared contracts
- Shared services
- Module lifecycle coordination
- Extension points
- Framework conventions

Core should remain useful even when no modules are installed.

---

# Core Responsibilities

Core owns the infrastructure shared across the entire framework.

Examples include:

- Framework bootstrap
- Configuration
- Shared services
- Module registration
- Module lifecycle
- Contracts
- View infrastructure
- Settings infrastructure
- Asset infrastructure
- Shared caching
- Shared logging (future)
- Shared notifications (future)

If multiple unrelated modules require the same infrastructure, Core owns it.

---

# Core Non-Responsibilities

Core does not own user-facing functionality.

Examples include:

- Dashboard panels
- Reports
- Widgets
- Activity logging
- Role management
- Redirect management
- Maintenance mode
- Analytics
- Feature-specific business logic

If removing a module would eliminate the need for a piece of functionality, that functionality belongs to the module—not Core.

---

# Core Services

Core may expose shared services for use by modules.

Examples include:

- Settings
- Views
- Assets
- Configuration
- Module management
- Logging
- Notifications
- Shared caching

These services provide infrastructure rather than feature-specific behavior.

Modules should consume shared services rather than creating duplicate infrastructure.

The method used to access Core services is considered an implementation detail and is intentionally left undefined by this specification.

---

# Extension Points

Core is designed to be extended—not modified.

Modules should extend Core through documented extension points.

Examples include:

- Public contracts
- WordPress actions
- WordPress filters
- Public services
- Module registration

Modules should never modify Core directly.

---

# Stability

Core is the most stable part of the framework.

Changes to Core have consequences for every module.

Public Core APIs should therefore be:

- Intentional
- Documented
- Backward compatible
- Versioned when necessary

Internal implementation details remain free to evolve.

---

# Design Principles

## Core owns infrastructure.

Features belong in modules.

---

## Core solves infrastructure once.

Every module benefits from the solution.

---

## Core should become smaller over time.

As the framework matures, Core should stabilize.

Future development should focus on modules rather than expanding Core.

---

## Every addition to Core has a permanent maintenance cost.

Adding functionality to Core should be considered carefully.

Core should remain intentionally minimal.

---

## Public APIs are long-term commitments.

Only expose APIs that the framework is prepared to support for future releases.

---

## Shared abstractions must be earned.

Infrastructure should move into Core only after real-world usage demonstrates a clear need.

---

## Core should remain independent of modules.

Core must never depend on the internal implementation of a module.

Modules may depend on Core.

Core must not depend on modules.

---

# The Dashboard Test

Before adding functionality to Core, ask:

> **If the Dashboard module disappeared tomorrow, would Core still need this?**

If the answer is **No**, the functionality belongs in the Dashboard module.

If the answer is **Yes**, the functionality may belong in Core.

This test helps preserve the separation between infrastructure and features.

---

# Core Checklist

Before introducing anything into Core, answer the following questions.

- Is this infrastructure?
- Will unrelated modules use it?
- Does WordPress already provide this capability?
- Is this solving a repeated problem?
- Is this API worth supporting long-term?
- Would Core still need this if the Dashboard module disappeared?
- Could this remain inside a module instead?

If these questions do not clearly justify Core ownership, the implementation should remain inside a module.

---

# Guiding Principle

> **Core provides the road. Modules choose the destination.**

Core exists to make building modules easier—not to become the destination itself.

---

# Next Steps

Subsequent documents define:

- Framework lifecycle
- Naming conventions
- Module directory structure
- Versioning
- Public APIs
- Developer guidance