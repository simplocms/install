<?php

namespace App\Traits;

/**
 * Trait FullTextSearch
 * @package App\Traits
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 *
 * @property array searchable
 */
trait FullTextSearchTrait
{
    /**
     * Replaces spaces with full text search wildcards.
     *
     * @param string $term
     * @return string
     */
    protected function getFullTextWildcards(string $term): string
    {
        // remove MySQL reserved symbols
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
        $term = str_replace($reservedSymbols, '', $term);

        // Searched words
        $words = explode(' ', $term);

        foreach ($words as $key => $word) {
            // Applying + operator (required word) on big words (smaller ones are not indexed by mysql)
            if (strlen($word) >= 3) {
                $words[$key] = '+' . $word . '*';
            }
        }

        $searchTerm = implode(' ', $words);
        return $searchTerm;
    }


    /**
     * Get wildcards for simple search.
     *
     * @param string $term
     * @return array
     */
    protected function getSimpleWildcards(string $term): array
    {
        // remove MySQL reserved symbols
        $term = preg_replace('%([_\%]+)%', '\\\\$1', $term);

        // Searched words
        $words = explode(' ', $term);

        return array_map(function (string $word) {
            return "%{$word}%";
        }, $words);
    }


    /**
     * Scope a query that matches a full text search of term.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $term
     */
    public function scopeSearch($query, string $term)
    {
        if (!strlen($term)) {
            return;
        }

        $columns = implode(',', $this->searchable);
        $searchFullText = $this->searchFullText ?? false;

        if ($searchFullText) {
            $query->whereRaw(
                "MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", $this->getFullTextWildcards($term)
            );
        } else {
            $query->where(function ($query) use ($term) {
                foreach ($this->getSimpleWildcards($term) as $word) {
                    foreach ($this->searchable as $column) {
                        $query->orWhere($column, 'LIKE', $word);
                    }
                }
            });
        }
    }
}
