# ADR-0002: Shared Infrastructure Services

- **Status:** Accepted
- **Date:** 2026-07-21
- **Decision Type:** Core Architecture

---

## Context

Multiple GOUG Framework modules may require access to the same infrastructure.

Examples include:

- Settings storage
- View rendering
- Asset registration
- Module management
- Configuration
- Logging
- Notifications
- Shared user preference data

Without a shared infrastructure layer, each module could create its own implementation of these services.

For example:

```php
new User_Settings_Service();
new View_Renderer();
new Asset_Manager();
```

This would lead to:

- Duplicate object creation
- Repeated database access
- Inconsistent configuration
- Separate caches
- Diverging implementations
- Increased memory usage
- Tighter coupling between modules and infrastructure

The Dashboard prototype demonstrated that settings, views, assets, and user preference data are infrastructure concerns that may be needed by multiple modules.

---

## Decision

The GOUG Framework Core will create and manage shared infrastructure services.

Modules will consume these services through an intentional Core API.

Conceptual usage:

```php
$core->settings();
$core->views();
$core->assets();
$core->modules();
$core->config();
```

Core services will be initialized once and shared among modules for the duration of the request.

Modules must not recreate Core infrastructure services themselves.

---

## Service Scope

Core services must provide infrastructure rather than feature-specific business logic.

Appropriate Core services may include:

- Settings
- Views
- Assets
- Configuration
- Module registration
- Logging
- Notifications
- Shared caching infrastructure

Feature-specific services remain inside the module that owns the feature.

Examples:

```text
Core
└── Settings Service

Dashboard Module
└── Dashboard Data Service

Role Manager Module
└── Capability Service
```

A service does not belong in Core merely because more than one module could theoretically use it.

The service must represent genuinely shared infrastructure.

---

## Shared Instance Behavior

Core services should generally behave as shared instances within the current request.

Conceptually:

```php
$dashboard_settings = $core->settings();
$role_settings      = $core->settings();
```

Both calls should return access to the same underlying service instance.

This allows the service to maintain shared runtime state such as:

- Loaded settings
- Cached values
- Registered definitions
- Configuration
- Validation rules

The implementation does not need to use the traditional Singleton pattern.

Core owns the service lifecycle and supplies the same instance where appropriate.

---

## Caching Responsibility

Infrastructure services may internally cache data to prevent repeated work.

For example:

```php
$core->settings()->get( 'user_preferences' );
```

The Settings Service may decide whether the value comes from:

- Request-level memory
- WordPress object cache
- User metadata
- WordPress options
- Another supported persistence layer

Modules should not need to know how the data is retrieved or cached.

This allows infrastructure optimizations to benefit every module without requiring module changes.

---

## API Design

The Core service API should be:

- Explicit
- Discoverable
- Typed
- Documented
- Stable
- Free from unnecessary string-based lookups

Preferred:

```php
$core->settings();
$core->views();
$core->assets();
```

Avoid:

```php
$core->get( 'settings' );
$core->get( 'views' );
$core->get( 'anything' );
```

A generic global service locator could become an undocumented dumping ground and hide dependencies.

Core should expose only intentional framework services.

---

## Module Dependency Rule

Modules may depend on documented Core services.

Modules must not:

- Instantiate replacement Core services
- Reach into Core internals
- Modify the service collection directly
- Register arbitrary global services without an approved extension contract
- Assume a service implementation beyond its documented public API

Module-specific dependencies should remain internal to the module.

---

## Consequences

### Positive

- Shared infrastructure is initialized once.
- Repeated database calls can be reduced.
- Caching can be centralized.
- Configuration remains consistent.
- Modules contain less framework plumbing.
- Infrastructure improvements benefit every module.
- Core APIs become easier to discover.
- Modules remain focused on business logic.

### Negative

- Core becomes responsible for service lifecycle management.
- Service APIs become long-term compatibility commitments.
- Poorly chosen Core services could increase coupling.
- Care must be taken to prevent Core from becoming a general-purpose service container.

---

## Rejected Alternatives

### Each module creates its own infrastructure

This was rejected because it encourages duplicate implementations, repeated initialization, and inconsistent behavior.

---

### Full dependency injection container

A full dependency injection container was rejected for the initial framework version.

It would introduce capabilities and complexity that have not been demonstrated as necessary.

The framework should not recreate Symfony, Laravel, or another application framework inside WordPress.

---

### Generic string-based service locator

Example:

```php
$core->get( 'service-name' );
```

This was rejected because it:

- Hides dependencies
- Reduces discoverability
- Encourages arbitrary registrations
- Weakens static analysis
- Makes supported APIs unclear

---

### Module-owned global service containers

This was rejected because module internals should remain isolated.

A module may organize its own objects internally, but it must not expose an unrestricted global container.

---

## Architectural Rule

> **If multiple modules require the same infrastructure, Core owns it.**

This applies only to infrastructure.

Shared business logic does not automatically belong in Core.

---

## Validation Questions

Before adding a service to Core, ask:

1. Is this infrastructure rather than feature logic?
2. Is it useful to unrelated modules?
3. Does sharing it eliminate repeated work?
4. Does WordPress already provide an adequate solution?
5. Are we prepared to support its API long-term?
6. Could it remain internal until genuine reuse is demonstrated?
7. Would it still make sense without the Dashboard module?

If the answers do not clearly justify Core ownership, the service should remain inside a module.

---

## Related Documents

- `docs/FRAMEWORK.md`
- `docs/ARCHITECTURE.md`
- `docs/MODULES.md`
- `docs/adr/0001-framework-philosophy.md`