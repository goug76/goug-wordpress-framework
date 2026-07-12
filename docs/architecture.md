# GOUG Framework Architecture

GOUG Framework is a lightweight, theme-agnostic WordPress framework focused on performance, modularity, and native WordPress conventions.

## Core Principles

1. Performance first
2. WordPress native
3. Modular design
4. Theme agnostic
5. Secure by default
6. Predictable structure
7. Progressive enhancement
8. No unnecessary dependencies
9. Documentation matters
10. Prefer simple code over clever code

## Dashboard Architecture

The dashboard uses a registry-driven component architecture.

```text
Dashboard Controller
        ↓
Dashboard Data Coordinator
        ↓
Dashboard Panel Modules
        ↓
Dashboard Registry
        ↓
Dashboard Template
        ↓
Panel and View Components
```

### Dashboard Controller

Responsible for:

- Registering the custom dashboard page
- Redirecting the native dashboard
- Checking permissions
- Rendering the dashboard view
- Applying global admin branding
- Dashboard Data Coordinator

Responsible for:

- Coordinating dashboard data services
- Initializing panel modules
- Returning prepared dashboard data

It should not contain presentation markup or grow into a collection of unrelated data calculations.

### Dashboard Registry

Responsible for:

- Registering panels
- Removing panels
- Checking capabilities and visibility
- Sorting panels by priority
- Returning the final panel collection
### Panel Modules

Each panel module implements the Dashboard_Panel interface:

```public function register( Dashboard_Registry $registry );```

A panel module owns its panel definition and prepares the data required by its view.

### Views

Views display prepared data.

Views may contain:

- Escaped HTML output
- Small display conditions
- Loops over prepared arrays

Views should not run database queries or calculate dashboard data.