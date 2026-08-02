# Example Module

The Example module demonstrates the complete GOUG Framework module architecture.

## Flow

```text
ModuleDiscovery
    ↓
ModuleRegistry
    ↓
ModuleLoader
    ↓
ExampleProvider
    ↓
ExampleService
    ↓
ExampleController
    ↓
example-notice.php
```

## Responsibilities

### Module.php

Describes the module and declares its providers.

### ExampleProvider

Constructs the module's dependencies and activates runtime behavior.

### ExampleService

Owns the feature's business logic and content.

### ExampleController

Registers WordPress hooks and coordinates the service and view.

### example-notice.php

Renders presentation markup only.

## Lifecycle

During registration:

```text
ExampleProvider::register()
    ↓
Create ExampleService
    ↓
Create ExampleController
```

During boot:

```text
ExampleProvider::boot()
    ↓
ExampleController::hooks()
    ↓
Register admin_notices
```

During the WordPress request:

```text
admin_notices
    ↓
ExampleController::renderNotice()
    ↓
ExampleService supplies data
    ↓
View renders markup
```

## Architectural Rules Demonstrated

- Core does not know the Example module exists.
- The module is discovered from its folder convention.
- The module describes providers but does not execute them.
- The provider wires dependencies.
- The service owns feature logic.
- The controller coordinates WordPress.
- The view owns presentation.
- Constructors do not register hooks or produce output.