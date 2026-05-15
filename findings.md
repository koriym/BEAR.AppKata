# Findings & Decisions

## Requirements (initial planning assumptions; later superseded in implementation)
- Build a modernization plan for BEAR.AppKata that avoids rework.
- Use MyVendor.Cms as a reference, but do not modify MyVendor.Cms in this track.
- Keep DDD/Application/Domain write workflows.
- Modernize read-side Resource contracts first.
- Skip CSRF/Form work. (initial plan; later changed)
- Start with Admin read side.

**Updated scope**: CSRF/Form work is no longer skipped. The Form layer was migrated from
Doctrine annotations to PHP 8 attributes (see `ray/web-form-module` dev-migrate-attribute
branch) as part of this PR stack.

## Research Findings
- `source/app/src/Resource/App` currently has only minimal `Index`, `Admin/Index`, and `User/Index` resources.
- `source/app/var/schema/response` and `source/app/var/schema/request` are effectively empty.
- `AppModule` already installs `JsonSchemaModule` for `var/schema/response` and `var/schema/request`.
- `AppModule` already installs `MediaQueryModule` over `ddd/core/src/Infrastructure/Query` and `var/sql`.
- Admin read queries already exist: `AdminQueryInterface::item`, `itemByUsername`, `itemByEmailAddress`, `AdminEmailQueryInterface::list`, and `AdminPermissionQueryInterface::list`.
- Existing Admin write workflows are Application/Domain use cases and should remain there.
- MyVendor.Cms uses App Resources with `#[Link]`, `#[Embed]`, `#[JsonSchema]`, and response schemas under `var/json_schema`.
- MyVendor.Cms preserves embed slots by mutating embedded body slots first and then using `$this->body += [...]`.
- MyVendor.Cms has hermetic testing patterns: direct Resource tests, HAL envelope tests, `FakePages`, and a broader `FakeSqlQuery`.
- Current BEAR.AppKata dependencies have `bear/resource 1.26.3`, which includes `#[Link]` and `#[JsonSchema]`.
- Current `bear/api-doc 1.3.1` does not include `BEAR\ApiDoc\Annotation\Alps`.
- Current `ray/media-query 0.17.1` includes `#[Pager]` and `PagesInterface`, but not MyVendor.Cms's Ray.MediaQuery 1.1 PostQueryInterface result path.
- Current BEAR.AppKata vendor tree does not contain `Ray\MediaQuery\Result\PostQueryInterface`, `AffectedRows`, or `InsertedRow`.
- MyVendor.Cms BDR samples:
  - `ArticleSelectionQueryInterface::list()` returns `ArticleSelection`.
  - `ArticleSelection` implements `PostQueryInterface`, `IteratorAggregate`, and `Countable`.
  - `ArticleAffectedRowsCommandInterface` returns `Ray\MediaQuery\Result\AffectedRows`.
  - `MediaQuerySamplesTest` verifies `PagesInterface`, typed result class, and affected-row metadata.
  - `FakeSqlQueryTest` verifies fake `execPostQuery` behavior for inserted rows, affected rows, and select row wrapping.
