# GOUG Framework Responsibility Specification

> **Version:** 0.1.0
> **Status:** Draft
> **Last Updated:** 2026-07-21

---

# Purpose

This document defines the responsibilities and ownership boundaries within the GOUG Framework.

A clear architecture requires clear ownership.

Every responsibility within the framework should have a single, obvious owner.

When ownership is unclear, the architecture should be reconsidered before implementation.

---

# Responsibility Philosophy

Every responsibility deserves an owner.

An owner is responsible for:

- Performing the work.
- Maintaining the implementation.
- Protecting its invariants.
- Exposing only intentional public behavior.

Ownership should never be shared.

Coordination between components is encouraged.

Ownership is not.

---

# Ownership Hierarchy

Responsibilities flow through the framework in predictable layers.

```text
WordPress
    │
    ▼
Core
    │
    ▼
Module
    │
    ▼
Component
```

Each layer owns only the responsibilities appropriate to that layer.

---

# Core Responsibilities

Core owns framework infrastructure.

Examples include:

- Bootstrap
- Configuration
- Shared Services
- Contracts
- Module Lifecycle
- Module Registry
- View Infrastructure
- Asset Infrastructure
- Settings Infrastructure

Core should never own feature-specific behavior.

---

# Module Responsibilities

Modules own features.

A module coordinates everything required to deliver a single capability.

Examples include:

- Dashboard
- Role Management
- Activity Log
- Maintenance Mode

Modules consume Core infrastructure.

They should not recreate it.

---

# Service Responsibilities

Services own reusable business capabilities.

A service should perform work.

Examples include:

- Rendering
- Configuration
- Settings access
- Caching
- Notifications

Services should not:

- Render HTML
- Register WordPress hooks
- Manage application flow

Services answer questions.

They do not coordinate the application.

---

# Registry Responsibilities

Registries own collections.

A registry knows:

- What exists
- How it is identified
- How it is retrieved

Examples include:

- ModuleRegistry
- AssetRegistry
- DashboardRegistry

Registries should not execute business logic.

---

# Provider Responsibilities

Providers supply information.

Examples include:

- Configuration
- Metadata
- Environment information

Providers answer:

> "What is available?"

They should not coordinate application behavior.

---

# Controller Responsibilities

Controllers coordinate behavior.

Controllers:

- Receive requests
- Validate input
- Coordinate services
- Produce responses

Controllers should contain as little business logic as possible.

They orchestrate.

They do not implement.

---

# View Responsibilities

Views own presentation.

Views should:

- Render output
- Display data
- Contain minimal logic

Views should never:

- Query services
- Perform business logic
- Modify application state

Views answer one question:

> "How should this be presented?"

---

# Configuration Responsibilities

Configuration owns application settings.

Configuration should answer:

- What is configured?
- What are the defaults?
- What values are available?

Configuration should never contain business logic.

---

# Asset Responsibilities

Asset classes own framework assets.

Responsibilities include:

- Registering assets
- Enqueuing assets
- Versioning assets
- Dependency management

Asset classes should not render views.

---

# Hook Responsibilities

Hook classes own WordPress integration.

Responsibilities include:

- Registering actions
- Registering filters
- Connecting framework behavior to WordPress

Business logic should remain elsewhere.

---

# Validation Responsibilities

Validation owns correctness.

Validation should determine whether data is acceptable.

Validation should never:

- Persist data
- Render output
- Coordinate application behavior

Validation answers one question:

> "Is this valid?"

---

# State Responsibilities

State owns data.

State should know:

- What data exists
- How it is retrieved
- How it is persisted

State should not know how the data is presented.

---

# Coordination vs Ownership

Coordination is not ownership.

Example:

A controller may coordinate:

- Validation
- Services
- Rendering

It does not own any of them.

Likewise, a module coordinates:

- Services
- Assets
- Views
- Settings

It does not replace them.

---

# Responsibility Checklist

Before adding code to a class, ask:

- Does this responsibility already have an owner?
- Am I introducing a second owner?
- Does this belong at a lower level?
- Does this belong at a higher level?
- Am I coordinating or implementing?
- Am I violating a documented boundary?

If ownership is unclear, stop and reconsider the design.

---

# Design Principles

## One responsibility.

Every component should have a clearly defined purpose.

---

## One owner.

Responsibilities should never be duplicated.

---

## Coordination is not ownership.

Coordinators assemble behavior.

Owners implement behavior.

---

## Infrastructure belongs in Core.

Features belong in Modules.

---

## Presentation belongs in Views.

Behavior belongs elsewhere.

---

## Business logic belongs in Services.

Services should remain independent of presentation whenever practical.

---

## Framework boundaries should remain visible.

Developers should always know where a responsibility belongs.

---

# Guiding Principle

> **When ownership is obvious, implementation becomes simple.**

Architecture is not about where code happens to live.

Architecture is about who owns the responsibility.

---

# Next Steps

Subsequent documents define:

- Framework roadmap
- Developer workflow
- Public APIs
- Versioning
- Contribution guidelines