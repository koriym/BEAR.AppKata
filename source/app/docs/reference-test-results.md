# Reference Test Results

This ledger records the MyVendor.Cms reference checks used before adapting a
pattern into BEAR.AppKata.

| Date | Feature | MyVendor.Cms ref | BEAR.AppKata ref | Command | Result | Notes |
|------|---------|------------------|------------------|---------|--------|-------|
| 2026-05-15 03:56 JST | BDR MediaQuery result samples | `67a23106731501f3711dc2a79dd59cec43cad40f` with dirty cache-showcase working tree | `3ff77a90bef33b0ca6032f585a4ba34ff0c89106` plus modernization working tree | `cd /Users/akihito/git/MyVendor.Cms && zsh -ic 'sphp85; vendor/bin/phpunit tests/Smoke/MediaQuerySamplesTest.php tests/Smoke/FakeSqlQueryTest.php'` | PASS: 7 tests, 36 assertions | Verified `PagesInterface`, `PostQueryInterface` rowlist/result class, `AffectedRows`, and fake `execPostQuery` behavior before adapting the BDR sample path. |
| 2026-05-15 04:09 JST | Cache showcase | `67a23106731501f3711dc2a79dd59cec43cad40f` with dirty cache-showcase working tree | modernization working tree | `cd /Users/akihito/git/MyVendor.Cms && zsh -ic 'sphp85; vendor/bin/phpunit tests/Resource/App/Cache'` | FAIL: 14 tests, 58 assertions, 4 failures, 4 warnings | Recorded per policy and continued. `Cache\Author` leaf behavior passed, but `ArticleTags` surrogate-key assertions and `AuthorProfile` embed dependency assertions failed in the reference working tree. BEAR.AppKata therefore adapts the stable leaf invalidation path and keeps the embed parent as a documented/cacheable response showcase. |
| 2026-05-15 04:20 JST | Hypermedia workflow and HAL envelope tests | `67a23106731501f3711dc2a79dd59cec43cad40f` with dirty cache-showcase working tree | modernization working tree | `cd /Users/akihito/git/MyVendor.Cms && zsh -ic 'sphp85; vendor/bin/phpunit tests/Hypermedia'` | PASS: 13 tests, 20 assertions | Verified the story-oriented rel-following and HAL envelope reference suite. BEAR.AppKata adapts the pattern to Admin index/profile choreography. |
