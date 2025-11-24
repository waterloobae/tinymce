<?php

return [

    /*
    |--------------------------------------------------------------------------
    | TinyMCE API Key
    |--------------------------------------------------------------------------
    |
    | Your TinyMCE API key is required for TinyMCE 8.x to function properly.
    | You can obtain a free API key from https://www.tiny.cloud/
    |
    | For open-source projects, TinyMCE offers free API keys.
    | Set your API key in your .env file as TINYMCE_API_KEY
    |
    */

    'api_key' => env('TINYMCE_API_KEY', 'no-api-key'),

];
