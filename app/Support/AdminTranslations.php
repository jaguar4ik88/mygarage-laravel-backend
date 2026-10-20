<?php

namespace App\Support;

class AdminTranslations
{
    /** @var array<int, string> */
    public const LOCALES = ['en', 'ru', 'uk', 'pl', 'pt', 'ro'];

    /**
     * Replace related translations, skipping rows whose text fields are all empty.
     *
     * @param  iterable<int, array<string, mixed>>  $rows
     * @param  array<int, string>  $fields
     */
    public static function persist($relation, iterable $rows, array $fields): void
    {
        $relation->delete();

        foreach ($rows as $row) {
            $locale = trim((string) ($row['locale'] ?? ''));
            if ($locale === '') {
                continue;
            }

            $payload = ['locale' => $locale];
            $hasText = false;
            foreach ($fields as $field) {
                $payload[$field] = $row[$field] ?? '';
                if (trim((string) $payload[$field]) !== '') {
                    $hasText = true;
                }
            }

            if (! $hasText) {
                continue;
            }

            $relation->create($payload);
        }
    }

    /** @return array<string, string> */
    public static function textRules(string $field, ?int $max = 255): array
    {
        $limit = $max ? '|max:'.$max : '';
        $rules = [
            'translations.*.locale' => 'required|string|max:5',
        ];

        foreach ([0, 1, 2] as $index) {
            $rules['translations.'.$index.'.'.$field] = 'required|string'.$limit;
        }
        foreach ([3, 4, 5] as $index) {
            $rules['translations.'.$index.'.'.$field] = 'nullable|string'.$limit;
        }

        return $rules;
    }
}
