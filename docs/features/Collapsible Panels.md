# Feature: Collapsible Panels

**Epic:** Dashboard Interaction

**Milestone:** v0.6.0

**Status:** Design

**Priority:** High

---

# Summary

Allow users to collapse and expand dashboard panels to reduce visual clutter while preserving their preferred workspace.

Collapsed state is stored per user and restored automatically on subsequent visits.

This feature builds upon the existing Dashboard Registry and Settings Framework without introducing new architectural concepts.

---

# User Story

As a dashboard user,

I want to collapse panels that I'm not currently using,

so I can focus on the information that matters most.

---

# Goals

- Improve dashboard usability.
- Reduce visual clutter.
- Persist user preferences.
- Maintain architectural separation of concerns.
- Reuse the existing Settings Framework.

---

# Non-Goals

This feature does **not** include:

- Drag-and-drop panel ordering
- Dashboard layouts
- Panel resizing
- Widget management

Those are future features.

---

# Acceptance Criteria

## Functional

- Every dashboard panel can be collapsed.
- Every dashboard panel can be expanded.
- State persists between sessions.
- State is unique per user.
- New panels default to expanded.
- State is restored automatically when loading the dashboard.

## Technical

- Uses User_Preferences_Service.
- Uses User_Settings_Service.
- Does not modify Dashboard Registry.
- Does not introduce new persistence mechanisms.
- Does not require panel-specific implementations.

## User Experience

- Collapse button appears in every panel header.
- Clear visual indicator of state.
- Smooth animation.
- Honors Reduced Motion preference.
- Keyboard accessible.

---

# Architecture

The Dashboard Registry always contains every registered panel.

Dashboard_Data prepares panel metadata for rendering.

Templates determine the initial collapsed state.

JavaScript manages user interaction and communicates changes back to the controller.

```
Dashboard Registry
        │
        ▼
Dashboard_Data
        │
        ▼
Template
        │
        ▼
JavaScript
        │
        ▼
Dashboard_Settings_Controller
        │
        ▼
User_Preferences_Service
        │
        ▼
User Meta
```

---

# Data Model

A new preference will be stored.

```php
collapsed_panels = [
    'site-health',
    'storage-usage',
    'recent-activity',
];
```

If a panel ID exists in the collection, it is considered collapsed.

If it is absent, the panel is expanded.

---

# API

## User_Preferences_Service

```php
is_panel_collapsed(string $panel_id): bool

set_panel_collapsed(
    string $panel_id,
    bool $collapsed
): bool

get_collapsed_panels(): array
```

---

# UI

Expanded

```
▼ Site Health
──────────────────────

Panel Body
```

Collapsed

```
▶ Site Health
```

Only the header remains visible.

---

# Accessibility

- Toggle implemented as a `<button>`.
- Uses `aria-expanded`.
- Uses `aria-controls`.
- Fully keyboard accessible.
- Honors Reduced Motion preference.

---

# Future Considerations

Potential future enhancements include:

- Collapse All
- Expand All
- Panel groups
- Remember scroll position
- Workspace layouts
- Auto-collapse inactive panels

This design should not prevent these features.

---

# Implementation Plan

## Task 1

Preference storage.

## Task 2

Preference service helpers.

## Task 3

Controller endpoints.

## Task 4

Template updates.

## Task 5

JavaScript interaction.

## Task 6

Restore state.

## Task 7

Animation and accessibility.

---

# Definition of Done

The feature is complete when:

- All panels can be collapsed.
- State persists.
- State restores correctly.
- Accessibility requirements are met.
- Reduced Motion is respected.
- Existing panels require no modification.
- Documentation is updated.