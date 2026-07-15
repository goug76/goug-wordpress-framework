# ADR-002: Semantic Dashboard Layout

**Status:** Accepted

**Date:** July 2026

---

# Context

Early dashboard layouts were based on CSS grid spans.

Panels defined their size using implementation-specific values such as:

- span 6
- span 4
- span 3

Although technically correct, these values exposed implementation details and reduced readability.

---

# Decision

Dashboard panels use semantic width definitions.

Supported widths are:

- full
- half
- third
- quarter

Example:

```php
'width' => 'half'
```

The Dashboard Registry translates these semantic values into layout classes.

---

# Consequences

Benefits include:

- improved readability
- implementation independence
- easier future layout changes
- simpler dashboard customization
- cleaner APIs

---

# Alternatives Considered

Using raw CSS grid spans.

Rejected because layout implementation should remain internal to the framework.

---

# Guiding Principle

Framework APIs should describe intent rather than implementation.