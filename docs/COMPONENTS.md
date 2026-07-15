# GOUG Framework Components

![Project Structure](images/project-structure.png)

> **Version:** 0.5.0-alpha  
> **Last Updated:** July 2026

---

# Overview

GOUG Framework includes a growing library of reusable user interface components.

Components eliminate duplicated presentation code while maintaining a consistent look, feel, and behavior throughout the framework.

Unlike templates, components are intentionally generic and should be reusable across multiple features.

---

# Component Philosophy

Components exist to solve one problem:

> **Render repeated interface patterns.**

Components should never:

- query WordPress
- perform business logic
- calculate application state

Components receive prepared data and render consistent markup.

---

# Component Lifecycle

Every dashboard feature follows this flow:

```text
Service

↓

Panel

↓

Template

↓

Component

↓

Rendered UI
```

Templates decide **what** to render.

Components decide **how** it is rendered.

---

# Component Categories

## Layout Components

Responsible for page structure.

Examples:

- Dashboard Panel
- Dashboard Rows
- Semantic Width System

---

## Card Components

Display grouped information.

Examples:

- Dashboard Card
- Statistic Card
- Status Card

Cards should provide:

- spacing
- borders
- typography
- hover states
- accessibility

---

## Status Components

Display health and state information.

Examples:

- Success
- Warning
- Error
- Information
- Neutral

State describes presentation.

Displayed values remain independent.

---

## Icon Components

GOUG Framework supports:

- Dashicons
- SVG Icons

SVG icons are preferred whenever possible because they:

- scale cleanly
- support theme colors
- remain consistent across devices

---

# Creating Components

A new component should only be created after repeated UI patterns emerge.

GOUG Framework follows:

> **Three uses before abstraction.**

Temporary duplication is acceptable.

Premature abstraction is not.

---

# Naming Conventions

Component templates:

```text
templates/components/
```

Examples:

```
panel.php
status-card.php
dashboard-card.php
```

SCSS modules:

```
_dashboard-cards.scss
_dashboard-status.scss
```

Keep names descriptive.

---

# Responsibilities

Components should:

✔ render HTML

✔ receive prepared data

✔ remain reusable

✔ remain generic

Components should never:

✘ query WordPress

✘ perform calculations

✘ register dashboard panels

✘ own application state

---

# Styling

Shared styling belongs in component SCSS partials.

Avoid duplicating:

- spacing
- colors
- transitions
- typography

Components should consume framework design tokens whenever practical.

---

# Accessibility

Components should include:

- semantic HTML
- keyboard accessibility
- ARIA attributes where appropriate
- sufficient color contrast
- meaningful labels

Accessibility is considered part of the component—not an enhancement.

---

# Future Component Library

The following components are planned as the framework grows.

## Dashboard

- Notification Card
- Empty State
- Loading State
- Error State

## Forms

- Toggle
- Select
- Search
- Button Groups

## Data

- Tables
- Charts
- Progress Indicators

## Navigation

- Command Palette
- Breadcrumbs
- Sidebar Sections

New components should continue following the same philosophy:

Reusable.

Predictable.

Independent.