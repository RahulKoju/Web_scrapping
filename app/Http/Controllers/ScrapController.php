<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ScrapController extends Controller
{
    public function scrap()
    {
        $url = 'https://kalimatimarket.gov.np/';
        $en = 'eyJpdiI6IlA0b1NQMUVHZ29vRldkak1JUFhqUUE9PSIsInZhbHVlIjoiQmFwMzlacFYvK0xoU1lqNmM0aCtUcDdOYzluckVVQW1qQXJBYTlHcWJwTGU2MElZTm9oUTlSMW1rT1JZblVpY2ZiOHdNNHRNR0Y3K2lJSnJ1MFFVZ0JrSk9jTld6V3dMcmo2WUdjdDFaM1djM3JyNkYzSkcyVjNnZUtrNWRsbTMiLCJtYWMiOiJhYzhiNTkyYTIwNTZhNjIxMDY5M2RlOTI5NmRjNGNiOWRjNmI4N2FkZWY5ZWVmMmNkODgzZGQ1ZDU2YmMyY2QxIiwidGFnIjoiIn0%3D; expires=Sun, 10-Nov-2024 09:08:04 GMT; Max-Age=3600; path=/; httponly; samesite=lax; secure';
        $res_np = Http::get($url);
        $res_en = Http::withCookies([
            'kalimati_fruits_and_vegetable_market_development_board_session' => $en
        ], 'kalimatimarket.gov.np')->get($url);
        $res_np = $res_np->body();
        $res_en = $res_en->body();
        $productDataNp = $this->extractProductData($res_np, 'np');
        $productDataEn = $this->extractProductData($res_en, 'en');
        $productData = [
            'EnglishData' => $productDataEn,
            'NepaliData' => $productDataNp
        ];
        return response()->json($productData);
    }

    protected function extractProductData($res, $lang)
    {
        $productData = [];

        $pattern = '/<tr>(.*?)<\/tr>/s';
        preg_match_all($pattern, $res, $matches);
        foreach ($matches[1] as $row) {
            $product = $this->parseProductHtml($row, $lang);
            if (!empty($product) && array_filter($product)) {
                $productData[] = $product;
            }
        }
        return $productData;
    }

    protected function parseProductHtml($row, $lang)
    {
        $prices = $this->extractPrices($row, $lang);

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

    protected function extractPrices($row, $lang)
    {
        if ($lang == 'en') {
            $pattern = '/Rs\s*([\d]+)/u';
        } else {
            $pattern = '/रू\s*([\d]+)/u';
        }
        preg_match_all($pattern, $row, $matches);
        return $matches[1] ?? [];
    }
}