- MyVendor.Cms includes `bear/async ^0.3` and uses `bin/async.php` plus ext-parallel/ZTS or Swoole-specific Docker runtimes.
- Packagist/composer metadata currently shows `bear/async 0.3.0` requiring `bear/resource ^1.32`; BEAR.AppKata currently has `bear/resource 1.26.3`.
- BEAR.AppKata already has `apidoc.xml`, but it currently generates only `html` and has no ALPS profile entry.
- BEAR.AppKata already has a Hypermedia testsuite in `phpunit.xml.dist`, but the current test is a placeholder-style index check.
- BEAR.AppKata already has BEAR.QueryRepository and Symfony cache dependencies installed transitively; `ProdModule` installs `CacheVersionModule`.
- MyVendor.Cms has cache showcase resources using `#[CacheableResponse]`, in-memory cache test override, ETag checks, and source-code invariant tests.
- MyVendor.Cms separates smoke tests into SQL smoke and MediaQuery smoke layers.
- `Ray\Di\Types` and `Ray\Aop\Types` use final `Types` classes as Psalm type-alias catalogs for repeated framework vocabulary: dependency containers, binding names, interceptor lists, matcher configs, method bindings, reflection references, and similar boundary shapes.
- BEAR.AppKata has comparable repeated application boundary shapes: Admin read response fragments, mail template/options arrays, fieldset nested address arrays, and smoke/fake SQL parameter maps.
- Fake JSON should be treated as executable domain vocabulary, not just mock data. A small canonical Admin read fixture can make the kata's terms visible: primary email, verified/unverified emails, allow/deny permissions, active status, and missing-id behavior.
- The current fake classes are still useful as typed adapters. The next step is not replacing them with raw JSON assertions, but loading canonical JSON fixtures and hydrating Entity/QueryResult objects from them.
- `ray-di/Ray.FakeQuery` already provides the intended adapter: it replaces Ray.MediaQuery SQL execution with JSON fixtures and hydrates the same query interfaces. It should be the preferred implementation path before writing app-local fake JSON infrastructure.
- Ray.FakeQuery should replace the `Ray\MediaQuery\DbQueryInterceptor` binding in override contexts, not try to outrank MediaQuery pointcuts with interceptor priority.
- Ray.FakeQuery is viable as a 1.0 select-fixture release baseline: row, nullable row, JSONL row list, factory hydration, typed rowlist wrappers, and MediaQuery override replacement are covered by tests and CI.
- `AffectedRows` and `InsertedRow` fake support is mechanically feasible from explicit fixture data, but should be tracked as DML metadata result support rather than fake PDO or mutable fake database behavior.

## Technical Decisions

| Decision | Rationale |
|----------|-----------|
| First code slice should be Admin profile read API | It demonstrates read projection, schema, HAL link, and QueryResult purity while avoiding write workflow churn. |
| Use direct fake query implementations for the first resource tests | It keeps tests hermetic without introducing a large fake SQL router too early; promote the data to canonical fake JSON once the examples are reused as shared vocabulary. |
| Add `#[JsonSchema]` and `#[Link]` before `#[Alps]` if dependency update is not done first | These attributes are already available in the installed BEAR.Resource version. |
| Upgrade Ray.MediaQuery for BDR result samples as a gated phase | The user explicitly wants affected rows and rowlist/result class features, and they require the newer Result API. |
| Add collection/pagination after item/profile | `#[Pager]` and `PagesInterface` are most useful after an item contract is stable. |
| Add hypermedia workflow tests | They validate Resource choreography through rels and are a core BEAR-native teaching pattern. |
| Add ALPS and ApiDoc/OpenAPI after dependency/schema baseline | ALPS needs a newer `bear/api-doc`, and OpenAPI output is only useful after schemas are meaningful. |
| Add cache showcase after Resource contracts | QueryRepository caching needs stable resource identity, links, and embed dependency shape to be educational. |
| Add smoke tests as separate shallow layers | They catch SQL/MediaQuery drift without duplicating Resource or Domain tests. |
| Add `bear/async` as a late optional showcase | It requires BEAR.Resource upgrade plus runtime-specific extensions, so it should not block the first Admin read slice. |
| Record MyVendor.Cms reference-test results | The port should demonstrate that the source reference behavior was verified, not merely copied. |
| Install `bear/async` as opt-in support | `bear/resource ^1.32` now satisfies the package dependency, but ext-parallel/ZTS or another supported runtime should remain outside default CI/test gates. |
| Keep cache parent embed as a response showcase | MyVendor.Cms cache reference tests failed on embed dependency assertions in the reference working tree, so BEAR.AppKata adapts the stable leaf invalidation path and records the parent limitation. |
| Use a Psalm baseline for legacy/project-wide static-analysis drift | The modernization introduces Psalm 6, but existing code has broad informational/static issues outside this migration scope; new gates still run via `composer sa`. |
| Add an app-local `Types.php` catalog only for duplicated boundary shapes | The Ray.Di/Ray.Aop pattern is valuable here, but only when an array shape crosses files/layers; Domain objects, Entities, QueryResults, and one-off resource bodies should remain explicit. |
| Treat fake JSON as executable domain vocabulary | JSON fixtures can make canonical examples readable outside PHP constructors; prefer `ray/fake-query` so the fixture vocabulary remains connected to Ray.MediaQuery query IDs and hydrated Entity/QueryResult contracts. |
| Release Ray.FakeQuery as select-focused 1.0 | The public API is small enough to stabilize now; `AffectedRows` / `InsertedRow` fixture support can be added in a later minor release without delaying the core JSON/JSONL adapter. |

