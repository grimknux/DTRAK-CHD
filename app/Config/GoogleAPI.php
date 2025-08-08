<?php

namespace Config;

class GoogleAPI extends \Config\App
{
    public $googleClientConfig = [
        'application_name' => 'ID Generator',
        'client_id' => '231583171443-pi0mgpuf6hqoc3j3ivljsj0m39gaf093.apps.googleusercontent.com',
        'client_secret' => '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',
        'redirect_uri' => 'http://localhost/idprint/',
        'scopes' => [
            \Google_Service_Sheets::SPREADSHEETS_READONLY,
        ],
    ];
}