# GOUG Framework Architecture

> **Version:** Draft 1.0  
> **Status:** Living Design Specification

---

# Purpose

This document defines the technical architecture of the GOUG Framework.

It establishes:

- System boundaries
- Dependency direction
- Core responsibilities
- Module responsibilities
- Module registration
- Framework lifecycle
- Public and internal APIs
- Architectural constraints

The architecture must remain consistent with the principles defined in `FRAMEWORK.md`.

---

# Architectural Goal

The GOUG Framework provides a lightweight architectural foundation for building focused WordPress modules.

The framework does not replace WordPress.

It organizes and extends WordPress through:

- Modern object-oriented PHP
- Clear separation of concerns
- Shared infrastructure
- Stable public APIs
- WordPress actions and filters
- Reusable development conventions

Features belong in modules.

Architecture belongs in Core.

---

# System Boundaries

The framework is divided into three primary layers:

```text
WordPress
    ↑
Framework Core
    ↑
Modules
```

## WordPress

WordPress remains the application platform.

WordPress owns:

- Plugin loading
- Theme loading
- Hooks
- Roles and capabilities
- User management
- Options and metadata
- Database access
- REST API
- Cron
- Admin pages
- Media
- Internationalization
- Security primitives

The framework must use these systems rather than duplicate them.

---

## Framework Core

Core provides reusable architectural infrastructure.

Core may depend on WordPress.

Core must not depend on any module.

Core must remain functional even if no modules are enabled.

---

## Modules

Modules provide user-facing functionality.

Modules may depend on:

- WordPress
- Framework Core
- Their own internal classes

Modules must not depend directly on the internal implementation of another module.

When modules need to interact, they should use:

- Public interfaces
- Public services
- WordPress actions
- WordPress filters
- Registered contracts

---

# Dependency Direction

Dependencies must flow in one direction:

```text
Modules
   ↓
Framework Core
   ↓
WordPress
```

The following dependency directions are prohibited:

```text
Framework Core
   ↓
Dashboard Module
```

```text
Module A
   ↓
Internal class from Module B
```

Core must never import, instantiate, or reference a module-specific class.

A module must never reach into another module's internal directory structure.

---

# Core Responsibilities

Core owns architecture and shared infrastructure.

Core may provide:

- Framework bootstrap
- Module registration
- Module lifecycle coordination
- Contracts and interfaces
- Base classes when justified
- Shared registries
- Settings infrastructure
- View rendering
- Asset management
- Shared UI components
- Shared services
- Common utilities
- Hook conventions
- Error handling conventions

Core must not provide user-facing features.

---

# Core Non-Responsibilities

Core must not contain:

- Dashboard panels
- Role management features
- Activity logging
- Maintenance mode
- Redirect management
- Media organization
- Module-specific settings
- Module-specific controllers
- Module-specific views
- Module-specific assets
- Module-specific business rules

If a class exists because a specific feature needs it, that class belongs in the module that owns the feature.

---

# Module Responsibilities

A module owns the complete implementation of its feature.

A module may contain:

- Module bootstrap class
- Registries
- Controllers
- Services
- Providers
- Settings definitions
- Admin pages
- Views
- Assets
- Hooks
- REST endpoints
- AJAX handlers
- Business logic
- Capability checks
- Data access specific to the module

A module should be independently understandable.

Removing a module should not break Core or unrelated modules.

---

# Module Definition

A module is a focused feature package built on top of the GOUG Framework.

Examples include:

- Dashboard
- Role Manager Lite
- Activity Log
- Maintenance Mode
- Redirect Manager

A module should solve one clear problem.

A module should not become a collection of unrelated features.

---

# Module Lifecycle

Modules follow two primary lifecycle phases:

```text
Register
    ↓
Boot
```

## Register Phase

During registration, a module declares what it provides.

Registration may include:

- Services
- Registries
- Settings definitions
- Controllers
- Capabilities
- Hooks that must exist before WordPress initialization continues

Registration should avoid executing feature behavior.

The register phase defines structure.

---

## Boot Phase

During boot, a module connects its behavior to WordPress.

