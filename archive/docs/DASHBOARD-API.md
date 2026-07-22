# GOUG Dashboard API

![Dashboard Lifecycle](images/dashboard-lifecycle.png)

> **Version:** 0.5.0-alpha  
> **Last Updated:** July 2026

---

# Overview

The GOUG Dashboard API provides a consistent way to build and extend the GOUG Framework dashboard.

Every dashboard feature follows the same architectural pattern.

```text
Service
    │
    ▼
Panel
    │
    ▼
Template
    │
    ▼
Shared Components
    │
    ▼
Rendered Dashboard
```

Each layer has a clearly defined responsibility.

Following this pattern keeps the dashboard modular, maintainable, and easy to extend.

---

# Dashboard Lifecycle

When the dashboard loads, the following sequence occurs.

```text
Dashboard Request

        │

        ▼

Dashboard Registry

        │

        ▼

Panel Registration

        │

        ▼

Service Collection

        │

        ▼

Prepared Data

        │

        ▼

Template Rendering

        │

        ▼

Shared Components

        │

        ▼

Rendered Dashboard
```

Panels never render themselves directly.

Services never generate HTML.

Templates never query WordPress.

---

# Creating a Dashboard Panel

Every new panel consists of three parts.

```
Service

↓

Panel

↓

Template
```

Some panels may also include:

- SCSS
- JavaScript
- Shared Components

---

# Step 1 — Create a Service

Services collect and normalize data.

Example:

```php
class Example_Service {

	public function get_data() {

		return array(
			'title' => 'Hello World',
		);

	}
}
```

Responsibilities include:

- Query WordPress
- Calculate values
- Normalize data
- Cache expensive operations

Services never render HTML.

---

# Step 2 — Create a Panel

Panels register dashboard metadata.

Example:

```php
class Panel_Example implements Dashboard_Panel {

	public function register(
		Dashboard_Registry $registry
	) {

		$service = new Example_Service();

		$registry->register_panel(
			array(
				'id'         => 'example',
				'title'      => __( 'Example', 'goug-framework' ),
				'icon'       => 'dashicons-admin-generic',
				'row'        => 3,
				'width'      => 'half',
				'priority'   => 10,
				'class_name' => 'goug-panel--example',
				'body_view'  => 'dashboard/components/example',
				'body_data'  => array(
					'example' => $service->get_data(),
				),
				'capability' => 'manage_options',
			)
		);

	}
}
```

Panels should only:

- request service data
- define metadata
- register themselves

Panels should not perform business logic.

---

# Step 3 — Create a Template

Templates receive prepared data.

Example:

```php
<?php

defined( 'ABSPATH' ) || exit;

$example = isset( $example )
	&& is_array( $example )
		? $example
		: array();

?>

<div class="goug-example">

	<h3>
		<?php
		echo esc_html(
			$example['title']
		);
		?>
	</h3>

</div>
```

Templates should:

- validate expected data
- escape output
- render HTML

Templates should not query WordPress.

---

# Registering Panels

Panels are registered through the Dashboard Registry.

```php
$registry->register_panel(
	array(
		'id'         => 'example',
		'title'      => __( 'Example', 'goug-framework' ),
		'icon'       => 'dashicons-admin-generic',
		'row'        => 3,
		'width'      => 'half',
		'priority'   => 10,
		'class_name' => 'goug-panel--example',
		'body_view'  => 'dashboard/components/example',
		'body_data'  => array(),
		'capability' => 'manage_options',
		'attributes' => array(),
	)
);
```

---

# Panel Properties

| Property | Description |
|----------|-------------|
| `id` | Unique panel identifier |
| `title` | Dashboard title |
| `icon` | Dashicon class |
| `icon_svg` | Optional SVG icon |
| `row` | Dashboard row |
| `width` | Panel width |
| `priority` | Position within row |
| `class_name` | Additional CSS classes |
| `body_view` | Template path |
| `body_data` | Data passed to template |
| `capability` | Required WordPress capability |
| `visible` | Optional visibility flag |
| `attributes` | Custom HTML attributes |

---

# Widths

GOUG Framework uses semantic widths.

```
full
half
third
quarter
```

Example:

```php
'width' => 'third'
```

The registry translates these into the appropriate CSS classes.

---

# Row Ordering

Panels are rendered using:

```text
Row

↓

Priority

↓

ID
```

This guarantees deterministic rendering regardless of registration order.

---

# Services

Services should return normalized data.

Good:

```php
return array(
	'title' => '',
	'items' => array(),
);
```

Avoid returning unrelated structures from the same service.

---

# Shared Components

Whenever possible, templates should use shared components instead of duplicating markup.

Examples include:

- Dashboard cards
- Status cards
- Statistics
- SVG icons

Only create new components after repeated use has been demonstrated.

---

# Styling

Each feature may include its own SCSS module.

```
_dashboard-example.scss
```

Common styles belong in shared component partials.

Avoid duplicating:

- spacing
- colors
- transitions
- typography

Use framework design tokens whenever practical.

---

# JavaScript

JavaScript is optional.

When needed:

```
src/js/modules/
```

Each module should:

- have one responsibility
- use ES Modules
- initialize through its constructor
- avoid global variables

Example:

```javascript
import Example from './modules/Example';

new Example();
```

---

# Security

Dashboard panels should always:

- verify capabilities
- escape output
- sanitize input
- verify nonces
- trust prepared service data

Services should assume templates are public.

Templates should assume data has already been normalized.

---

# Performance

Dashboard panels should remain lightweight.

Prefer:

- cached calculations
- reusable services
- normalized data
- lazy loading

Avoid expensive work inside templates.

---

# Extension Points

Whenever appropriate, expose WordPress hooks.

Examples:

```php
do_action(
	'goug_dashboard_register_panels'
);
```

```php
apply_filters(
	'goug_dashboard_activity_items',
	$items
);
```

Hooks should expose prepared data whenever possible.

---

# Recommended Development Workflow

When building a new dashboard feature:

1. Create the Service.
2. Normalize the returned data.
3. Create the Panel.
4. Register the panel.
5. Create the Template.
6. Reuse existing Components.
7. Add SCSS only when necessary.
8. Add JavaScript only when needed.
9. Test capabilities.
10. Test empty states.
11. Test responsive behavior.

---

# Dashboard Checklist

Before merging a new dashboard feature:

- Service has one responsibility.
- Panel performs no business logic.
- Template contains no WordPress queries.
- Output is escaped.
- Inputs are sanitized.
- Capabilities are checked.
- Responsive layout is verified.
- Shared components are reused.
- Comments explain intent.
- Documentation is updated.

---

# Guiding Principle

Every dashboard feature should look as though it was built by the same developer.

Consistency is more valuable than cleverness.

When in doubt, follow the existing patterns established throughout GOUG Framework.