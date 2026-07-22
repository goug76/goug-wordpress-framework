# GOUG Framework Module Specification

> **Version:** 0.1.0
> **Status:** Draft
> **Last Updated:** 2026-07-21

---

# Purpose

This document defines the requirements and conventions for modules within the GOUG Framework.

A module is the primary unit of functionality in the framework. Every feature delivered by the framework exists as a module.

This specification defines:

- What a module is
- What responsibilities a module owns
- How a module interacts with Core
- How modules interact with each other
- What constitutes a valid GOUG Framework module

This document intentionally describes module behavior rather than implementation details.

---

# Module Philosophy

Modules are responsible for delivering features.

Core is responsible for providing infrastructure.

Modules extend the framework but never modify it.

Every module should:

- Solve one well-defined problem.
- Be understandable in isolation.
- Own its feature completely.
- Consume shared Core infrastructure.
- Expose only intentional extension points.
- Leave no unnecessary impact on unrelated modules.

A module should coordinate responsibilities rather than accumulate them.

---

# What Is a Module?

A module is the smallest independently deployable unit of functionality within the GOUG Framework.

A module owns everything required to deliver its feature, including:

- Business logic
- Controllers
- Services
- Settings
- Assets
- Views
- Registries
- Hooks
- REST endpoints
- Tests
- Documentation

Core should know only that the module exists.

Core should not know how the module is internally organized.

---

# Framework Hierarchy

The framework is divided into two primary layers.

```text
GOUG Framework
│
├── Core
│   ├── Infrastructure
│   ├── Services
│   ├── Contracts
│   ├── Registries
│   └── Extension Points
│
└── Modules
    ├── Dashboard
    ├── Role Manager
    ├── Activity Log
    ├── Maintenance Mode
    └── Future Modules
```

Core provides the foundation.

Modules provide functionality.

---

# Module Identity

Every module has a unique identity.

A module identity must remain stable for the lifetime of the module.

At minimum, every module provides:

- Identifier
- Display name
- Description
- Version
- Author
- Framework compatibility
- WordPress compatibility
- PHP compatibility

Module metadata describes a module.

It does not execute a module.

---

# Module Responsibilities

Every module owns its feature from end to end.

Typical responsibilities include:

- Registering WordPress behavior
- Providing feature-specific services
- Managing feature settings
- Rendering feature views
- Registering assets
- Managing feature-specific data
- Providing extension hooks
- Documenting public APIs

If a responsibility exists solely because a module exists, that responsibility belongs to the module.

---

# Core Responsibilities

Modules should rely on Core for shared infrastructure.

Examples include:

- Settings infrastructure
- View rendering
- Asset infrastructure
- Shared configuration
- Module lifecycle
- Logging (future)
- Notifications (future)

Modules consume infrastructure.

Core owns infrastructure.

---

# Ownership

Every responsibility must have a single owner.

Examples:

| Responsibility | Owner |
|----------------|-------|
| Dashboard panels | Dashboard Module |
| Role assignments | Role Manager Module |
| Framework settings | Core |
| View rendering | Core |
| Asset infrastructure | Core |

If ownership is unclear, the architecture should be reconsidered before implementation.

---

# Module Contract

Every module must implement the framework's module contract.

The contract defines only the behaviors required by Core.

Conceptually, every module must be able to:

- Describe itself.
- Register itself.
- Boot itself.

The contract should remain intentionally small.

Additional behavior belongs inside the module rather than the contract.

---

# Public vs Internal APIs

A module should expose only the functionality intended for use by other modules or external developers.

Examples of public APIs include:

- Actions
- Filters
- Public services
- REST endpoints
- Documented extension points

Everything else should be considered internal implementation.

Internal implementation may change without affecting the framework or other modules.

---

# Design Principles

Modules follow these principles.

### A module owns one feature.

Avoid combining unrelated functionality into a single module.

---

### A module coordinates responsibilities.

The module class should coordinate specialized classes rather than become a large implementation class.

---

### Every responsibility has one owner.

If multiple classes appear to own the same behavior, responsibilities should be reconsidered.

---

### Shared infrastructure belongs in Core.

Modules should consume infrastructure rather than recreate it.

---

### Public APIs are promises.

Only expose APIs that the framework is prepared to support long-term.

---

### Internal implementation remains private.

Modules should be free to reorganize their internal implementation without affecting other modules.

---

### A module should be understandable in isolation.

A developer should be able to understand a module without first studying unrelated modules.

---

### Modules extend Core.

Modules never modify Core.

---

# Guiding Principle

> **A module coordinates responsibilities; it does not accumulate them.**

The goal of the module architecture is to produce small, focused, maintainable feature packages that can evolve independently while sharing a common architectural foundation.

---

# Next Steps

Subsequent sections of this specification will define:

- Module lifecycle
- Metadata specification
- Settings
- Assets
- Dependencies
- Versioning
- Directory structure
- Installation context
- Module checklist
- Example implementation