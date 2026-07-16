# ADR-007: Framework Registry Pattern

- **Status:** Accepted
- **Date:** 2026-07-16
- **Decision Owners:** GOUG Framework maintainers
- **Related Decisions:** ADR-001 through ADR-006
- **Scope:** Framework architecture

---

## Context

GOUG Framework is evolving beyond a traditional WordPress theme.

It provides theme-level presentation capabilities while also introducing application-style functionality such as:

- A custom administrative dashboard
- Dashboard panels
- Quick actions
- Services
- User preferences
- Capability-aware layouts
- Future settings and plugin integrations

As these systems have grown, a recurring architectural pattern has emerged:

1. Features register structured metadata.
2. A registry validates and normalizes that metadata.
3. A coordinator filters and prepares registered items.
4. A renderer displays the resulting collection.

The dashboard panel system already follows this pattern successfully.

Panels register metadata such as:

- Identifier
- Title
- Capability
- Layout row
- Width
- Priority
- Visibility
- Template
- Template data

The Dashboard Registry then normalizes, filters, sorts, and returns those panel definitions without rendering them directly.

Similar needs are appearing in other areas of the framework, including:

- Framework settings
- Quick actions
- Dashboard preferences
- Developer tools
- Plugin integrations
- Notifications
- Commands
- Future extension APIs

Without a consistent pattern, these systems could become collections of hardcoded arrays, conditional statements, and tightly coupled templates.

---

## Decision

GOUG Framework will use registries as the preferred architectural pattern for extensible collections of framework features.

The guiding principle is:

> **Register behavior. Do not hardcode it.**

A registry-based subsystem should generally contain four layers:

    Feature definitions
            ↓
        Registry
            ↓
    Coordinator or processor
            ↓
    Renderer or consumer

### Feature definitions

Individual modules register structured metadata describing their behavior.

For example:

~~~php
$registry->register_panel(
	array(
		'id'         => 'site-health',
		'title'      => __( 'Site Health', 'goug-framework' ),
		'capability' => 'view_site_health_checks',
		'row'        => 2,
		'width'      => 'third',
		'priority'   => 10,
		'body_view'  => 'dashboard/panels/site-health',
	)
);
~~~

Feature modules should describe themselves without directly controlling the entire collection.

### Registry

The registry owns collection-level metadata concerns, including:

- Registration
- Removal
- Identifier uniqueness
- Default values
- Validation
- Normalization
- Metadata lookup

A registry should not normally:

- Render HTML
- Perform unrelated business logic
- Collect expensive application data
- Read persistent user state unless that is its explicit responsibility
- Become a general-purpose service container

### Coordinator or processor

A coordinator applies runtime rules to registered definitions.

Examples include:

- Capability filtering
- Profile filtering
- User preferences
- Availability checks
- Ordering
- Context-specific overrides
- Plugin integration rules

This preserves a distinction between:

    What was registered

and:

    What should be used for this request

### Renderer or consumer

Templates, components, APIs, and other consumers receive prepared definitions and render or expose them.

Renderers should not duplicate registry validation or business rules.

---

## Registry characteristics

Framework registries should follow these conventions.

### Stable identifiers

Every registered item must have a unique, sanitized identifier.

~~~php
'id' => 'quick-actions'
~~~

Identifiers are used for:

- Lookup
- Removal
- User preferences
- Layout overrides
- Filters
- Extension APIs
- Persistent state

Changing a public identifier may require a migration.

### Normalized definitions

Registries should return predictable structures.

Optional values must receive defaults before registered items are consumed.

~~~php
$defaults = array(
	'id'         => '',
	'title'      => '',
	'priority'   => 100,
	'capability' => 'read',
	'visible'    => true,
);
~~~

### Capability-aware behavior

WordPress capabilities should be used instead of hardcoded role names.

~~~php
current_user_can( $capability );
~~~

This allows custom roles and modified capability sets to work naturally.

### Extensibility

Registries should expose focused WordPress hooks when outside code has a legitimate need to extend or alter the collection.

Examples:

~~~php
do_action(
	'goug_dashboard_register_panels',
	$registry
);
~~~

~~~php
apply_filters(
	'goug_dashboard_panels',
	$panels
);
~~~

Hooks should not replace a clear public PHP API when one is more appropriate.

### Separation from storage

Registries describe available features.

Persistent user or site settings should be handled by dedicated storage or service classes.

For example:

    Dashboard Registry
        → Knows which panels exist

    User Preferences Service
        → Knows which panels a user has hidden

The coordinator combines those two sources when preparing the dashboard.

### Separation from rendering

Registries should not generate markup.

Registered definitions may identify a template or component:

~~~php
'body_view' => 'dashboard/panels/site-health'
~~~

The rendering layer remains responsible for displaying it.

---

## Initial registry-backed systems

### Dashboard panels

**Status:** Implemented

    Panel modules
        ↓
    Dashboard Registry
        ↓
    Dashboard Data coordinator
        ↓
    Panel component

### Quick actions

**Status:** Partially implemented

Quick actions already use structured definitions and capability filtering.

They may eventually receive a dedicated registry if extension requirements justify separating registration from data preparation.

### Framework settings

