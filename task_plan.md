# Task Plan: BEAR.AppKata Modernization

## Goal
Modernize BEAR.AppKata as a BEAR.Sunday reference kata by keeping DDD write workflows intact and adding BEAR-native read-side contracts with Resource, MediaQuery, HAL, JsonSchema, and focused tests.

## Current Phase
CI remediation after PR #2

## Non-Goals
- Do not replace DDD/Application/Domain with BDR.
- Do not move existing write workflows into QueryResult/read models.
- Do not touch CSRF/Form work in this modernization track.
- Do not build a full fake SQL layer for every table before the first read-side slice proves the pattern.
- Do not make `bear/async` part of the first Admin read slice; it requires a larger BEAR.Resource/runtime gate.

## Modernization Targets

### Target 1: Repository Identity and Docs
- Rename user-facing references from generic `bear-app` / `my-project` where appropriate.
- Document the architecture boundary: Page Resource for HTML workflow, App Resource for HAL/API contract.
- Add conventions for read model purity and write workflow ownership.

### Target 2: Dependency Gate
- Keep BEAR.Resource as-is initially because current `bear/resource 1.26.3` already supports `#[Link]` and `#[JsonSchema]`.
- Upgrade `bear/api-doc` before introducing `#[Alps]`; current `1.3.1` does not provide `BEAR\ApiDoc\Annotation\Alps`.
- Upgrade Ray.MediaQuery before introducing BDR result samples. Current `ray/media-query 0.17.1` has `#[Pager]` and `PagesInterface`, but does not provide `Ray\MediaQuery\Result\PostQueryInterface` / `AffectedRows`.
- Treat PostQueryInterface-style rowlist/result wrapping as a first-class BDR feature, not just an implementation detail.
- Treat `bear/async` as a later dependency/runtime spike. Packagist currently shows `bear/async 0.3.0`, requiring `bear/resource ^1.32`, while this app has `bear/resource 1.26.3`.

### Target 2A: MyVendor.Cms Reference Verification
- Run the relevant MyVendor.Cms reference tests before porting BDR features.
- Record the command, result, date, source commit, and BEAR.AppKata target commit in `docs/reference-test-results.md` so BEAR.AppKata shows that the referenced patterns were verified.
- Minimum reference checks:
  - `MediaQuerySamplesTest` for `PagesInterface`, rowlist/result class, and `AffectedRows`.
  - `Smoke/FakeSqlQueryTest` for fake `execPostQuery` behavior.
  - Hypermedia and cache tests when those tracks are adapted.
- If a MyVendor.Cms reference test fails, stop adapting that feature and record the failure before changing BEAR.AppKata.

### Target 3: Admin Read Side First Slice
- Add an Admin profile read model/QueryResult that composes `AdminEntity`, `AdminEmailEntity`, and `AdminPermissionEntity`.
- Expose pure query methods such as `primaryEmail()`, `verifiedEmails()`, `allowedPermissions()`, and `allowedResourceNames()`.
- Add an App Resource for Admin read API, for example `app://self/admin/profile{?id}`.
- Add `#[Link]` and `#[JsonSchema]` immediately; add `#[Alps]` after the dependency gate.
- Ensure response bodies never expose password hashes.
- Leave `CreateAdminUseCase`, join, verify, reset password, delete, email queue, and repository write paths unchanged.

### Target 4: Admin Collection and Pagination
- Add an admin list projection only after the item/profile contract is stable.
- Introduce `list` naming and `#[Pager] + PagesInterface` only where collection pagination is actually needed.
- Keep collection response shape consistent with MyVendor.Cms list resources: page metadata plus `items`.

### Target 5: JsonSchema and HAL Contract Coverage
- Add response schemas under `source/app/var/schema/response`.
- Add request schemas only for new App Resource write endpoints; existing Page Form workflows stay out of scope.
- Add HAL envelope tests that pin `_links` for choreography and `_embedded` for taxonomy only when `#[Embed]` is introduced.
- Use `$this->body += [...]` in embedded resources to preserve embed slots.