Booting may include:

- Registering WordPress hooks
- Adding admin menus
- Registering assets
- Registering REST routes
- Initializing controllers
- Starting feature-specific services

The boot phase activates behavior.

---

# Module Contract

Modules should implement a shared Core contract.

Initial proposed contract:

```php
interface Module_Interface {

    /**
     * Register module services and definitions.
     */
    public function register(): void;

    /**
     * Attach module behavior to WordPress.
     */
    public function boot(): void;
}
```

This contract may evolve before Core v1 is finalized.

Once released as a public API, changes must preserve backward compatibility.

---

# Module Registration Strategy

The framework supports two registration paths:

1. Explicit registration for bundled modules
2. WordPress-native registration for external modules

---

## Bundled Module Registration

Modules shipped with the framework should be registered explicitly.

Example:

```php
$module_registry->register(
    new Dashboard_Module()
);
```

Explicit registration provides:

- Predictable load order
- Easier debugging
- Clear ownership
- Fewer hidden dependencies
- Reliable initialization

Bundled modules should not depend on hook timing to become available.

---

## External Module Registration

External modules should register through a public WordPress action.

Example Core hook:

```php
do_action(
    'goug_framework_register_modules',
    $module_registry
);
```

External registration example:

```php
add_action(
    'goug_framework_register_modules',
    static function ( $module_registry ): void {
        $module_registry->register(
            new Example_Module()
        );
    }
);
```

This allows external extensions to remain WordPress-native without requiring changes to Core.

---

# Module Registry

The Module Registry owns the collection of registered modules.

It should be responsible for:

- Registering modules
- Preventing duplicate registrations
- Returning registered modules
- Coordinating register calls
- Coordinating boot calls
- Validating the module contract

It should not contain module-specific logic.

Initial conceptual API:

```php
$module_registry->register( $module );

$module_registry->has( 'dashboard' );

$module_registry->get( 'dashboard' );

$module_registry->all();

$module_registry->register_modules();

$module_registry->boot_modules();
```

Exact method names remain subject to review before implementation.

---

# Module Identity

Every module must have a stable identifier.

Examples:

```text
dashboard
role-manager
activity-log
maintenance-mode
```

Module identifiers must:

- Use lowercase characters
- Use hyphens between words
- Remain stable after release
- Be unique within the framework
- Be suitable for settings keys, hooks, and capability prefixes

Display names may change.

Identifiers must not.

---

# Framework Bootstrap

The framework bootstrap is the entry point into Core.

Its responsibilities should remain minimal.

The bootstrap should:

1. Confirm WordPress is loaded
2. Load the autoloader
3. Initialize Core
4. Create the Module Registry
5. Register bundled modules
6. Expose external module registration
7. Run module registration
8. Run module booting

Conceptual flow:

```text
WordPress loads framework
        ↓
Autoloader initialized
        ↓
Core services created
        ↓
Module Registry created
        ↓
Bundled modules registered
        ↓
External registration action fired
        ↓
Modules register
        ↓
Modules boot
        ↓
Framework ready
```

---

# Framework Lifecycle

The initial lifecycle is expected to follow this sequence:

```text
Plugin or theme loaded
        ↓
Framework bootstrap created
        ↓
Core initialized
        ↓
Bundled modules added to registry
        ↓
goug_framework_register_modules
        ↓
Module register phase
        ↓
goug_framework_modules_registered
        ↓
Module boot phase
        ↓
goug_framework_modules_booted
        ↓
goug_framework_ready
```

The exact WordPress hook used to start this sequence must be decided during lifecycle design.

Likely candidates include:

- `plugins_loaded`
- `after_setup_theme`
- `init`

The framework may need to support both plugin and theme installation contexts.

That decision must be documented before implementation.

---

# Hook Architecture

The framework should use WordPress actions and filters as its primary public event system.

It should not introduce a competing event dispatcher unless WordPress hooks prove insufficient.

Public hook examples:

```php
do_action( 'goug_framework_register_modules', $module_registry );

do_action( 'goug_framework_modules_registered', $module_registry );

do_action( 'goug_framework_modules_booted', $module_registry );

do_action( 'goug_framework_ready' );
```

