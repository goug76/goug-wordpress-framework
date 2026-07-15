# ADR-005: Modern Asset Pipeline

**Status:** Accepted

**Date:** July 2026

---

# Context

Traditional WordPress themes often rely on manually managed CSS and JavaScript assets.

As GOUG Framework expanded, maintaining compiled assets manually became increasingly difficult.

---

# Decision

GOUG Framework adopts a modern asset pipeline based on:

- Vite
- SCSS
- ES Modules

Assets are organized into modular source files and compiled for production.

---

# Consequences

Benefits include:

- faster builds
- modular architecture
- improved maintainability
- modern JavaScript support
- reusable SCSS components

---

# Guiding Principle

Developer tooling should simplify development without increasing runtime complexity.