### Target 5A: Hypermedia Workflow Tests
- Convert the existing placeholder `tests/Hypermedia/WorkflowTest.php` into story-oriented tests.
- Use one hard-coded entry URI per story, then follow rels with `ResourceInterface::href()`.
- Keep per-resource field validation in JsonSchema/resource tests; hypermedia tests should validate transitions and invariants.
- Add separate HAL envelope contract tests for `_links` and `_embedded` shape.

### Target 5B: ALPS, ApiDoc, and OpenAPI
- Upgrade `bear/api-doc` before using `#[Alps]`.
- Add class/method `#[Alps]` descriptors only after the semantic names are stable.
- Add or update `var/alps/profile.json` as the semantic source used by docs.
- Update `apidoc.xml` from `html` only to `html,openapi,llms` after response/request schemas are meaningful.
- Add CI for ApiDoc/OpenAPI generation after docs generation is deterministic.

### Target 6: Hermetic Test Context
- Start with direct fake query implementations in resource tests for the Admin read slice.
- Add `FakePages` only when a paginated collection is introduced.
- Add `FakeSqlQuery` only after at least two App Resource tests need the same end-to-end fake MediaQuery stack.
- Keep DB integration tests separate from hermetic resource contract tests.

### Target 6A: Smoke Test Layers
- Add SQL smoke tests for placeholder coverage and basic prepare/execute.
- Add MediaQuery smoke tests that invoke every Query/Command method through DI with explicit sample args.
- Add smoke tests to `phpunit.xml.dist` as a separate `smoke` testsuite.
- Keep smoke tests shallow: callability/execution only, not business assertions.

### Target 6B: BDR MediaQuery Result Samples
- Add a rowlist/result class sample equivalent to MyVendor.Cms `ArticleSelectionQueryInterface` + `ArticleSelection`.
- Keep result classes under `ddd/core/src/Infrastructure/Result` or another explicit Query-result namespace, not Domain.
- Add an `AffectedRows` sample command equivalent to MyVendor.Cms `ArticleAffectedRowsCommandInterface`.
- Keep canonical workflow command interfaces returning their existing types; affected-row metadata samples should be separate reference interfaces.
- Add sample tests that prove:
  - paged rowlist returns `PagesInterface`,
  - typed rowlist/result class wraps hydrated rows and exposes pure inquiry methods,
  - `AffectedRows::count` and `isAffected()` work for update/delete/missing paths.
- Extend fake MediaQuery support only enough to test the sample path; avoid broad fake SQL before it is needed.

### Target 7: Subsequent Read Slices
- Apply the proven Admin profile pattern to User/account read side.
- Consider EmailQueue and verification-code read projections only if they are exposed as App Resource contracts.
- Keep command resources and batch workflows owned by Application/Domain services.

### Target 8: Cache Showcase
- Use BEAR.QueryRepository cache as a reference pattern after read Resource contracts are stable.
- Prefer `#[CacheableResponse]` on cache-specific App Resources over manual cache primitives.
- Add tests that assert ETag/conditional response behavior and that source code does not contain manual cache code.
- Use an in-memory cache override for hermetic tests.

### Target 9: Async Embed Showcase
- Add `bear/async` only after BEAR.Resource is upgraded enough to satisfy its dependency.
- Introduce async only for resources that already have stable `#[Embed]` contracts.
- Keep ext-parallel/ZTS and ext-swoole runtime setup out of normal test requirements.
- Add a demo command and optional CI/manual workflow, not a required default test, unless the runtime is available.

## Phases

### Phase 1: Discovery and Plan Lock
- [x] Confirm current App Resource surface is minimal.
- [x] Confirm response schema directory is empty.
- [x] Confirm MyVendor.Cms reference patterns.
- [x] Confirm `#[Alps]` dependency gap.
- [x] Publish this rollout plan to GitHub issue #1.
- **Status:** complete

