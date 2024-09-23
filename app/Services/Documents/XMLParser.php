<?php

namespace App\Services\Documents;

use App\Enums\ShopStatus;
use App\Models\Shop;
use DiDom\Document;
use DiDom\Element;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Pharse;

use function Laravel\Prompts\form;

class XMLParser extends Parser
{
    protected Pharse $reader;
    protected string $file;



    public function parse($feeds): array
    {
        $products = [];
        foreach ($feeds as $feed) {
            $data = new Document();
            $data->loadXmlFile($feed['url'], true);
            $item_tag = Arr::get($feed, 'item', 'offer');
            $nodes = $data->find($item_tag);
            Log::info('nodes ' . count($nodes));
            $this->feeds[$feed['url']] = [];
            foreach ($nodes as $i => $node) {

                $node = $this->nodeToArray($node);
                $this->feeds[$feed['url']][] = $node;
                $offer_data = $this->parseOffer($node, $feed['mapping']);
                if ($this->validateOffer($offer_data)) {
                    $products[$offer_data['url']] = $offer_data;
                }
                if ($i == 0) {
                    Log::info($offer_data);
                }
            }
        }

        return $products;
    }

    private function nodeToArray(Element $node): array
    {
        $children = $node->children();
        $result = $this->getAttrValues($node);

        foreach ($children as $child) {
            if ($child->isCommentNode()) {
                continue;
            }
            $result[$child->tagName()] = $child->text();
            $result = array_merge($result, $this->getAttrValues($child));
        }
        return $result;
    }
    private function getAttrValues(Element $node): array
    {
        $attrs =  $node->attributes();
        $result = [];
        foreach ($attrs as $key => $value) {
            $result["attr_" . $key] = $value;
        }

        $tag = $node->tagName();
        $tagValue = $node->text();
        foreach ($attrs as $key => $value) {
            $key = $tag . '[' . $key . '="' . $value . '"]';
            $result[$key] = $tagValue;
        }
        return $result;
    }
}