Modules may expose their own hooks using a consistent naming convention.

Example:

```php
apply_filters(
    'goug_dashboard_quick_actions',
    $actions
);
```

Hook naming standards will be defined in `NAMING.md`.

---

# Public APIs

A public API is any framework surface intended for use outside the class or module that owns it.

Public APIs may include:

- Interfaces
- Public service methods
- Public registry methods
- Actions
- Filters
- Settings keys
- Capability names
- REST routes
- JavaScript events
- CSS utility classes
- Template extension points

Public APIs must be:

- Intentional
- Documented
- Stable
- Versioned when necessary
- Backward compatible after release

A method being declared `public` in PHP does not automatically make it part of the supported public API.

Supported APIs must be explicitly documented.

---

# Internal APIs

Internal APIs are implementation details.

They may include:

- Private services
- Module-specific providers
- Internal helper methods
- Concrete class implementations
- Internal template data structures
- Internal JavaScript modules
- Internal CSS selectors

Internal APIs may change without backward compatibility guarantees.

Internal classes should be clearly separated through:

- Namespace
- Directory structure
- Documentation
- Visibility
- Naming where appropriate

External code should never depend on internal APIs.

---

# API Stability

Before Core v1, architecture may change while the design is being validated.

After Core v1:

- Public interfaces must remain stable
- Hook names must remain stable
- Public method signatures must remain compatible
- Module identifiers must remain stable
- Settings keys must remain stable
- Capability names must remain stable

Breaking changes require:

- A major version change
- Migration guidance
- Deprecation where possible
- Clear documentation

---

# Service Architecture

Services encapsulate reusable operations or access to data.

A service should:

- Have one clear responsibility
- Avoid rendering markup
- Avoid owning WordPress page flow
- Be testable independently where practical
- Return structured data

Examples:

```text
User Settings Service
Asset Service
View Service
Module Registry
```

Module-specific services remain inside their modules.

Core services must be broadly reusable.

---

# Registry Architecture

A registry stores and organizes definitions or objects.

Examples include:

- Module Registry
- Dashboard Panel Registry
- Settings Registry
- Widget Registry

Core may define a shared registry contract if repeated behavior justifies it.

Core must not create an abstract registry merely because multiple registries are expected.

The abstraction should be extracted only after real implementations demonstrate stable common behavior.

---

# Controller Architecture

Controllers coordinate requests and application flow.

A controller may:

- Validate requests
- Check capabilities
- Call services
- Select views
- Return WordPress responses

Controllers should not:

- Contain large amounts of business logic
- Perform direct presentation formatting
- Become general-purpose service containers
- Store unrelated feature behavior

A generic Core controller base class should only be introduced if multiple modules demonstrate a genuine shared requirement.

---

# View Architecture

Views are responsible for presentation.

Views should:

- Receive prepared data
- Render markup
- Escape output
- Avoid querying data directly
- Avoid performing business logic
- Avoid mutating application state

Core may provide a shared View Renderer.

Modules own their own templates.

Shared UI components may live in Core when they are truly module-independent.

---

# Asset Architecture

Assets should be registered centrally and loaded only when needed.

Core may provide shared asset infrastructure.

Modules own their own:

- CSS
- JavaScript
- Images
- Icons
- Build entry points

Assets must not load globally unless required globally.

A module should determine whether its assets are needed on the current request.

Shared framework assets should remain minimal.

---

# Settings Architecture

Core may provide reusable settings infrastructure.

Core may own:

- Registration conventions
- Validation flow
- Sanitization contracts
- Persistence adapters
- Settings rendering infrastructure

Modules own:

- Setting definitions
- Defaults
- Business meaning
- Capability requirements
- Module-specific validation
- Module-specific settings screens

Core must not contain dashboard-specific or feature-specific settings.

---

# Error Handling

The framework should fail safely.

Core errors must not unnecessarily take down WordPress.

Where possible:

