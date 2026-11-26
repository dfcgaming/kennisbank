<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestEnvironmentSeeder extends Seeder
{
    private const PEOPLE = 1_500;

    private const ASSETS = 3_000;

    private const ARTICLES = 2_500;

    public function run(): void
    {
        if (! app()->environment('test')) {
            return;
        }

        $now = now();

        DB::transaction(function () use ($now): void {
            $this->fill('people', self::PEOPLE, fn (int $n): array => [
                'name' => sprintf('Test Person %05d', $n),
                'email' => sprintf('test.person.%05d@example.invalid', $n),
                'department' => ['IT', 'Finance', 'Operations', 'People'][($n - 1) % 4],
                'created_at' => $now,
                'updated_at' => $now,
            ], 'email', 'test.person.%');

            $people = DB::table('people')
                ->where('email', 'like', 'test.person.%@example.invalid')
                ->orderBy('id')->pluck('id')->all();

            $this->fill('assets', self::ASSETS, fn (int $n): array => [
                'name' => sprintf('%s %05d', ['Laptop', 'Mobile phone', 'Display', 'Mouse', 'Keyboard', 'Server'][$n % 6], $n),
                'type' => ['laptop', 'personal_equipment', 'personal_equipment', 'personal_equipment', 'personal_equipment', 'server'][$n % 6],
                'equipment_kind' => ['laptop', 'phone', 'screen', 'mouse', 'keyboard', null][$n % 6],
                'asset_tag' => sprintf('TEST-%05d', $n),
                'serial_number' => sprintf('SN-TEST-%08d', $n),
                'manufacturer' => ['Dell', 'Lenovo', 'HP', 'Apple', 'Logitech'][$n % 5],
                'model' => ['Latitude 5450', 'ThinkPad T14', 'ProBook 440', 'iPhone 16', 'MX Master'][$n % 5],
                'location' => ['Amsterdam', 'Utrecht', 'Rotterdam', 'Remote'][$n % 4],
                'hostname' => $n % 6 === 0 ? sprintf('test-srv-%04d', $n) : null,
                'ip_address' => $n % 6 === 0 ? sprintf('10.%d.%d.%d', 16 + ($n % 200), intdiv($n, 250) % 250, 1 + ($n % 253)) : null,
                'person_id' => $n % 6 === 0 ? null : ($people[($n - 1) % max(count($people), 1)] ?? null),
                'created_at' => $now,
                'updated_at' => $now,
            ], 'asset_tag', 'TEST-%');

            $assetIds = DB::table('assets')->where('asset_tag', 'like', 'TEST-%')->orderBy('id')->pluck('id')->all();

            $this->fill('knowledge_articles', self::ARTICLES, fn (int $n): array => [
                'title' => sprintf('Test guide %05d — %s', $n, ['Device setup', 'Troubleshooting', 'Security', 'Access request', 'Maintenance'][$n % 5]),
                'body' => "Synthetic test documentation #{$n}.\n\nUse this sample article to exercise search, pagination, relations, and detail pages in the test environment. No real user or system information is included.\n\nChecklist:\n- Confirm the example device is connected.\n- Record the result in this sample guide.\n- Escalate to the IT team if the simulated issue persists.",
                'created_at' => $now,
                'updated_at' => $now,
            ], 'title', 'Test guide %');

            $articleIds = DB::table('knowledge_articles')->where('title', 'like', 'Test guide %')->orderBy('id')->pluck('id')->all();

            $assetArticleLinks = [];
            foreach ($assetIds as $index => $assetId) {
                foreach (array_slice($articleIds, $index % max(count($articleIds), 1), 2) as $articleId) {
                    $assetArticleLinks[] = ['asset_id' => $assetId, 'knowledge_article_id' => $articleId];
                }
            }
            foreach (array_chunk($assetArticleLinks, 500) as $chunk) {
                DB::table('asset_knowledge_article')->insertOrIgnore($chunk);
            }

            $personArticleLinks = [];
            foreach ($people as $index => $personId) {
                foreach (array_slice($articleIds, $index % max(count($articleIds), 1), 2) as $articleId) {
                    $personArticleLinks[] = ['person_id' => $personId, 'knowledge_article_id' => $articleId];
                }
            }
            foreach (array_chunk($personArticleLinks, 500) as $chunk) {
                DB::table('knowledge_article_person')->insertOrIgnore($chunk);
            }
        });
    }

    /** @param callable(int): array<string, mixed> $makeRecord */
    private function fill(string $table, int $total, callable $makeRecord, string $uniqueColumn, string $prefix): void
    {
        $existing = array_fill_keys(
            DB::table($table)->where($uniqueColumn, 'like', $prefix)->pluck($uniqueColumn)->all(),
            true,
        );

        $rows = [];
        for ($number = 1; $number <= $total; $number++) {
            $record = $makeRecord($number);
            if (isset($existing[$record[$uniqueColumn]])) {
                continue;
            }

            $rows[] = $record;
            if (count($rows) === 500) {
                DB::table($table)->insertOrIgnore($rows);
                $rows = [];
            }
        }

        if ($rows !== []) {
            DB::table($table)->insertOrIgnore($rows);
        }
    }
}
