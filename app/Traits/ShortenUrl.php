<?php
namespace App\Traits;

use Illuminate\Support\Facades\Log;

class ShortenUrl {
    public function __construct(){ }

    public static function make($long_url){
        $longUrl = [
            'longUrl' => $long_url
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyDeJXYYD1u4bpLiKcDO7Y5XEgo_0uBG_yw",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($longUrl),
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            Log::info("cURL Error #: $err" );
            return null;
        } else {
            $data = json_decode($response, true);
            $url  = $data['id'];
            return $url;
        }
        
        return null;
    }
}