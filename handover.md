# Handover: BEAR.AppKata Modernization

## Current State

- Repository: `https://github.com/koriym/BEAR.AppKata`
- Branch: `codex/bear-appkata-modernization`
- PR: `https://github.com/koriym/BEAR.AppKata/pull/2`
- Status: implementation complete for the current modernization scope.
- CI: passing for Compile, All tests, All Coverages, apidoc, and CodeRabbit.
- Working tree: clean at handover time.

## Related Ray.FakeQuery Work

- Repository: `https://github.com/ray-di/Ray.FakeQuery`
- Branch: `codex/fake-query-release-hardening`
- PR: `https://github.com/ray-di/Ray.FakeQuery/pull/2`
- Current head used by BEAR.AppKata: `77d6002`
- CI: passing for Quality and Unit PHP 8.2, 8.3, 8.4, and 8.5.
- Release policy: do not tag `ray/fake-query` yet. BEAR.AppKata intentionally uses the GitHub VCS branch alias for now.

BEAR.AppKata currently depends on Ray.FakeQuery through a Composer VCS repository and:

```json
"ray/fake-query": "dev-codex/fake-query-release-hardening as 1.0.x-dev"
```

When Ray.FakeQuery is eventually tagged, remove the VCS repository override and switch to:

```json
"ray/fake-query": "^1.0"
```

## Completed Scope

- Kept DDD/Application/Domain write workflows intact.
- Added BEAR-native Admin read-side Resource contracts.
- Added Admin read model / QueryResult style inquiry methods.
- Added HAL links, JsonSchema, ALPS descriptors, ApiDoc/OpenAPI/llms generation, and CI.
- Added story-oriented hypermedia tests and HAL envelope tests.
- Added BDR MediaQuery result samples for typed rowlist/result and affected-row metadata.
- Added smoke tests and a `smoke` PHPUnit suite.
- Added cache showcase and opt-in async support.
- Converted remaining app-local form validation annotation usage to attributes and removed `doctrine/annotations`.
- Replaced app-local Admin fake query data classes with Ray.FakeQuery-backed canonical JSON/JSONL fixtures.

## Important Decisions

- Do not replace DDD with BDR. The write side stays Application UseCase -> Domain -> Repository/Query/Command.
- Use BEAR.Resource / Ray.MediaQuery / HAL / JsonSchema primarily for read-side contracts.
- Treat fake JSON/JSONL as executable domain vocabulary, not throwaway mocks.
- Ray.FakeQuery should replace `Ray\MediaQuery\DbQueryInterceptor` in override contexts, not compete by priority.
- Keep Ray.FakeQuery 1.0 select-focused. DML metadata fake support is a follow-up, not a release blocker.

## Open Follow-Ups

- Ray.FakeQuery DML metadata fixtures:
  - `https://github.com/ray-di/Ray.FakeQuery/issues/3`
  - Scope: fixture-driven `AffectedRows` / `InsertedRow`, not fake PDO or mutable fake DB.
- Extend the Admin read pattern to User/account read side.
- Decide whether EmailQueue or verification-code projections need App Resource contracts.
- Optionally introduce app-local `Types.php` aliases only for repeated cross-boundary array shapes.
- When Ray.FakeQuery is tagged, switch BEAR.AppKata from the dev branch alias to a stable constraint.

## Verification Commands

Run from `source/app` in this repository:

```bash
composer cs
composer sa
composer test
composer doc
```

Targeted fake/Admin read verification:

```bash
vendor/bin/phpunit tests/Fake/FakeQueryAdminReadTest.php tests/Resource/App/Admin/ProfileTest.php tests/Hypermedia
```

Run from the Ray.FakeQuery repository root:

```bash
composer tests
composer crc
composer validate --strict
```

## Reference Files

- `task_plan.md`: full phased plan and completion state.
- `progress.md`: chronological progress, test results, and error log.
- `findings.md`: decisions, findings, and issue resolutions.
- `source/app/docs/reference-test-results.md`: MyVendor.Cms reference-test ledger.
