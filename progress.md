# Progress Log

## Session: 2026-05-15

### Phase 1: Discovery and Plan Lock
- **Status:** complete
- **Started:** 2026-05-15 JST
- Actions taken:
  - Confirmed `origin` now points to `https://github.com/koriym/BEAR.AppKata.git`.
  - Created GitHub issue #1 for the modernization track.
  - Inspected current App Resource, Query, schema, and test surfaces.
  - Compared MyVendor.Cms Resource, Query, Result, schema, fake, and hypermedia test patterns.
  - Confirmed dependency gate for `#[Alps]`.
  - Created persistent planning files.
  - Published the rollout plan to GitHub issue #1.
  - Evaluated additional roadmap candidates: hypermedia tests, ALPS, ApiDoc/OpenAPI CI, bear/async, cache, and smoke tests.
  - Added accepted candidates to the plan with sequencing and dependency gates.
  - Evaluated MyVendor.Cms BDR samples for rowlist/result classes and affected-row metadata.
  - Promoted BDR result samples and MyVendor.Cms reference-test result recording into the implementation plan.
  - Ran MyVendor.Cms BDR reference tests: `MediaQuerySamplesTest.php` and `FakeSqlQueryTest.php` passed with 7 tests and 36 assertions.
  - Upgraded BEAR.AppKata dependencies for `#[Alps]`, OpenAPI generation, and Ray.MediaQuery result APIs.
  - Added Admin read contract, BDR result samples, smoke tests, hypermedia tests, ALPS profile, API doc CI, and reference-test ledger.
  - Added cache showcase resources/tests with in-memory cache override.
  - Added opt-in BEAR.Async support with `bear/async`, `bin/async.php`, and `composer async`.
  - Generated ApiDoc artifacts: `source/app/docs/index.html`, `source/app/docs/openapi.json`, and `source/app/docs/llms.txt`.
  - Final gates passed after implementation: `composer cs`, `composer sa`, `composer test`, and `composer doc`.
  - Posted completion notes and final gate results to GitHub issue #1: https://github.com/koriym/BEAR.AppKata/issues/1#issuecomment-4453997544
  - Investigated PR #2 CI failures and confirmed install-time failures were caused by workflow PHP 8.3 running against the PHP 8.5 dependency baseline.
  - Aligned `tests`, `compile`, and `reports` workflows to PHP 8.5, matching `apidoc`.
  - Restored `doctrine/annotations` as an explicit compatibility dependency for `ray/web-form-module` compile-time bindings.
  - Deferred `doctrine/annotations` removal; when it is prioritized, use `bearsunday/rector-bearsunday`.
  - Re-ran CI-equivalent local gates for tests, compile, coverage, and ApiDoc after the CI remediation.
  - Revisited the requested scope "through Phase 5" and strengthened the HAL envelope contract test to assert rendered `_links` JSON instead of only checking `#[Link]` attributes.
  - Confirmed PR #2 GitHub Actions were passing before starting annotation conversion.
  - Used `bearsunday/rector-bearsunday` as the conversion tool gate; the standard rules found no remaining supported annotations.
  - Replaced the remaining `@FormValidation()` usage with `#[FormValidation]` and an app-local Attribute-aware form module/interceptor.
  - Removed `doctrine/annotations` from Composer dependencies; `composer why doctrine/annotations` now reports it is not installed.
  - Evaluated the `Ray\Di\Types` / `Ray\Aop\Types` pattern and added a proposed Domain Type Alias Catalog follow-up to `task_plan.md`.
  - Reframed fake JSON as executable shared domain vocabulary and added a proposed Canonical Fake JSON Vocabulary follow-up to `task_plan.md`.
- Files created/modified:
  - `task_plan.md`
  - `findings.md`
  - `progress.md`