### Phase 2: Dependency and Convention Baseline
- [ ] Update `bear/api-doc` enough to support `#[Alps]`, or defer `#[Alps]` in code and document why.
- [ ] Upgrade Ray.MediaQuery enough to support BDR rowlist/result class and `AffectedRows` samples, or record why it cannot be done in this slice.
- [ ] Add architecture/conventions documentation for read models, links, schema, and Page/App split.
- [ ] Document BDR result class placement, affected-row samples, hypermedia story-test rules, smoke-test layers, cache showcase rules, and async runtime constraints.
- [ ] Add `docs/reference-test-results.md` and record MyVendor.Cms reference-test results for the features being adapted.
- [ ] Run `zsh -ic 'sphp85; composer test'` after dependency changes.
- **Status:** complete

### Phase 3: Admin Profile Read Contract
- [ ] Add Admin profile read model/QueryResult.
- [ ] Add App Admin profile resource.
- [ ] Add Admin profile response JsonSchema.
- [ ] Add pure read model unit tests.
- [ ] Add resource contract tests for success and not-found.
- [ ] Verify existing admin write UseCase tests still pass.
- **Status:** complete

### Phase 4: Admin Collection Contract
- [ ] Add list query only if it is needed for an admin collection resource.
- [ ] Add paginated collection resource and schema.
- [ ] Add fake pager tests if `#[Pager]` is used.
- **Status:** not needed in this slice; Admin index/profile links and BDR rowlist sample cover the read-side collection reference without adding a workflow-owned collection endpoint.

### Phase 5: Hypermedia and Embed Contracts
- [ ] Add links between Admin profile and Admin collection.
- [ ] Add story-oriented hypermedia workflow tests.
- [ ] Add HAL envelope contract test.
- [ ] Introduce `#[Embed]` only for a real taxonomy relation and preserve embed slots with `$this->body += [...]`.
- **Status:** complete

### Phase 6: ALPS and ApiDoc/OpenAPI
- [ ] Upgrade `bear/api-doc` and add `#[Alps]` descriptors.
- [ ] Add or update `var/alps/profile.json`.
- [ ] Update `apidoc.xml` to generate `html,openapi,llms`.
- [ ] Add ApiDoc/OpenAPI CI.
- **Status:** complete

### Phase 7: BDR MediaQuery Result Samples
- [ ] Add rowlist/result class query interface and result object.
- [ ] Add affected-row sample command interface.
- [ ] Add sample SQL files if needed.
- [ ] Add sample tests covering PagesInterface, result class, and AffectedRows.
- [ ] Record MyVendor.Cms reference-test result alongside BEAR.AppKata test result.
- **Status:** complete

### Phase 8: Smoke Test Layers
- [ ] Add SQL smoke tests and parameter fixtures.
- [ ] Add MediaQuery smoke tests and query argument fixtures.
- [ ] Add `smoke` testsuite to `phpunit.xml.dist`.
- **Status:** complete

### Phase 9: Cache Showcase
- [ ] Add cache-specific App Resource examples after read contracts are stable.
- [ ] Add in-memory cache override module for tests.
- [ ] Add ETag/invalidation/source-code invariant tests.
- [ ] Record MyVendor.Cms cache reference-test result before adapting.
- **Status:** complete

### Phase 10: Async Embed Showcase
- [ ] Spike BEAR.Resource upgrade required by `bear/async`.
- [ ] Add `bear/async` and runtime-specific demo only after `#[Embed]` contracts are stable.
- [ ] Keep async checks optional/manual unless ext-parallel or ext-swoole is available in CI.
- **Status:** complete as opt-in runtime support: `bear/async` dependency, `bin/async.php`, and `composer async` are installed; ext-parallel/ZTS execution remains outside the default gate.

### Phase 11: Extend Pattern Beyond Admin
- [ ] Apply the pattern to User/account read side.
- [ ] Decide whether EmailQueue or verification-code projections need App Resource contracts.
- [ ] Keep write workflows under UseCase/Domain unless adding a deliberately simple CRUD reference.
- **Status:** deferred; the requested autonomous slice keeps the first reference pattern on Admin and documents the next read slices.

### Phase 12: Final Quality Gate
- [ ] Run `zsh -ic 'sphp85; composer cs'`.
- [ ] Run `zsh -ic 'sphp85; composer sa'`.
- [ ] Run `zsh -ic 'sphp85; composer test'`.
- [ ] Update issue #1 with completion notes and any deliberate deferrals.
- **Status:** complete

