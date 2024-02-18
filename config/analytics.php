<?php

return [

    /*
     * The property id of which you want to display data.
     */
    'property_id' => env('ANALYTICS_PROPERTY_ID'),

    /*
     * Path to the client secret json file. Take a look at the README of this package
     * to learn how to get this file. You can also pass the credentials as an array
     * instead of a file path.
     */
//    'service_account_credentials_json' => storage_path('app/analytics/bbazaar-407717-5d77608a1e6f'),
    'service_account_credentials_json' => [
        "type" => "service_account",
        "project_id" => "bbazaar-407717",
        "private_key_id" => "5d77608a1e6f58bc0eedcb6b279dd181ca4632c0",
        "private_key" => "-----BEGIN PRIVATE KEY-----\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDE2IM1Sd1Gk9dk\nEaTDD8kDX9RF02UaON5//01E6FsAPlztZNpg4PGWNJu0QhLcRnopoabwmeFLbiJA\nnhb2zPMSx3KWFRfxgnIySDhFIkh71I3r2d/54zfdizhljKQYOm8unxaaq+qyu/4V\nhbHpVuDzfbGe1KxTfQ2JbVMPJ72cKI06qxwizoqRd5xDsOO9bN4RSTfIQodxEp0d\nYwV1KQ11eXkHWtxP1SLm9d+/bPvo+uicPfk/E04wCBviPCs3tx3EVkr9oOsFYb7w\ngq6vrUHUPRUxamyXdEjWdTBx4A/aeecM8S/miLXZBX53BdfZEEYqXMTxGvvEw+c9\nhTy8vUOnAgMBAAECggEAA2+ZKydz933Mzy5oU48XQWvEZ/KAZnHUdGssBGK7dT3o\nilcmtq4UtebgZuR56ludPQ0gWiCrtnRnvMOQVPDWjK0y5RyH7GfFG74iaxaCRiot\nQKt9MGlGa8AOFR8/8q3fU0gruHKoGe7pj9wOLADm0/dxFt9DIavwhSlMouiySNwY\nSR9iw4++m268BVrDFdQmIhASWxuZi4EoCFeWcTY5/ijBEvKvvNlSQzClFWsqBI0C\n83rn9w52EG4fV8IUK6O2VpJFZ+8Ugokk4nHLlQ22Geriz2FTaG1xr2EZVMKW/P9t\ne4JKmee3spCSlzxd1GS0W87uEgAhcXpW3WcfhSl37QKBgQDlwjPMQHOsu1ZbnSTC\nSDTNGRyhL4LSuIzB12NJ/vIyTofO2CE4B6i1pcYMG1XlQsBcg1vpf71NWvc/CgjF\nR/wP8RmDPxk7ItrPIK9JAitOKnK8DPyFW/1XdsLvSVVcKSUi02QXbM+gsAuUOIje\n0yLOv5HaKR8wAqGak61LrOq2BQKBgQDbU/zZ73HRFAewZl56qp1J7POQzNn3fEdy\n7UPsd167PQGgNJ/yCj7ziWqMAKIDKBffTFLphoXhDdD4+A7peXK53csKEE1tjklZ\n2snUnEwfCWM+SlsNl6X2ctQmAx/5eEWUCaleGpeEODhnYkqOZ7Cm4cuUDev7VqpV\nxahxARR2uwKBgFBsNe1c8iCYQpY231MSK0lPbPIOy2AvMTX4ysxv7R8m1erVRGt0\nYt2SdUxvlRV21BaN9HI7QGv/V2Q9guxrUhPSdPe38oD8plKyQijwFXB680ZX1Vxo\n4rjm3T0dEj/8Nv+FtxdsAxFgDe2IDIDSrvBnpgoTMtN/WEnfP12p3U5xAoGAVFF+\nlJ4sCHAF4LgKPGhZHbF9lb5bUXyyzykOAtuFTMBQXrG8h8WOm/vJPVeE8We5fbwO\n5/4ye+ne28gpm3J4O6Jby1unGKYp5uRo0VbhINFwGONnC9uwDwXoL1DmOvcU8Kui\nNC5O76LZdE1iUkrkq9OEez+66B5ST7U0Y3VB078CgYBFz2btCNRZu5aGai7ZrIh2\nMLWZZTv96mQ/Aw9MLtL418VIkams5LU+0XIhFXxoNM0Y6/jQB5Qso6XjJx74CNHI\n+WGbaIwRDfnWSmdgIkTmaOwtvTzTU1tNRHOTPYPl1GWq/awaG9BbIUsntsMtIznx\nZsVigoCM/LLKD6YrwTIeXA==\n-----END PRIVATE KEY-----\n",
        "client_email" => "bbazaar@bbazaar-407717.iam.gserviceaccount.com",
        "client_id" => "106573786898045704901",
        "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
        "token_uri" => "https://oauth2.googleapis.com/token",
        "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
        "client_x509_cert_url" => "https://www.googleapis.com/robot/v1/metadata/x509/bbazaar%40bbazaar-407717.iam.gserviceaccount.com",
        "universe_domain" => "googleapis.com"
    ],

    /*
     * The amount of minutes the Google API responses will be cached.
     * If you set this to zero, the responses won't be cached at all.
     */
    'cache_lifetime_in_minutes' => 60 * 24,

    /*
     * Here you may configure the "store" that the underlying Google_Client will
     * use to store it's data.  You may also add extra parameters that will
     * be passed on setCacheConfig (see docs for google-api-php-client).
     *
     * Optional parameters: "lifetime", "prefix"
     */
    'cache' => [
        'store' => 'file',
    ],
];