## Test Results
| Test | Input | Expected | Actual | Status |
|------|-------|----------|--------|--------|
| Not run | Planning only | No implementation tests required yet | Not run | pending |
| MyVendor.Cms BDR reference tests | `vendor/bin/phpunit tests/Smoke/MediaQuerySamplesTest.php tests/Smoke/FakeSqlQueryTest.php` | Reference tests pass | 7 tests, 36 assertions, OK | pass |
| MyVendor.Cms cache reference tests | `vendor/bin/phpunit tests/Resource/App/Cache` | Record result and continue on reference failure | 14 tests, 58 assertions, 4 failures, 4 warnings | recorded-fail |
| MyVendor.Cms hypermedia reference tests | `vendor/bin/phpunit tests/Hypermedia` | Reference tests pass | 13 tests, 20 assertions, OK | pass |
| BEAR.AppKata targeted modernized suite | `vendor/bin/phpunit ddd/core/tests/Infrastructure/Result tests/Resource/App/Admin tests/Hypermedia tests/Smoke tests/Resource/App/Cache` | Pass | 21 tests, 71 assertions, OK | pass |
| BEAR.AppKata async script syntax | `php -l bin/async.php` | Pass | No syntax errors detected | pass |
| BEAR.AppKata ApiDoc generation | `composer doc` | Generate html, openapi, llms | Generated `docs/index.html`, `openapi.json`, `llms.txt` | pass |
| BEAR.AppKata coding standard | `composer cs` | Pass | 67 files checked, OK | pass |
| BEAR.AppKata static analysis | `composer sa` | Pass | Psalm/PHPStan/PHPMD completed with exit code 0 | pass |
| BEAR.AppKata full test suite | `composer test` | Pass | 52 tests, 111 assertions, OK | pass |
| BEAR.AppKata CI tests script | `composer run-script tests` | Pass | 52 tests, 111 assertions, OK; Psalm/PHPStan/PHPMD exited 0 | pass |
| BEAR.AppKata compile script | `composer run-script compile` | Pass | prod HAL/API, HTML, and CLI compile completed; PHP 8.5 vendor deprecation warnings only | pass |
| BEAR.AppKata coverage script | `composer run-script pcov` | Pass | 52 tests, 111 assertions, OK; coverage generated | pass |
| BEAR.AppKata ApiDoc CI script | `composer doc` | Pass | Generated `docs/index.html`, `openapi.json`, `llms.txt` | pass |
| BEAR.AppKata production audit | `composer audit --no-dev` | No security advisories | No security vulnerability advisories found | pass |
| BEAR.AppKata Phase 5 hypermedia contracts | `vendor/bin/phpunit tests/Hypermedia tests/Resource/App/Admin` | Pass | 7 tests, 42 assertions, OK | pass |
| BEAR.AppKata annotation conversion compile | `composer run-script compile` | Pass without Doctrine annotations | prod HAL/API, HTML, and CLI compile completed; PHP 8.5 vendor deprecation warnings only | pass |
| BEAR.AppKata annotation conversion audit | `composer audit` | No security advisories | No security vulnerability advisories found | pass |
| BEAR.AppKata annotation conversion coding standard | `composer cs` | Pass | 68 files checked, OK | pass |
| BEAR.AppKata annotation conversion static analysis | `composer sa` | Pass | Psalm/PHPStan/PHPMD completed with exit code 0; vendor PHP 8.5 deprecation warnings only | pass |
| BEAR.AppKata annotation conversion full test suite | `composer test` | Pass | 68 tests, 166 assertions, OK | pass |
| BEAR.AppKata form validation attribute contract | `vendor/bin/phpunit tests/Resource/Page/FormValidationAttributeTest.php` | Pass | 15 tests, 30 assertions, OK | pass |

## Error Log
| Timestamp | Error | Attempt | Resolution |
|-----------|-------|---------|------------|
| 2026-05-15 JST | GitHub issues disabled for `koriym/BEAR.AppKata` | 1 | Enabled issues and created issue #1. |
| 2026-05-15 JST | PHPStan crashed under the default `128M` memory limit during `composer sa` | 1 | Added `--memory-limit=1G` to the PHPStan script. |
| 2026-05-15 JST | PHPMD rejected initial `AdminProfile` shape for public method count and short `id()` name | 1 | Reduced public API to response-oriented inquiries, renamed to `adminId()`, and kept internal helpers private. |
| 2026-05-15 JST | MyVendor.Cms cache reference tests failed in the reference working tree | 1 | Recorded the failure in `source/app/docs/reference-test-results.md` and adapted only the stable leaf invalidation pattern. |
| 2026-05-15 JST | GitHub Actions `tests`, `compile`, and `reports` failed during Composer install | 1 | Changed workflow PHP from 8.3 to 8.5 to match the app dependency baseline. |
| 2026-05-15 JST | `composer run-script compile` failed in `prod-html-app` with `Doctrine\Common\Annotations\Reader` not found | 1 | Added `doctrine/annotations` explicitly for the existing `ray/web-form-module` annotation-based form interceptor. |
| 2026-05-15 JST | `composer audit --no-dev` exits non-zero due to abandoned `doctrine/annotations` | 1 | Resolved by using `bearsunday/rector-bearsunday` as the migration gate, replacing `@FormValidation()` with app-local attributes/interceptors, and removing `doctrine/annotations`. |

## 5-Question Reboot Check
| Question | Answer |
|----------|--------|
| Where am I? | Final gate complete. |
| Where am I going? | Ready for review/commit/PR; follow-up read slices can reuse the Admin pattern. |
| What's the goal? | Modernize BEAR.AppKata read/API contracts while preserving DDD write workflows. |
| What have I learned? | See `findings.md`. |
| What have I done? | Created issue #1 and local planning files. |
