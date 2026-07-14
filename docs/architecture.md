# GOUG Framework Architecture

GOUG Framework is a lightweight, modular WordPress framework focused on building high-performance administrative and frontend features using native WordPress APIs.

It is designed to reduce plugin bloat by providing reusable, production-ready components that are fast, maintainable, and easy to extend.

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

## Framework Request Flow

Every request follows the same general pattern.

```text
WordPress
        ↓
Framework Bootstrap
        ↓
Feature Registration
        ↓
Service Initialization
        ↓
Registry
        ↓
Panel / Widget
        ↓
Template
        ↓
HTML Response
```

This separation keeps business logic, presentation, and registration independent.

## Project Structure

```text
inc/
├── classes/
│   ├── dashboard/
│   ├── services/
│   ├── features/
│   ├── helpers/
│   └── settings/
│
templates/
│   └── dashboard/
│
src/
├── js/
│   └── modules/
│
└── scss/
```

## JavaScript Architecture

JavaScript follows a modular ES Module architecture.

Each feature should exist as its own class.

```text
Constructor
        ↓
Cache DOM Elements
        ↓
Register Events
        ↓
Event Handlers
        ↓
Helper Methods
```

Entry files (`admin_script.js` and `script.js`) should only import and instantiate modules.

Business logic belongs inside the module, not in the entry file.

## Responsibilities

### Services

Retrieve and normalize data.

### Panels

Register dashboard panels and prepare view data.

### Views

Render HTML.

### Registry

Manage panel registration, ordering, visibility, and capabilities.

### JavaScript Modules

Handle frontend interactions for a single feature.

### SCSS

Style one feature or component.