# GOUG Framework Technical Debt

This document tracks architectural improvements that have been intentionally postponed.

These items are not bugs.

They are future improvements once the framework architecture has stabilized.

---

# Architecture

## Naming

- Rename Services → Providers
- Rename Panels → Widgets
- Rename Dashboard Registry → Widget Manager

---

## Base Classes

Create shared base classes after patterns stabilize.

Planned:

- Dashboard Service
- Dashboard Provider
- Dashboard Widget

---

## Formatting Helpers

Centralize formatting methods.

Examples:

- Relative time
- File size
- Status badges
- Version formatting
- Percentages

---

## Dashboard

### Layout

- User configurable layout
- Drag-and-drop widgets
- Adjustable widget width
- Reset layout

---

### Recent Activity

- Client-side filters
- Live updates
- Activity categories
- Infinite history page

---

### Site Health

Expand beyond WordPress.

Future checks:

- Docker
- Git
- SSL expiration
- Scheduled backups
- Cloudflare
- Reverse proxy
- Disk usage
- Custom health providers

---

## JavaScript

Continue converting remaining JavaScript into ES modules.

Future modules:

- Dashboard filters
- Panel customization
- Notifications
- Theme switching

---

## SCSS

Continue separating dashboard styling.

Goal:

One SCSS file per major component.

---

## Framework

Feature manager.

Optional modules:

- Dark Mode
- Reading Progress
- Table of Contents
- Copy Code
- Back to Top
- External Links
- Image Lightbox

---

## Documentation

Future documentation:

- Architecture Guide
- Coding Standards
- Creating Features
- Creating Dashboard Widgets
- JavaScript Module Guide
- SCSS Style Guide

---

## Performance

Investigate:

- Transient caching
- Deferred loading
- Lazy initialization
- Asset optimization

---

## Release

Before v1.0:

- Review naming
- Remove deprecated code
- Final documentation pass
- Accessibility audit
- Performance audit