## Issues Encountered

| Issue | Resolution |
|-------|------------|
| GitHub issues were disabled for `koriym/BEAR.AppKata` | Enabled issues and created issue #1. |
| `#[Alps]` is not available in current `bear/api-doc 1.3.1` | Treat as a dependency gate before adding Alps annotations. |
| Ray.MediaQuery 1.1 has multiple dependency upgrades | Defer the upgrade and avoid PostQueryInterface-specific code in the first slice. |
| `bear/async` requires newer BEAR.Resource and optional runtime extensions | Keep it as a later showcase/spike after Embed contracts are stable. |
| BDR result samples depend on newer Ray.MediaQuery APIs | Promote the dependency work to a gated implementation phase rather than hand-rolling local result abstractions. |
| BEAR.DevTools HTTP test helper is incompatible with the upgraded BEAR.Resource signature | Rewrote the HTTP workflow test to exercise `Bootstrap` directly instead of `BEAR\Dev\Http\HttpResource`. |
| Ray.MediaQuery 1.1 removed `CamelCaseTrait` | Removed the obsolete trait usage from hydrated entities. |
| Ray.Di treated `ProviderInterface` in Aura router construction as provider-set injection after dependency upgrades | Added `CompatibleAuraRouter` with a concrete nullable header-provider dependency and rebound `primary_router`. |
| PHPStan needed more than the default CLI memory limit | Added `--memory-limit=1G` in the `sa` script. |
| Composer audit initially reported `doctrine/annotations` as abandoned | After aligning the app to the PHP 8.5 baseline and updating dependencies, `doctrine/annotations` was removed and `composer audit --no-dev` passes. |
| FakeQuery override initially leaked to real SQL in an app that already installed MediaQueryModule | Fixed in Ray.FakeQuery by binding `DbQueryInterceptor` to `FakeQueryInterceptor`, so existing pointcuts resolve to the fake interceptor. |
| Factory misconfiguration in Ray.FakeQuery was too forgiving | Added explicit `InvalidFactoryException`; missing/non-public factories no longer silently fall back to default hydration. |

## Resources
- GitHub issue: https://github.com/koriym/BEAR.AppKata/issues/1
- Local repo: `<repo-root>`
- Reference repo: `<path-to-MyVendor.Cms>`
- MyVendor.Cms BDR sample docs: `<path-to-MyVendor.Cms>/docs/media-query-samples.md`
- MyVendor.Cms BDR tests: `<path-to-MyVendor.Cms>/tests/Smoke/MediaQuerySamplesTest.php`
- Planned BEAR.AppKata reference-test ledger: `docs/reference-test-results.md`
- Ray.FakeQuery PR: https://github.com/ray-di/Ray.FakeQuery/pull/2
- Ray.FakeQuery DML metadata follow-up: https://github.com/ray-di/Ray.FakeQuery/issues/3
- Admin queries: `source/app/ddd/core/src/Infrastructure/Query`
- App resources: `source/app/src/Resource/App`
- Response schemas: `source/app/var/schema/response`

## Visual/Browser Findings
- No visual/browser findings for this planning pass.
