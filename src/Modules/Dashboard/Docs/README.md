# Example Module Blueprint

This directory is the canonical starting point for a GOUG Framework module.

## Creating a Module

1. Copy the complete `blueprints/module` directory into `src/Modules`.
2. Rename the copied directory to the module's PascalCase name.
3. Replace `Example` in namespaces, imports, class names, and documentation.
4. Replace the module metadata in `Module.php`.
5. Replace the package metadata in `module.json`.
6. Rename `ExampleProvider.php`.
7. Add only the folders and classes the module genuinely requires.

## Example

Copy:

```text
blueprints/module
```

to:

```text
src/Modules/ReadingProgress
```

Then replace:

```text
Example
```

with:

```text
ReadingProgress
```

The expected entry point becomes:

```php
Goug\Framework\Modules\ReadingProgress\Module
```

## Required Files

Every module requires:

```text
Module.php
module.json
```

A module with behavior normally requires at least one provider:

```text
Providers/
```

All other directories are optional and may be removed when unused.

## Responsibilities

- `Module.php` describes the module and declares providers.
- Providers coordinate registration and runtime behavior.
- Controllers coordinate requests.
- Services own feature logic.
- Registries own collections.
- Repositories own persistence.
- Models represent state.
- Views own presentation.
- Assets contain public frontend or admin resources.
- Resources contain internal non-public artifacts.
- Support contains small supporting types.
- Tests and documentation remain owned by the module.

## Rules

- Core may not depend on this module.
- The module may depend only on published Core APIs.
- The module may not reach into another module's internal classes.
- Constructors must not register hooks or execute feature behavior.
- Installing the module must not require editing Core.

# Module Rename Checklist

Replace all occurrences of:

```text
Example
example
ExampleProvider
```

Update the following:

- Module directory name
- PHP namespaces
- Provider filename
- Provider class name
- Imports
- Module identifier
- Module display name
- Description
- Version
- `module.json`
- Documentation

The module directory and namespace must match.

Example:

```text
src/Modules/RoleManager/Module.php
```

must declare:

```php
namespace Goug\Framework\Modules\RoleManager;
```