**Status:** Planned

Settings will be registered through structured definitions.

A future setting definition may resemble:

~~~php
$settings_registry->register_setting(
	'dashboard.density',
	array(
		'label'      => __( 'Dashboard Density', 'goug-framework' ),
		'type'       => 'select',
		'default'    => 'comfortable',
		'capability' => 'read',
		'choices'    => array(
			'compact',
			'comfortable',
			'spacious',
		),
	)
);
~~~

The same definition may be consumed by:

- The settings interface
- Validation
- Storage
- REST or AJAX handlers
- Documentation
- Default-value resolution

### Plugin integrations

**Status:** Planned

Integrations may register panels, settings, actions, or other features when supported plugins are active.

Examples include:

- Tutor LMS
- WooCommerce
- Membership systems

Integrations should extend existing registries rather than bypassing them with hardcoded template logic.

### Commands and notifications

**Status:** Future consideration

Command palette entries and dashboard notifications are likely candidates for dedicated registries if their requirements become substantial.

---

## When to create a registry

A registry should be considered when a feature collection meets several of these conditions:

- Multiple modules contribute items.
- Items share a common metadata structure.
- Items require validation or normalization.
- Items must be filtered or sorted.
- Third-party code may add or remove items.
- Persistent settings refer to items by identifier.
- More than one renderer or consumer uses the collection.
- The collection is likely to grow over time.

A registry should not be created merely because an array exists.

The framework will continue to follow the principle:

> **Wait for repeated use before introducing an abstraction.**

A small fixed collection may remain a local array until extension, normalization, or reuse requirements justify a registry.

---

## Consequences

### Positive consequences

- Features are easier to extend.
- Metadata has a predictable structure.
- Capability checks remain consistent.
- Rendering is separated from registration.
- Third-party integrations have defined extension points.
- User preferences can reference stable identifiers.
- Features are discoverable and testable.
- Framework modules remain less tightly coupled.
- New consumers can reuse registered definitions.

### Negative consequences

- Registries introduce additional classes and indirection.
- Developers must understand the registration lifecycle.
- Stable identifiers become part of the public contract.
- Poorly scoped registries can become oversized coordinators.
- Excessive registry use could over-engineer simple features.
- Runtime filters and registration order require careful documentation.

### Risks

A registry can become a dumping ground if it begins owning:

- Data collection
- Persistence
- Rendering
- Request handling
- Business logic
- Service construction

Each registry must retain a narrow responsibility centered on registered metadata.

---

## Alternatives considered

### Hardcoded feature arrays

Feature definitions could remain in one coordinator or template.

This is simpler initially but becomes difficult to extend, validate, and maintain as the framework grows.

**Decision:** Rejected for extensible framework systems.

### WordPress hooks only

Features could be added entirely through actions and filters without a registry object.

Hooks remain valuable extension points, but they do not by themselves provide normalization, lookup, removal, validation, or a discoverable API.

**Decision:** Rejected as the sole architecture.

### One universal framework registry

All panels, settings, actions, and integrations could be placed in one generic registry.

This reduces the number of classes but sacrifices clear contracts and domain-specific validation.

**Decision:** Rejected in favor of focused registries.

### Direct service discovery

The framework could scan folders or automatically instantiate classes.

Automatic discovery may be useful later, but it does not replace the need for normalized metadata and explicit registration contracts.

**Decision:** Deferred.

---

## Implementation guidelines

New registry classes should generally provide focused methods such as:

~~~php
register_item();
unregister_item();
has_item();
get_item();
get_items();
~~~

Method names should reflect the domain where practical:

~~~php
register_panel();
register_setting();
register_command();
~~~

Registries should:

- Sanitize identifiers
- Reject incomplete definitions
- Prevent malformed output
- Document required and optional fields
- Return normalized arrays
- Expose extension hooks deliberately
- Avoid rendering and persistence

Coordinators should:

- Resolve request context
- Apply capabilities and preferences
- Combine services and registered definitions
- Pass final data to consumers

Templates should:

- Render prepared data
- Escape output
- Avoid capability and storage logic

---

## Current application

The Dashboard Registry is the first formal implementation of this decision.

Its responsibilities include:

- Registering panels
- Normalizing panel definitions
- Filtering unavailable panels
- Applying capability-based layouts
- Sorting panels by row and priority

User preferences remain in `User_Preferences_Service`.

`Dashboard_Data` coordinates the two systems and applies user-specific visibility rules before rendering.

This separation is intentional and should be preserved.

---

## Future review

This decision should be reviewed when:

- The Settings framework is implemented.
- Third-party panel registration is documented as a public API.
- Multiple registries begin duplicating substantial logic.
- Automatic module discovery is considered.
- Registry definitions require schema versioning.

Shared behavior may eventually justify a small abstract registry base class, but only after at least three concrete registries demonstrate the same requirements.

---

## Summary

GOUG Framework will favor focused registries for extensible collections of features.

Registries define and normalize what is available.

Coordinators determine what applies to the current request.

Services collect and persist data.

Templates render the final result.

This pattern supports the framework’s broader goals:

> **Native WordPress before third-party dependencies.**

> **Register behavior. Do not hardcode it.**