- Invalid registrations should be rejected clearly
- Duplicate registrations should be handled predictably
- Missing optional services should fail quietly
- Developer errors should be visible when debugging is enabled
- User-facing errors should be understandable
- Sensitive implementation details should not be exposed publicly

Error-handling conventions will be refined during implementation planning.

---

# Installation Context

The framework may eventually be distributed through:

- A standalone plugin
- A parent plugin
- A theme
- A child theme
- A Composer package

Core architecture must not assume a permanent installation location.

Paths and URLs should be provided through bootstrap configuration rather than inferred throughout the codebase.

Modules should not hard-code framework filesystem paths.

---

# Namespaces

Core and modules must use namespaces.

Conceptual namespace structure:

```php
Goug\Framework\Core
Goug\Framework\Contracts
Goug\Framework\Support
Goug\Framework\Modules\Dashboard
Goug\Framework\Modules\RoleManager
```

The final namespace and directory convention will be defined in `NAMING.md`.

---

# Architectural Constraints

The following rules are mandatory.

## Core must not depend on modules.

No exceptions.

---

## Modules must not depend on another module's internals.

Cross-module communication must use documented public APIs.

---

## WordPress functionality must not be duplicated without justification.

Use WordPress first.

---

## Shared abstractions must be earned.

Do not introduce abstract classes or interfaces based only on anticipated reuse.

---

## Public APIs must be intentional.

Do not accidentally expose implementation details as permanent contracts.

---

## Feature code must remain outside Core.

If it solves a specific user problem, it belongs in a module.

---

## The Dashboard is not special.

The Dashboard is the first module, not the architectural center of the framework.

Core must remain equally suitable for modules unrelated to dashboards.

---

# Architectural Validation Questions

Before adding a class to Core, ask:

1. Would this class still make sense if the Dashboard module did not exist?
2. Could a completely unrelated module use it?
3. Does WordPress already provide this functionality?
4. Is the abstraction proven by real repetition?
5. Are we willing to support its public API long-term?
6. Does adding it make Core smaller and clearer?
7. Could this remain internal to a module instead?

If these questions do not produce a clear Core justification, the class should remain inside a module.

---

# Initial Architectural Decisions

The following decisions are accepted for the first implementation.

## Decision 1: WordPress remains the platform

The framework extends WordPress and does not replace its fundamental systems.

---

## Decision 2: Core contains architecture only

User-facing features belong in modules.

---

## Decision 3: The Dashboard becomes a module

Dashboard-specific registries, services, controllers, views, assets, and settings will be migrated out of Core.

---

## Decision 4: Modules use register and boot phases

Registration defines structure.

Booting activates behavior.

---

## Decision 5: Bundled modules use explicit registration

Bundled modules will be registered directly for predictable loading.

---

## Decision 6: External modules use a WordPress registration action

External modules may register through:

```php
goug_framework_register_modules
```

---

## Decision 7: WordPress hooks remain the public event system

The framework will not introduce a competing event dispatcher unless a demonstrated need emerges.

---

## Decision 8: Public APIs must be explicitly documented

Not every public PHP method is automatically a supported framework API.

---

# Open Questions

The following items require further design:

- Is the framework primarily a plugin, a package, or installation-context neutral?
- Which WordPress hook starts the framework lifecycle?
- How are Core dependencies created and shared?
- Is a service container necessary?
- How are bundled modules discovered?
- How are modules enabled or disabled?
- Does disabling a module preserve its settings?
- How are module dependencies declared?
- How are module versions represented?
- How are compatibility requirements validated?
- Which existing prototype classes qualify for Core?
- Which shared UI components truly belong in Core?
- Does Core need a base registry contract?
- How should framework failures be surfaced in wp-admin?

These questions must be answered before Core v1 is considered stable.

---

# Next Design Document

The next document should be:

```text
docs/MODULES.md
```

It will define:

- Required module structure
- Module metadata
- Module class responsibilities
- Register and boot behavior
- Module settings
- Module assets
- Module hooks
- Internal versus public module APIs
- Module enablement and dependencies

---

# Guiding Rule

> **Core provides the road. Modules choose where to go.**

Core should remain stable, lightweight, and unaware of the features built upon it.