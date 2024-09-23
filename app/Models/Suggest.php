<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JeroenG\Explorer\Application\Explored;
use JeroenG\Explorer\Domain\Analysis\Analyzer\AnalyzerInterface;
use JeroenG\Explorer\Domain\Analysis\Filter\SynonymFilter;
use Laravel\Scout\Searchable;

class Suggest extends Model implements Explored
{
    use HasFactory, Searchable;
    public $timestamps = false;
    public $fillable = ['query', 'count', 'result_count'];
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'query' => [
                'input' => $this->query,
                'weight' => $this->count
            ],
            'results' => $this->results
        ];
    }
    public function searchableAs()
    {
        return 'suggests';
    }
    public function mappableAs(): array
    {
        return [
            "id" => [
                "type" => "keyword",
            ],
            "query" => [
                "type" => "completion",
                "analyzer" => "sug_tokenizer",

            ],
            "count" => [
                "type" => "integer"
            ]
        ];
    }
}
// class StandardAnalyzer implements AnalyzerInterface
// {
//     private string $name;

//     private string $tokenizer = 'standard';

//     private array $filters = [];

//     public function __construct(string $name)
//     {
//         $this->name = $name;
//     }

//     public function getName(): string
//     {
//         return $this->name;
//     }

//     public function setFilters(array $filters = []): void
//     {
//         $this->filters = $filters;
//     }

//     public function getFilters(): array
//     {
//         return array_map(function ($filter) {
//             if ($filter instanceof FilterInterface) {
//                 return $filter->getName();
//             }

//             return $filter;
//         }, $this->filters);
//     }

//     public function build(): array
//     {
//         return [
//             'tokenizer' => $this->tokenizer,
//             'filter' => $this->getFilters(),
//         ];
//     }
// }