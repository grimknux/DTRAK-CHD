<?php

if (!function_exists('getApiKey')) {
    function getApiConfig(string $system): array
    {
        
        $key = env("apiKey.$system");
        $url = env("apiUrl.$system");
        
        if (!$key || !$url) {
    
            throw new Exception ("Unknown system '$system' in getApiKey()");
        }

         return [
            'key' => $key,
            'url' => $url,
        ];
        
    }
}
