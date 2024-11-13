<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Http\Request;

class ScrapController_V2 extends Controller
{
    public function scrape()
    {
        $url = 'https://kalimatimarket.gov.np/lang/en';
        $client = new Client();
        $res = $client->get($url);
        //return $res->getBody();
        $headers = $res->getHeaders();
        $cookies = $headers['set-cookie'][1];
        $cookies = explode(';', $cookies)[0];

        $cookieData = explode('=', $cookies);
        $jar = CookieJar::fromArray([
            $cookieData[0] => urldecode($cookieData[1])
        ], 'kalimatimarket.gov.np');

        $priceRes = $client->request('GET', 'https://kalimatimarket.gov.np/price', [
            'cookies' => $jar
        ]);

        $html = $priceRes->getBody()->getContents();
        return $html;

        // $ch = curl_init();
        // $url = "https://kalimatimarket.gov.np/lang/en";
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_HEADER, true);

        // $response = curl_exec($ch);
        // curl_close($ch);

        // $pattern = "/^Set-Cookie:\s*([^;]*)/mi";
        // preg_match_all($pattern, $response, $matches);

        // $cookie = $matches[1][1];
        // $context = stream_context_create([
        //     'http' => [
        //         'header' => "Cookie: $cookie\r\n"
        //     ]
        // ]);

        // $html = file_get_contents("https://kalimatimarket.gov.np/price", false, $context);
        // $pattern = "/<tr><td>(.*?)<\/td><td>(.*?)<\/td><td>(.*?)<\/td><td>(.*?)<\/td><td>(.*?)<\/td><\/tr>/s";

        // preg_match_all($pattern, $html, $matches);
        // array_shift($matches);
        // echo $cookie;
        // exit;
    }
}