## Completion Summary
- Updated dependencies for `#[Alps]`, OpenAPI/llms ApiDoc generation, Ray.MediaQuery BDR result APIs, and opt-in `bear/async`.
- Added Admin read model/resource/schema/link/ALPS contract while leaving existing Application/Domain write workflows untouched.
- Added BDR result samples for typed rowlists and affected-row command metadata under Infrastructure.
- Replaced placeholder hypermedia coverage with story and HAL contract tests.
- Added SQL/MediaQuery smoke guardrails and a `smoke` PHPUnit suite.
- Added cache showcase resources/tests with in-memory cache override.
- Added ApiDoc/OpenAPI CI and generated `docs/index.html`, `docs/openapi.json`, and `docs/llms.txt`.
- Recorded MyVendor.Cms BDR and cache reference-test results in `source/app/docs/reference-test-results.md`.
- Final local implementation gates passed on 2026-05-15 JST: `composer cs`, `composer sa`, `composer test`, and `composer doc`.
- PR #2 CI remediation aligned GitHub Actions to PHP 8.5 and added explicit `doctrine/annotations` compatibility for the existing `ray/web-form-module` form interceptor.
- `doctrine/annotations` removal is deferred; when prioritized, use `bearsunday/rector-bearsunday`.

## Decisions Made
| Decision | Rationale |
|----------|-----------|
| Admin read side is the first implementation slice | It has existing Query interfaces, multiple related projections, and real value for demonstrating QueryResult purity without touching writes. |
| Keep write workflows in Application/Domain | Admin creation, verification, password reset, deletion, and email queue are workflow-heavy and already fit the DDD boundary. |
| Start with direct fake query implementations | This gives hermetic resource tests without prematurely building a broad FakeSqlQuery dispatch layer. |
| Promote Ray.MediaQuery 1.1-style BDR samples to the roadmap | AffectedRows and rowlist/result classes are part of the reference surface the user wants to teach and verify. |
| Treat `#[Alps]` as a dependency gate | Current `bear/api-doc 1.3.1` lacks the attribute class. |
| Add hypermedia tests to the roadmap | They prove rel choreography and prevent Resource contracts from becoming disconnected examples. |
| Add ApiDoc/OpenAPI CI after schema/ALPS stabilization | Docs generation should validate the contract once the semantic/source schemas are meaningful. |
| Add cache showcase after read contracts | Cache behavior needs stable Resource contracts and meaningful links/embeds to demonstrate dependency invalidation. |
| Add smoke tests as shallow guardrails | SQL and MediaQuery callability catches drift without duplicating resource/domain assertions. |
| Keep `bear/async` as a late showcase | It currently requires `bear/resource ^1.32` plus ext-parallel/ZTS or Swoole runtime concerns. |
| Record MyVendor.Cms reference-test results before adapting features | BEAR.AppKata should prove the upstream reference pattern is green before it ports the pattern. |

## Errors Encountered
| Error | Attempt | Resolution |
|-------|---------|------------|
| GitHub issues disabled on `koriym/BEAR.AppKata` | 1 | Enabled issues with `gh repo edit --enable-issues` before creating issue #1. |
| PHPStan crashed with PHP memory limit `128M` during `composer sa` | 1 | Added `--memory-limit=1G` to the PHPStan script. |
| PHPMD rejected the initial `AdminProfile` read model for too many public methods and short method names | 1 | Collapsed safe account data into `account()`, renamed `id()` to `adminId()`, and moved internal filtering helpers private. |
| GitHub Actions failed Composer install under PHP 8.3 after the PHP 8.5 dependency baseline | 1 | Aligned `tests`, `compile`, and `reports` workflows to PHP 8.5. |
| `composer run-script compile` failed in `prod-html-app` because `Doctrine\Common\Annotations\Reader` was not installed | 1 | Added `doctrine/annotations` explicitly for `ray/web-form-module` compatibility. |
| `composer audit --no-dev` exits non-zero because `doctrine/annotations` is abandoned | 1 | Deferred annotation removal by request; future removal should use `bearsunday/rector-bearsunday`. |
