# BEAR.AppKata Modernization Conventions

## Resource Boundaries

- Page Resources are HTML workflows. They may coordinate forms, sessions, and Application UseCases.
- App Resources are HAL/API contracts. They should expose stable read and write contracts with `#[Link]`, `#[JsonSchema]`, and, where available, `#[Alps]`.
- Workflow-heavy writes stay in Application/Domain. Resource-to-Command direct writes are reserved for explicit reference samples.

## Read Models and BDR Results

- Query-local result objects live under `AppCore\Infrastructure\Result`.
- These objects are not Domain entities. They wrap query rows or DML metadata and expose pure inquiry methods only.
- `PostQueryInterface` result classes are paired with Query interfaces so the query and result shape are readable together.
- `AffectedRows` samples live in `Infrastructure\Query\Samples` and do not change canonical command interfaces.

## Hypermedia Tests

- A workflow test hard-codes only its entry URI.
- Follow-on transitions use `ResourceInterface::href($rel, $vars, $ro)`.
- Field shape belongs to JsonSchema/resource tests. Hypermedia tests validate transitions and business invariants.
- HAL envelope contract tests pin choreography under `_links` and taxonomy under `_embedded`.

## Smoke Tests

- SQL smoke tests are shallow guardrails for placeholder/parameter drift.
- MediaQuery smoke tests prove reference dispatch shapes such as result classes and DML metadata.
- Business behavior stays in Domain, Resource, and Hypermedia tests.

## Cache and Async

- Cache examples should use framework surface such as `#[CacheableResponse]`, not manual cache primitives in resources.
- Cache tests should use an in-memory cache override and assert ETag/conditional behavior.
- Async embed examples require stable `#[Embed]` contracts first. Runtime-specific ext-parallel/ZTS or Swoole checks should be optional unless CI provisions that runtime.
