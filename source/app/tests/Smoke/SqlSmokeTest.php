<?php

declare(strict_types=1);

namespace MyVendor\MyProject\Smoke;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function array_keys;
use function array_unique;
use function array_values;
use function file_get_contents;
use function preg_match_all;
use function sort;

final class SqlSmokeTest extends TestCase
{
    private const string SQL_DIR = __DIR__ . '/../../var/sql';

    /** @param array<string, mixed> $params */
    #[DataProvider('sampleSqlProvider')]
    public function testSampleSqlParamsMatchPlaceholders(string $sqlPath, array $params): void
    {
        $actual = array_keys($params);
        sort($actual);

        $this->assertSame(
            $this->placeholderNames((string) file_get_contents(self::SQL_DIR . '/' . $sqlPath)),
            $actual,
            "{$sqlPath} params do not match SQL placeholders",
        );
    }

    /** @return iterable<string, array{0: string, 1: array<string, mixed>}> */
    public static function sampleSqlProvider(): iterable
    {
        $params = require __DIR__ . '/../params/sql_params.php';

        foreach ($params as $sqlPath => $values) {
            yield $sqlPath => [$sqlPath, $values];
        }
    }

    /** @return list<string> */
    private function placeholderNames(string $sql): array
    {
        preg_match_all('/:([A-Za-z_][A-Za-z0-9_]*)/', $sql, $matches);
        $names = $matches[1];
        sort($names);

        return array_values(array_unique($names));
    }
}
