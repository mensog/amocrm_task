<?php

namespace App\Http\Controllers;

use AmoCRM\Client\AmoCRMApiClient;
use Illuminate\Http\Request;

class AmoAPIController extends Controller
{
    public function index(Request $request)
    {
        return view('form');
    }


    public function data_submit(Request $request)
    {
        return '';
    }

    public function getAllLeads(Request $request)
    {
        $clientId = "f5c743dd-ca53-4d20-a728-b7b83789a37e";
        $clientSecret = "emFbmh8gQYYrTFqVO7rxUsKq2u8j75BIQ6XCupA4X5G2SgrjBRyecgEGSJ1mPLAt";
        $redirectUri = "";

        $apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);

        dd($apiClient->getOAuthClient());
    }
}
