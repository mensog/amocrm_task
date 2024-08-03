<?php

namespace App\Http\Controllers;

use AmoCRM\Client\AmoCRMApiClient;
use App\Models\Lead;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use League\OAuth2\Client\Token\AccessToken;
use Psy\CodeCleaner\FunctionContextPass;

class AmoAPIController extends Controller
{
    protected $clientId = "f5c743dd-ca53-4d20-a728-b7b83789a37e";
    protected $clientSecret = "emFbmh8gQYYrTFqVO7rxUsKq2u8j75BIQ6XCupA4X5G2SgrjBRyecgEGSJ1mPLAt";
    protected $redirectUri = "";

    protected $apiToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImI1NjRkZGFkMmIzMDhiMjg1OGNhYjdlMmQ3MDI5NWRhYzEzMWUwOTFmNGQ5YjdjZDlhZDYyZWIzNDNjNjljMzE0YTA2YzUzOThlMDJlYTQxIn0.eyJhdWQiOiJmNWM3NDNkZC1jYTUzLTRkMjAtYTcyOC1iN2I4Mzc4OWEzN2UiLCJqdGkiOiJiNTY0ZGRhZDJiMzA4YjI4NThjYWI3ZTJkNzAyOTVkYWMxMzFlMDkxZjRkOWI3Y2Q5YWQ2MmViMzQzYzY5YzMxNGEwNmM1Mzk4ZTAyZWE0MSIsImlhdCI6MTcyMjYyMjA4MSwibmJmIjoxNzIyNjIyMDgxLCJleHAiOjE3ODEzOTUyMDAsInN1YiI6IjExMzQ1MTM4IiwiZ3JhbnRfdHlwZSI6IiIsImFjY291bnRfaWQiOjMxODc5MzYyLCJiYXNlX2RvbWFpbiI6ImFtb2NybS5ydSIsInZlcnNpb24iOjIsInNjb3BlcyI6WyJjcm0iLCJmaWxlcyIsImZpbGVzX2RlbGV0ZSIsIm5vdGlmaWNhdGlvbnMiLCJwdXNoX25vdGlmaWNhdGlvbnMiXSwiaGFzaF91dWlkIjoiZTNhNGQ4ZjktMWU1Ni00MzI0LTliYmMtOTYzYTA1ZTZmODYzIn0.GWRawL6IpYTcWXb-Tzj_MNGcr_y5GaM-p-nV4NQQYQVQ1r_1MMVGYEqay5Ywj7Z2TfV90BL5HBbXCwsqRp4pkChL7CSHNcTPw_O8I9N42H9PDJk7E2hoxLT2ueKeU5gSs9l4CQziOv-VFkNz5eYdinX_yu3I2nR1dlRzu4HO3TU21EscjE2RBxs7rW3P_h-9HKGJ4cjnYdFXmeRNdFYp-pSCUKMUu_3p53r_nCuo8Os41GmZzvV7tqwg6rDALsCsn8lm1dALajHBWS3k0OSw5dprGAFm399mE7CKh0xjefTUEx17AIAAGWTbOxu1A1PWd_ZKkn-UqtmEo8YbBhydOw";

    public function index(Request $request)
    {
        return view('form');
    }


    public function formSubmit(Request $request)
    {
        dd($request);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'price' => 'required|numeric',
        ]);

        $lead = new Lead();
        $lead->name = $request->name;
        $lead->email = $request->email;
        $lead->phone = $request->phone;
        $lead->price = $request->price;
        $lead->spent_more_than_30s = $request->has('spent_more_than_30s') ? $request->spent_more_than_30s : false;
        $lead->save();

        $this->createLead($lead);
        
        dump($lead, $request);

        return redirect()->back()->with('success', 'Заявка успешно отправлена!');
    }

    public function createContact($name, $email, $phone)
    {
        return '';
    }


    public function createLead($lead)
    {
        $client = new Client();
        $response = $client->post('https://goyiyip384.amocrm.com/api/v4/leads', [
            'headers' => [
                'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImI1NjRkZGFkMmIzMDhiMjg1OGNhYjdlMmQ3MDI5NWRhYzEzMWUwOTFmNGQ5YjdjZDlhZDYyZWIzNDNjNjljMzE0YTA2YzUzOThlMDJlYTQxIn0.eyJhdWQiOiJmNWM3NDNkZC1jYTUzLTRkMjAtYTcyOC1iN2I4Mzc4OWEzN2UiLCJqdGkiOiJiNTY0ZGRhZDJiMzA4YjI4NThjYWI3ZTJkNzAyOTVkYWMxMzFlMDkxZjRkOWI3Y2Q5YWQ2MmViMzQzYzY5YzMxNGEwNmM1Mzk4ZTAyZWE0MSIsImlhdCI6MTcyMjYyMjA4MSwibmJmIjoxNzIyNjIyMDgxLCJleHAiOjE3ODEzOTUyMDAsInN1YiI6IjExMzQ1MTM4IiwiZ3JhbnRfdHlwZSI6IiIsImFjY291bnRfaWQiOjMxODc5MzYyLCJiYXNlX2RvbWFpbiI6ImFtb2NybS5ydSIsInZlcnNpb24iOjIsInNjb3BlcyI6WyJjcm0iLCJmaWxlcyIsImZpbGVzX2RlbGV0ZSIsIm5vdGlmaWNhdGlvbnMiLCJwdXNoX25vdGlmaWNhdGlvbnMiXSwiaGFzaF91dWlkIjoiZTNhNGQ4ZjktMWU1Ni00MzI0LTliYmMtOTYzYTA1ZTZmODYzIn0.GWRawL6IpYTcWXb-Tzj_MNGcr_y5GaM-p-nV4NQQYQVQ1r_1MMVGYEqay5Ywj7Z2TfV90BL5HBbXCwsqRp4pkChL7CSHNcTPw_O8I9N42H9PDJk7E2hoxLT2ueKeU5gSs9l4CQziOv-VFkNz5eYdinX_yu3I2nR1dlRzu4HO3TU21EscjE2RBxs7rW3P_h-9HKGJ4cjnYdFXmeRNdFYp-pSCUKMUu_3p53r_nCuo8Os41GmZzvV7tqwg6rDALsCsn8lm1dALajHBWS3k0OSw5dprGAFm399mE7CKh0xjefTUEx17AIAAGWTbOxu1A1PWd_ZKkn-UqtmEo8YbBhydOw',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'name' => $lead->name,
                'contacts' => [
                    [
                        'name' => $lead->name,
                        'custom_fields_values' => [
                            [
                                'field_id' => 'EMAIL_FIELD_ID',
                                'values' => [
                                    ['value' => $lead->email]
                                ]
                            ],
                            [
                                'field_id' => 'PHONE_FIELD_ID',
                                'values' => [
                                    ['value' => $lead->phone]
                                ]
                            ],
                        ]
                    ]
                ],
                'price' => $lead->price,
                'custom_fields_values' => [
                    [
                        'field_id' => 'SPENT_MORE_THAN_30S_FIELD_ID',
                        'values' => [
                            ['value' => $lead->spent_more_than_30s]
                        ]
                    ]
                ]
            ]
        ]);

        dump($client, $response);
    }


    public function leadAdd(Request $request)
    {
        Log::info($request);
    }

    public function contactAdd(Request $request)
    {
        Log::info($request);
    }

    
    public function getAllLeads(Request $request)
    {
        $apiClient = new AmoCRMApiClient($this->clientId, $this->clientSecret, $this->redirectUri);
        dd($apiClient->getOAuthClient());
    }
}
