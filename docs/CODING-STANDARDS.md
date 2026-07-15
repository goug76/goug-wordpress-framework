# GOUG Framework Coding Standards

> **Version:** 0.5.0-alpha  
> **Last Updated:** July 2026

---

# Purpose

This document defines the coding conventions used throughout GOUG Framework.

These standards exist to ensure every part of the framework feels consistent regardless of when it was written or who wrote it.

Consistency improves:

- readability
- maintainability
- onboarding
- debugging
- long-term scalability

Whenever possible, GOUG Framework follows the official WordPress Coding Standards while adopting modern software engineering practices where they improve clarity.

---

# Core Philosophy

The framework follows five guiding principles.

## 1. Clarity Over Cleverness

Write code that explains itself.

Avoid unnecessary abstractions, overly compact logic, and clever one-liners.

Future readability is more important than saving a few lines of code.

---

## 2. Single Responsibility

Every class should have one responsibility.

Examples:

- Services collect data.
- Panels register widgets.
- Templates render HTML.
- Components render reusable UI.
- Helpers provide generic utilities.

If a class begins serving multiple unrelated purposes, consider refactoring.

---

## 3. Small Methods

Methods should perform one task.

Instead of writing one 200-line method:

```php
build_dashboard()
```

Prefer:

```php
build_status_cards()
build_statistics()
build_actions()
```

The calling method should read almost like documentation.

---

## 4. Proven Abstractions

Do not create reusable code until the pattern has naturally emerged.

GOUG Framework follows:

> **Three uses before abstraction.**

This prevents unnecessary complexity while still encouraging reuse.

---

## 5. WordPress First

Whenever practical:

- Use WordPress APIs.
- Use WordPress hooks.
- Use WordPress capabilities.
- Follow WordPress security practices.

The framework extends WordPress—it does not replace it.

---

# PHP Standards

## File Headers

Every class begins with a descriptive file header.

```php
<?php
/**
 * Dashboard storage service.
 *
 * @package GOUG
 */
```

---

## Class Documentation

Every class includes a responsibility summary.

Example:

```php
/**
 * Provides cached WordPress storage usage information.
 *
 * Responsibilities:
 *
 * - Calculate storage usage.
 * - Cache filesystem scans.
 * - Normalize dashboard data.
 *
 * This service performs filesystem inspection only.
 */
```

Document intent—not implementation.

---

## Method Ordering

Methods should appear in a predictable order.

```text
Properties

Constructor

Public API

Protected Methods

Private Builders

Private Utilities
```

Readers should never need to hunt for important methods.

---

## Method Documentation

Document:

- purpose
- important behavior
- parameters
- return values

Explain **why**, not merely **what**.

Good:

> Normalize dashboard items before rendering.

Less helpful:

> Get dashboard items.

---

## Method Length

Aim for approximately:

- 10–40 lines

Longer methods should be composed from smaller helpers.

---

# Naming Conventions

## Classes

Use PascalCase.

```
Storage_Service
Dashboard_Registry
Quick_Actions_Service
```

---

## Methods

Use descriptive verb-based names.

```
get_data()
calculate_storage()
build_activity_item()
register_panel()
```

Avoid generic names like:

```
process()
handle()
run()
```

---

## Variables

Prefer descriptive names.

Good:

```php
$storage_items
```

Avoid:

```php
$data
$temp
$value
```

unless the context is obvious.

---

# Arrays

Associative arrays should use aligned keys where readability improves.

```php
array(
    'id'          => '',
    'title'       => '',
    'description' => '',
);
```

Normalize repeated array structures through helper methods whenever appropriate.

---

# Services

Services:

✔ collect data

✔ calculate

✔ normalize

✔ cache

Services never:

✘ render HTML

✘ register panels

✘ output markup

---

# Panels

Panels:

✔ define metadata

✔ request service data

✔ load templates

Panels never:

✘ query WordPress directly

✘ perform heavy calculations

---

# Templates

Templates:

✔ render HTML

✔ escape output

✔ validate expected data

Templates should avoid business logic.

---

# Components

Components exist to eliminate repeated presentation patterns.

Create components only after repeated use has been demonstrated.

---

# JavaScript Standards

GOUG Framework uses modern ES Modules.

Each module should have one responsibility.

Prefer:

```
DashboardSearch
QuickDraft
StorageChart
```

over large utility files.

Avoid global variables whenever possible.

---

# SCSS Standards

Styles are organized into layers.

```
Tokens

↓

Layout

↓

Shared Components

↓

Feature Modules

↓

Responsive Rules
```

Avoid duplicating colors, spacing, and transitions.

Use design tokens whenever practical.

---

# Security

Always:

- escape output
- sanitize input
- verify capabilities
- use nonces
- trust prepared data only

Security belongs in the backend—not JavaScript.

---

# Performance

Prefer:

- cached calculations
- lazy loading
- efficient queries
- reusable components

Avoid premature optimization.

Measure first.

Optimize second.

---

# Comments

Comments should explain intent.

Avoid comments that simply restate code.

Good:

```php
// Newly published content is detected using a small
// timestamp tolerance because publication and
// modification occur almost simultaneously.
```

Avoid:

```php
// Get post title.
```

The code already says that.

---

# Final Guideline

Whenever adding new code, ask:

1. Is it readable?
2. Does it have one responsibility?
3. Does it follow existing patterns?
4. Is the abstraction justified?
5. Will future developers understand it?

If the answer to all five is yes, the code probably belongs in GOUG Framework.

## The GOUG Review Checklist

Every pull request (or even every commit) should be mentally checked against questions like:

- oes this make the framework easier to understand?
- Does it reduce duplication without over-abstracting?
- Does it respect WordPress conventions?
- Would I write the next feature the same way?
- Can I explain this code six months from now without rereading it three times?