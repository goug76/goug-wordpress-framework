# Architecture Decision Records (ADRs)

Architecture Decision Records (ADRs) capture important architectural decisions made during the development of the GOUG Framework.

Unlike the documents in the parent `docs/` directory, ADRs are **historical records**. They explain **why** a decision was made at a particular point in time.

## Purpose

ADRs help preserve architectural intent by documenting:

- The problem being solved
- The context surrounding the decision
- The decision that was made
- Alternatives that were considered
- The consequences of the decision

They provide future contributors with insight into the reasoning behind the framework's architecture.

## Living Documents vs ADRs

The documentation in `/docs` is considered **living documentation**. It evolves as the framework evolves.

Examples include:

- `FRAMEWORK.md`
- `ARCHITECTURE.md`
- `MODULES.md`

These documents describe **how the framework currently works**.

ADRs, on the other hand, describe **how the framework arrived at its current design**.

An ADR should not be updated simply because the architecture evolves.

If a significant architectural decision changes, a **new ADR** should be created that supersedes the previous one.

## ADR Lifecycle

Each ADR should include:

- Status
- Date
- Context
- Decision
- Alternatives
- Consequences
- Related Documents

Typical statuses include:

- Proposed
- Accepted
- Superseded
- Deprecated

## Naming Convention

ADRs are numbered sequentially.

Examples:

```text
0001-framework-philosophy.md
0002-shared-infrastructure-services.md
0003-module-lifecycle.md
```

Numbers are never reused.

## Editing ADRs

Accepted ADRs should only be edited to correct spelling, formatting, or factual mistakes.

Changes to architectural direction should be documented in a new ADR rather than rewriting history.

## Philosophy

The goal of an ADR is not to prove a decision was perfect.

The goal is to preserve the reasoning behind the decision so future maintainers understand the trade-offs that were considered at the time.