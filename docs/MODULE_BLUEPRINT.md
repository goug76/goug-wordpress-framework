# Module Blueprint

## Purpose

A module is the smallest independently deployable feature package within the GOUG Framework.

Modules extend the framework without requiring modifications to Core. Every module owns its implementation, lifecycle, assets, documentation, and tests.

A valid module should be portable. If copied to another compatible GOUG Framework installation, it should function without requiring changes to Core.

---

# Philosophy

Modules follow the same architectural principles as the Core framework.

- One responsibility per class.
- Coordination is not ownership.
- Abstractions are earned.
- Composition over inheritance.
- Explicit over implicit.
- Portable by design.

Core provides infrastructure.

Modules provide features.

---

# Lifecycle

Every module participates in the framework lifecycle.

```
Framework
        │
        ▼
Discover
        │
        ▼
Register
        │
        ▼
Boot
```

Modules should never perform work during construction.

The constructor exists only to initialize the module itself.

---

# Responsibilities

## Module

Owns:

- Metadata
- Provider coordination

Never owns:

- Business logic
- Rendering
- Controllers
- Persistence

---

## Providers

Own:

- Infrastructure registration
- Hook registration
- Service registration

Never own:

- Business logic

---

## Controllers

Coordinate requests.

Controllers should delegate work to services.

---

## Services

Own business logic.

Whenever a question begins with:

> "How should this feature work?"

The answer probably belongs inside a Service.

---

## Registries

Own collections.

Examples:

- PanelRegistry
- WidgetRegistry
- ToolRegistry

Registries never create or discover objects.

They only manage collections.

---

## Repositories

Own persistence.

Examples:

- Reading options
- User preferences
- Module settings

Repositories never contain business logic.

---

## Models

Represent state.

Models are simple objects.

They should not communicate with WordPress directly.

---

## Views

Own presentation.

Views contain as little PHP as possible.

---

## Assets

Own public assets.

Examples:

- JavaScript
- SCSS
- Images

---

## Resources

Own non-public resources.

Examples:

- JSON
- SVG
- Translation files
- Schemas

---

## Tests

Own module tests.

Modules never place tests into Core.

---

## Docs

Own module documentation.

Every module should be independently understandable.

---

# Standard Structure

```text
Module/
│
├── Module.php
├── module.json
│
├── Providers/
├── Controllers/
├── Services/
├── Registries/
├── Repositories/
├── Models/
├── Views/
│
├── Assets/
│   ├── Js/
│   ├── Scss/
│   └── Images/
│
├── Resources/
├── Support/
├── Tests/
└── Docs/
```

---

# Required Files

## Module.php

Entry point.

Coordinates providers.

Nothing else.

---

## module.json

Describes the package.

Example:

```json
{
    "id": "dashboard",
    "name": "Dashboard",
    "version": "1.0.0",
    "requiresFramework": "^1.0"
}
```

---

# Communication Rules

Modules may depend on Core.

Core must never depend on Modules.

Modules communicate with Core only through published contracts.

Modules must never reach into another module's internals.

---

# Portability

A module should be installable by copying its folder into the Modules directory.

No Core modifications should ever be required.

---

# Future

Eventually modules may be:

- Installed
- Updated
- Enabled
- Disabled
- Removed

through the GOUG Package Manager.

For this reason every module should be treated as an independent package from the beginning.