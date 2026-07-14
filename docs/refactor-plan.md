# GOUG Framework Refactor Plan

## Goal

Improve maintainability, consistency, and extensibility without changing the dashboard’s appearance or behavior.

## Refactor Rules

- Keep each change small and testable.
- Commit after every successful refactor session.
- Do not combine refactoring with new features.
- Prefer composition over inheritance.
- Abstract repeated patterns only after they are proven.
- Preserve backwards-compatible hooks and filters where practical.
- The dashboard should look and behave exactly the same after each step.

---

## Phase 1 — Code Audit

### Dashboard Infrastructure

- [ ] Dashboard controller
- [ ] Dashboard data coordinator
- [ ] Dashboard registry
- [ ] Panel interface
- [ ] View renderer
- [ ] Shared helpers

Review for:

- Mixed responsibilities
- Tight coupling
- Repeated normalization
- Manual data forwarding
- Missing validation
- Naming consistency

### Services

- [ ] Activity Service
- [ ] Content Service
- [ ] Database Service
- [X] Development Service
- [ ] Draft Service
- [ ] Health Service
- [ ] Site Service
- [ ] Storage Service
- [ ] System Service
- [ ] Update Service
- [ ] User Service

Review for:

- Request-level caching
- Transient caching
- Repeated formatting
- Repeated filtering
- WordPress API boundaries
- Error handling
- Expensive operations
- Constructor dependencies

### Panel Modules

- [ ] Site Status
- [ ] Site Health
- [ ] At a Glance
- [ ] Storage Usage
- [ ] Quick Actions
- [ ] Recent Activity
- [ ] Quick Draft
- [X] Development

Review for:

- Repeated registration metadata
- Data preparation inside panels
- Capability handling
- Width and row metadata
- Icon handling
- Empty-state behavior
- Shared body-layout behavior

### Templates and Components

- [ ] Shared panel component
- [ ] Stat grid
- [ ] Status cards
- [ ] Quick Actions
- [ ] At a Glance
- [ ] Recent Activity
- [ ] Quick Draft
- [ ] Site Health
- [ ] Storage Usage
- [ ] Development

Look for reusable patterns:

- Metric item
- Action card
- Feature card
- Information-list item
- Timeline item
- Panel footer link
- Empty state
- Status indicator

### JavaScript

- [ ] `admin_script.js`
- [ ] `script.js`
- [ ] Quick Draft module
- [ ] Keyboard Shortcuts module
- [ ] Other existing modules

Review for:

- Entry-file responsibilities
- Event binding conventions
- Repeated DOM helpers
- Error handling
- Translatable strings
- Module naming
- Frontend/backend separation

### SCSS

- [ ] Base dashboard layout
- [ ] Panel structure
- [ ] Semantic widths
- [ ] Shared cards
- [ ] Shared forms
- [ ] Shared icons
- [ ] Panel-specific partials
- [ ] Responsive/container queries

Review for:

- Duplicate card styling
- Duplicate spacing values
- Duplicate state colors
- Panel-specific layout rules that belong in shared utilities
- Dead selectors
- Conflicting media and container queries

---

## Phase 2 — Formatting and Helpers

- [ ] Audit existing helper functions
- [ ] Centralize icon URL handling
- [ ] Centralize relative-time formatting
- [ ] Centralize file-size formatting
- [ ] Centralize percentages and localized numbers
- [ ] Avoid wrappers that add no value over native WordPress functions

---

## Phase 3 — Shared UI Components

Extract only patterns used by at least three consumers.

- [ ] Metric item
- [ ] Action card
- [ ] Feature card
- [ ] Timeline item
- [ ] Information-list item
- [ ] Empty state
- [ ] Footer action link

---

## Phase 4 — Service Cleanup

- [ ] Standardize request-level caching where useful
- [ ] Review transient ownership
- [ ] Separate formatting from data collection
- [ ] Review expensive filesystem and health operations
- [ ] Avoid premature base-class inheritance

---

## Phase 5 — Panel Cleanup

- [ ] Standardize registration metadata
- [ ] Review whether a panel factory or helper is justified
- [ ] Preserve declarative panel definitions
- [ ] Add body-layout metadata if repetition warrants it
- [ ] Validate width, row, and future customization constraints

---

## Phase 6 — JavaScript and SCSS Cleanup

- [ ] Move all feature behavior into OOP modules
- [ ] Keep entry files limited to imports and instantiation
- [ ] Consolidate shared UI styles
- [ ] Remove dead code
- [ ] Verify compiled assets

---

## Phase 7 — Naming and Documentation

Potential future naming changes:

- [ ] Services → Providers
- [ ] Panels → Widgets
- [ ] Dashboard Registry → Widget Manager

These changes should happen only after responsibilities and public APIs have stabilized.

Update:

- [ ] Architecture documentation
- [ ] Coding standards
- [ ] JavaScript module guide
- [ ] Dashboard extension guide
- [ ] Hooks and filters reference

---

## Completion Criteria

- No visual regressions
- No behavior regressions
- No new PHP warnings or notices
- No duplicate expensive operations
- No dead files or selectors
- Consistent formatting and naming
- Documentation reflects the actual code