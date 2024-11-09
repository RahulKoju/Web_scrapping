<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ScrapController extends Controller
{
    public function scrap()
    {
        $url = 'https://kalimatimarket.gov.np/';
        $res = Http::get($url);
        $res = $res->body();
        $productData = $this->extractProductData($res);
        return response()->json($productData);
    }

    protected function extractProductData($res)
    {
        $productData = [];

        $pattern = '/<tr>(.*?)<\/tr>/s';
        preg_match_all($pattern, $res, $matches);
        foreach ($matches[1] as $row) {
            $product = $this->parseProductHtml($row);
            if (!empty($product) && array_filter($product)) {
                $productData[] = $product;
            }
        }
        return $productData;
    }

    protected function parseProductHtml($row)
    {
        $prices = $this->extractPrices($row);

        $product = [
            'name' => $this->extractValue($row, '/class="dt-body-left">(.*?)<span/'),
            'min' => $prices[0] ?? '',
            'max' => $prices[1] ?? '',
            'avg' => $prices[2] ?? '',
        ];

        return $product;
    }

    protected function extractValue($row, $pattern)
    {
        if (preg_match($pattern, $row, $matches)) {
            return trim($matches[1]);
        }
    }

    protected function extractPrices($row)
    {
        $pattern = '/रू\s*([\d]+)/u';
        preg_match_all($pattern, $row, $matches);
        return $matches[1] ?? [];
    }
}
