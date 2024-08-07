<?php

return [
    'amocrm' => [
        'client_id' => env('AMOCRM_CLIENT_ID'),
        'client_secret' => env('AMOCRM_CLIENT_SECRET'),
        'redirect_uri' => env('AMOCRM_REDIRECT_URI'),
        'domain' => env('AMOCRM_DOMAIN'),
        'time_spent_field_id' => env('AMOCRM_TIME_SPENT_FIELD_ID'),
        'refresh_token' => env('AMOCRM_REFRESH_TOKEN'),
    ],
];
