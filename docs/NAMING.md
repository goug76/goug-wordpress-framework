# GOUG Framework Naming Specification

> **Version:** 0.1.0
> **Status:** Draft
> **Last Updated:** 2026-07-21

---

# Purpose

This document defines the naming conventions used throughout the GOUG Framework.

Naming is more than style.

Names communicate architecture.

A well-chosen name should describe responsibility, reveal intent, and remain accurate as the implementation evolves.

This specification focuses on *how to think about naming*, not formatting rules enforced by automated tools.

---

# Naming Philosophy

Good names reduce documentation.

Good names reduce comments.

Good names reduce mistakes.

Every name should help another developer understand the purpose of a class without reading its implementation.

Names should describe responsibility.

Not implementation.

Not history.

Not convenience.

---

# Responsibility Before Implementation

Classes should be named after what they own rather than how they work.

Good examples:

- ViewRenderer
- AssetRegistry
- ModuleMetadata
- SettingsRepository
- HookRegistrar

Poor examples:

- PHPTemplateLoader
- JsonSettingsClass
- FileHelper

Implementations change.

Responsibilities usually do not.

A good name should remain correct even when the implementation changes.

---

# Every Responsibility Deserves an Owner

Every significant responsibility should have a clearly identifiable owner.

Examples:

| Responsibility | Owner |
|----------------|-------|
| Rendering views | ViewRenderer |
| Registering assets | AssetRegistry |
| Module metadata | ModuleMetadata |
| Registering hooks | HookRegistrar |
| Module discovery | ModuleDiscovery |

A developer should be able to identify ownership simply by reading class names.

---

# Prefer Specific Nouns

Specific names communicate intent.

Broad names accumulate unrelated responsibilities.

Prefer:

- AssetRegistry
- SettingsRepository
- ViewRenderer
- DashboardPanels
- ModuleConfiguration

Instead of:

- Helper
- Utility
- Common
- Misc
- Stuff

Specific names naturally encourage focused classes.

---

# Generic Names

Some names have become common throughout software development because they are easy to choose.

Unfortunately, they often communicate very little.

The following names should be used only when they accurately describe the class responsibility.

## Helper

A helper helps something.

If the name cannot answer *what* it helps, choose a more descriptive name.

---

## Utility

Utilities often become collections of unrelated functionality.

Prefer naming the responsibility directly.

---

## Common

"Common" describes frequency rather than responsibility.

Shared behavior should still have a clearly defined owner.

---

## Shared

Shared describes usage.

It does not describe ownership.

---

## Manager

Manager is not inherently wrong.

However, it should describe genuine coordination or management responsibilities.

For example:

- AssetManager may coordinate multiple asset collections.
- CacheManager may coordinate multiple cache providers.

If "Manager" could be replaced with a more specific responsibility, prefer the more specific name.

Examples:

Instead of:

- SettingsManager

Consider:

- SettingsRepository
- SettingsValidator
- SettingsMigrator
- SettingsRenderer

The more specific name better communicates responsibility.

---

# Classes Are Nouns

Classes represent things.

Methods represent actions.

Prefer:

- ViewRenderer
- ModuleRegistry
- AssetLoader

Over:

- RenderViews
- RegisterModules
- LoadAssets

The class owns the responsibility.

The methods perform the work.

---

# Framework Names

Framework-level names should remain broad because they represent architectural concepts.

Examples:

- Core
- Module
- Registry
- Contract
- Service
- Provider

These names form the vocabulary of the framework.

---

# Module Names

Module classes should build upon the framework vocabulary.

Examples:

- DashboardRegistry
- DashboardAssets
- DashboardSettings
- DashboardRoutes
- DashboardPermissions

Combining a module name with a framework concept produces names that are both descriptive and consistent.

---

# Prefer Clarity Over Brevity

Long names are acceptable when they improve understanding.

For example:

DashboardPanelRegistry

is preferable to:

Registry

Developers read code far more often than they write it.

Clarity should always take precedence over saving a few characters.

---

# Names Should Age Well

Names should remain accurate even as implementations evolve.

For example:

ViewRenderer

may initially render PHP templates.

Later it may render Twig templates.

Its responsibility remains rendering views.

By contrast:

PHPTemplateLoader

becomes misleading as soon as the implementation changes.

Choose names that describe enduring responsibilities rather than temporary implementation details.

---

# Questions to Ask

Before naming a class, ask:

- What responsibility does this class own?
- Would another developer understand its purpose from the name alone?
- Am I describing implementation or responsibility?
- Could this class become more focused with a more specific name?
- Will this name still be accurate in five years?

If the answer is unclear, reconsider the name.

---

# Design Principles

## Responsibility first.

Choose names that describe ownership.

---

## Be specific.

Specific names encourage focused classes.

---

## Prefer nouns.

Classes own responsibilities.

Methods perform actions.

---

## Avoid implementation details.

Responsibilities outlive implementations.

---

## Clarity beats brevity.

Readable code is maintainable code.

---

# Guiding Principle

> **If a name needs a comment to explain it, it is probably the wrong name.**

Names are the first form of documentation.

Choose them carefully.

---

# Next Steps

Subsequent documents define:

- Public APIs
- Versioning
- Developer workflow
- Coding standards