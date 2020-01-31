<?php

namespace App\Helpers;

use Carbon\Carbon;

abstract class Functions
{

    /**
     * Creates date from input
     *
     * @param mixed $date Input
     * @param string $format Format
     * @return \DateTime
     */
    public static function createDateFromFormat($format, $date = null)
    {
        if (is_null($date)) return NULL;
        if (!is_scalar($date) && (get_class($date) == "DateTime" || get_class($date) == Carbon::class)) {
            return $date;
        }

        $d = \DateTime::createFromFormat($format, $date);
        if (!$d) {
            return NULL;
        }

        return $d;
    }


    /**
     * Combine translations.
     *
     * @param array $keys
     * @return array
     */
    public static function combineTrans(array $keys): array
    {
        $result = [];

        foreach ($keys as $key) {
            $trans = trans($key);
            $mergeWith = is_array($trans) ? $trans : [$key => $trans];
            $result = self::arrayDistinctMerge($result, $mergeWith);
        }

        return $result;
    }


    /**
     * Combine translations to JSON.
     *
     * @param array $keys
     * @return string
     */
    public static function combineTransToJson(array $keys): string
    {
        return json_encode(self::combineTrans($keys));
    }


    /**
     * Works same as array_merge_recursive, but it overrides values with duplicate
     * keys in the first array with the duplicate value in the second array, as array_merge does.
     *
     * Parameters are passed by reference, though only for performance reasons. They're not
     * altered by this function.
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function arrayDistinctMerge(array &$array1, array &$array2): array
    {
        $merge = $array1;

        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($merge[$key]) && is_array($merge[$key])) {
                $merge[$key] = self::arrayDistinctMerge($merge[$key], $value);
            } else {
                $merge[$key] = $value;
            }
        }

        return $merge;
    }


    /**
     * Normalize text for comparing while searching.
     *
     * @param string $text
     * @return string
     */
    public static function normalizeSearchText(string $text): string
    {
        $trim = trim($text);
        $stripTags = strip_tags($trim);
        $removeDiacritics = self::removeDiacritics($stripTags);
        return strtolower($removeDiacritics);
    }


    /**
     * Remove diacritics from given string.
     *
     * @param string $text
     * @return string
     */
    public static function removeDiacritics(string $text): string
    {
        return strtr($text, [
            'Š' => 'S', 'š' => 's', 'Ð' => 'Dj', 'Ž' => 'Z', 'ž' => 'z',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A',
            'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I',
            'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y',
            'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ě' => 'e', 'Ě' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o',
            'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'č' => 'c', 'Č' => 'C', 'ř' => 'r', 'Ř' => 'R',
            'ů' => 'u', 'Ů' => 'U', 'ň' => 'n', 'Ň' => 'N', 'ť' => 't', 'Ť' => 'T', 'ď' => 'd',
            'Ď' => 'D', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u',
            'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y',
        ]);
    }


    /**
     * Compare two dates.
     * @param \Carbon\Carbon|null $first
     * @param \Carbon\Carbon|null $second
     * @param bool $nullIsInf
     * @return int -1 - $first date is greater, 0 - are equal, 1 - $second date is greater
     */
    public static function compareDates(?Carbon $first, ?Carbon $second, bool $nullIsInf = false): int
    {
        if (is_null($first) && is_null($second)) {
            return 0;
        }

        if (is_null($first)) {
            return $nullIsInf ? 1 : -1;
        }

        if (is_null($second)) {
            return $nullIsInf ? -1 : 1;
        }

        if ($first->greaterThan($second)) {
            return 1;
        }

        if ($first->lessThan($second)) {
            return -1;
        }

        return 0;
    }


    /**
     * Sanitize HTML class name.
     *
     * @param string $class
     * @param string $fallback
     * @return string
     */
    public static function sanitizeHtmlClass(string $class, string $fallback = ''): string
    {
        $sanitized = preg_replace('/[^A-Za-z0-9_-]/', '', $class);

        if ($sanitized === '' && $fallback) {
            return self::sanitizeHtmlClass($fallback);
        }

        return $sanitized;
    }

    /**
     * @param array $input
     * @param string $keyName
     * @param string $labelName
     * @param string|null $childrenName
     * @return array
     */
    public static function associativeArrayToSequentialArray(
        array $input,
        string $keyName = 'id',
        string $labelName = 'label',
        string $childrenName = null
    ): array
    {
        $output = [];

        foreach ($input as $key => $value) {
            if ($childrenName !== null && is_array($value)) {
                $item = [
                    $labelName => $key,
                    $childrenName => self::associativeArrayToSequentialArray(
                        $value, $keyName, $labelName, $childrenName
                    )
                ];
            } else {
                $item = [
                    $keyName => $key,
                    $labelName => $value
                ];
            }

            $output[] = $item;
        }

        return $output;
    }
}
