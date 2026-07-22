# Repository Structure

## Purpose

This document defines the physical organization of the GOUG Framework repository.

The repository structure exists to reflect the architecture of the framework. Every directory has a clear responsibility and every file belongs to a logical owner.

This document serves as the blueprint for organizing the framework and all future packages.

---

# Repository Philosophy

The repository is organized around **ownership**, not file type.

Code should live with the component that owns its responsibility.

This makes packages portable, improves discoverability, and keeps the framework modular as it grows.

---

# Guiding Principles

- Ownership determines location.
- Every directory has a single responsibility.
- Modules are self-contained.
- Core contains only framework infrastructure.
- Packages own everything they require.
- Shared abstractions must be earned.

---

# Repository Layout

```text
goug-framework/
│
├── docs/
├── src/
├── tests/
│
├── composer.json
├── package.json
├── vite.config.js
├── phpunit.xml
├── README.md
├── CHANGELOG.md
├── LICENSE
└── .editorconfig
```

---

# Root Directories

## docs/

Contains all architectural documentation.

Examples:

- ARCHITECTURE.md
- CORE.md
- MODULES.md
- LIFECYCLE.md
- RESPONSIBILITIES.md
- NAMING.md
- ROADMAP.md
- REPOSITORY.md

Documentation is the authoritative source for architectural decisions.

---

## src/

Contains all framework source code.

There are only two top-level code directories.

```text
src/

Core/
Modules/
```

Every class belongs to one of these locations.

---

## Core/

Core contains framework infrastructure.

It provides services used by every package but contains no feature-specific functionality.

```text
Core/

Bootstrap/
Configuration/
Contracts/
Exceptions/
Lifecycle/
Registries/
Services/
```

Core should remain stable and intentionally small.

---

## Modules/

Modules extend the framework by providing functionality.

Each module is completely self-contained.

```text
Modules/

Dashboard/
RoleManager/
ReadingProgress/
MaintenanceMode/
```

Modules may be installed, updated, or removed independently of one another.

---

# Module Structure

Every module follows the same structure.

```text
Dashboard/

Assets/
Configuration/
Controllers/
Docs/
Providers/
Registries/
Resources/
Services/
Tests/
Views/

Module.php
README.md
CHANGELOG.md
```

Modules may omit unused directories, but the overall structure remains consistent across the framework.

---

## Assets/

Contains frontend assets owned by the module.

```text
Assets/

css/
js/
icons/
images/
```

Modules own their assets.

Core determines when and how assets are loaded.

---

## Configuration/

Contains configuration specific to the module.

Framework configuration never belongs here.

---

## Controllers/

Coordinates requests and user interaction.

Controllers coordinate responsibilities but should not contain business logic.

---

## Docs/

Contains documentation specific to the module.

Examples include:

- README
- CHANGELOG
- Upgrade Guides

Documentation travels with the package.

---

## Providers/

Supplies data or functionality to the module.

Providers perform work.

---

## Registries/

Own collections used by the module.

Registries should only manage module-specific objects.

---

## Resources/

Contains package resources.

Examples:

- translations
- schemas
- stubs

Resources remain inside the package that owns them.

---

## Services/

Contains module-specific services.

Framework services belong in Core.

---

## Tests/

Contains automated tests for the package.

Each package owns its own tests.

Suggested organization:

```text
Tests/

Unit/
Integration/
```

---

## Views/

Contains presentation templates owned by the package.

Views should contain presentation only.

---

## Module.php

Every package contains a single entry point.

Core discovers this file during module discovery.

Module.php is responsible for registering the package with the framework.

---

# Package Philosophy

A package is portable.

Everything required to build, test, maintain, and distribute a package should exist inside the package directory.

If a package is compressed into a ZIP archive, it should remain complete.

---

# Discovery

Core discovers packages by scanning the Modules directory.

Each package provides a Module.php entry point.

Core is unaware of package implementation details.

---

# Tooling

GOUG Framework uses modern tooling to separate frontend and backend responsibilities.

## Composer

Composer manages PHP autoloading and framework dependencies.

The framework uses PSR-4 autoloading.

Composer is not used to replace Vite.

---

## Vite

Vite manages frontend assets.

Responsibilities include:

- SCSS compilation
- JavaScript bundling
- Asset optimization
- Development server
- Source maps

Vite is responsible only for frontend assets.

---

## PHPUnit

PHPUnit provides automated testing.

Every package owns its own tests.

Framework infrastructure should be tested independently of package functionality.

---

## EditorConfig

EditorConfig maintains consistent formatting across contributors and development environments.

---

# Why There Is No Global Assets Directory

Assets belong to the package that owns them.

Loading assets is the responsibility of Core.

Ownership and delivery are intentionally separated.

---

# Why There Is No Support Directory

The framework avoids generic directories.

Every class should have an obvious owner.

If a shared abstraction becomes necessary, it must be earned through repeated use rather than anticipated.

---

# Design Principles

- Organize by responsibility.
- Organize by ownership.
- Keep packages portable.
- Keep Core independent.
- Prefer clarity over convention.

---

# Guiding Principle

> The repository should communicate the architecture of the framework before a single line of code is read.