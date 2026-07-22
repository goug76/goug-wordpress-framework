---
title: Dashboard Panel Sorting
feature: dashboard-panel-sorting
status: In Progress
version: 0.7.0
owner: Goug Labs
introduced: 0.7.0
last_updated: 2026-07-19
related:
  - Dashboard Registry
  - User Preferences
  - Dashboard Collapse
---

# Dashboard Panel Sorting

**Status:** 🚧 In Progress  
**Version:** v0.7.0  
**Author:** Goug Labs

---

## Overview

Dashboard Panel Sorting allows each user to customize the order of dashboard panels through an intuitive drag-and-drop interface.

Panel order is stored as a user preference and restored automatically when the dashboard loads.

The feature integrates with the existing Dashboard Registry and User Preference system while remaining independent of individual panel implementations.

---

## Goals

- Allow users to reorder dashboard panels.
- Keep the interaction intuitive and responsive.
- Preserve existing collapse functionality.
- Support mouse and touch devices.
- Lay the foundation for future keyboard reordering.
- Persist layout on a per-user basis.

---

## Non-Goals

This feature is **not** intended to provide a free-form dashboard designer.

The dashboard does **not** support:

- Arbitrary panel positioning
- Custom row/column placement
- Panel resizing
- Dragging panels outside the dashboard grid

CSS Grid remains responsible for calculating the final layout.

---

## User Experience

Users initiate dragging from the six-dot handle located on the left side of each panel header.

Dragging from any other area of the header continues to collapse or expand the panel.

Panels animate smoothly into their new positions.

The dashboard automatically scrolls while dragging near the viewport edges.

---

## Architecture

```text
Dashboard
        │
        ▼
PanelSort.js
        │
        ▼
SortableJS
        │
        ▼
Dashboard Grid
        │
        ▼
Panel Order
        │
        ▼
User Preferences (Future)
```

---

## Components

### JavaScript

**PanelSort.js**

Responsibilities:

- Initialize SortableJS
- Register drag events
- Register keyboard events
- Read panel order
- Save panel order *(future)*
- Restore panel order *(future)*

---

### PHP

No PHP changes are required for the initial drag-and-drop implementation.

Future versions will integrate with:

- Dashboard Preferences
- AJAX Controller
- Dashboard Registry

---

## Panel Requirements

Every sortable panel must contain:

```html
data-panel-id
```

Every sortable panel header must contain:

```html
.goug-drag-handle
```

The drag handle is the only draggable region.

---

## Styling

SortableJS state classes:

```css
.goug-panel--ghost
.goug-panel--chosen
.goug-panel--dragging
```

These classes are intentionally framework-owned rather than library-owned so their appearance can evolve independently.

---

## Accessibility

### Current

- Keyboard-focusable drag handles
- Pointer interaction
- Touch interaction

### Planned

- Arrow-key reordering
- Screen reader announcements
- Focus restoration after reorder

---

## Persistence

Future implementation:

```text
Panel Order
      │
      ▼
AJAX
      │
      ▼
Preference Service
      │
      ▼
User Meta
```

The implementation will reuse the existing dashboard preference infrastructure.

---

## Known Limitations

Panel ordering is based on **document order**.

CSS Grid determines the visual layout.

Panels with different column spans may not be placeable into every visually empty location because those locations do not always correspond to a valid insertion point in the DOM.

This behavior is expected and is **not considered a defect**.

---

## Future Enhancements

### Planned

- [ ] Persist panel order
- [ ] Restore panel order
- [ ] Keyboard reordering
- [ ] Reset dashboard layout

### Possible Future Features

- [ ] Panel groups
- [ ] Dashboard presets
- [ ] Import/export layouts
- [ ] Per-role default layouts
- [ ] Panel visibility management
- [ ] Panel resizing
- [ ] User-created dashboards

---

## Lessons Learned

This feature reinforced an important architectural principle:

> The dashboard controls **panel sequence**; CSS Grid controls **panel placement**.

Attempting to force SortableJS into becoming a layout engine would significantly increase complexity and duplicate functionality already provided by CSS Grid.

By separating these responsibilities, the implementation remains lightweight, maintainable, and predictable.

---

## Development Notes

### Libraries

- SortableJS

### Primary Files

```text
assets/js/modules/PanelSort.js
templates/components/panel.php
templates/admin-dashboard.php
```

### Dependencies

- Dashboard Registry
- User Preference Service *(future)*
- AJAX Controller *(future)*

---

## Testing Checklist

### Functionality

- [x] Drag panels using the six-dot handle
- [x] Clicking the handle does not collapse the panel
- [x] Clicking the header still collapses the panel
- [x] Keyboard focus reaches each drag handle
- [x] Dragging works with collapsed panels
- [x] Auto-scroll works while dragging
- [x] No JavaScript errors

### Edge Cases

- [x] Drag first panel to last position
- [x] Drag last panel to first position
- [x] Drag quickly
- [x] Drag slowly
- [x] Attempt to drag between mixed-width panels
- [x] Verify layout remains stable

### Remaining

- [ ] Persist order
- [ ] Restore saved order
- [ ] Keyboard reordering
- [ ] Reset layout