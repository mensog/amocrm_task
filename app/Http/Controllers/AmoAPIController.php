<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\CheckboxCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\CheckboxCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\CheckboxCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use League\OAuth2\Client\Token\AccessTokenInterface;

class AmoAPIController extends Controller
{
    private $apiClient;
    protected $clientId = "f5c743dd-ca53-4d20-a728-b7b83789a37e";
    protected $clientSecret = "emFbmh8gQYYrTFqVO7rxUsKq2u8j75BIQ6XCupA4X5G2SgrjBRyecgEGSJ1mPLAt";
    private $redirectUri = '';

    protected $apiToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImI1NjRkZGFkMmIzMDhiMjg1OGNhYjdlMmQ3MDI5NWRhYzEzMWUwOTFmNGQ5YjdjZDlhZDYyZWIzNDNjNjljMzE0YTA2YzUzOThlMDJlYTQxIn0.eyJhdWQiOiJmNWM3NDNkZC1jYTUzLTRkMjAtYTcyOC1iN2I4Mzc4OWEzN2UiLCJqdGkiOiJiNTY0ZGRhZDJiMzA4YjI4NThjYWI3ZTJkNzAyOTVkYWMxMzFlMDkxZjRkOWI3Y2Q5YWQ2MmViMzQzYzY5YzMxNGEwNmM1Mzk4ZTAyZWE0MSIsImlhdCI6MTcyMjYyMjA4MSwibmJmIjoxNzIyNjIyMDgxLCJleHAiOjE3ODEzOTUyMDAsInN1YiI6IjExMzQ1MTM4IiwiZ3JhbnRfdHlwZSI6IiIsImFjY291bnRfaWQiOjMxODc5MzYyLCJiYXNlX2RvbWFpbiI6ImFtb2NybS5ydSIsInZlcnNpb24iOjIsInNjb3BlcyI6WyJjcm0iLCJmaWxlcyIsImZpbGVzX2RlbGV0ZSIsIm5vdGlmaWNhdGlvbnMiLCJwdXNoX25vdGlmaWNhdGlvbnMiXSwiaGFzaF91dWlkIjoiZTNhNGQ4ZjktMWU1Ni00MzI0LTliYmMtOTYzYTA1ZTZmODYzIn0.GWRawL6IpYTcWXb-Tzj_MNGcr_y5GaM-p-nV4NQQYQVQ1r_1MMVGYEqay5Ywj7Z2TfV90BL5HBbXCwsqRp4pkChL7CSHNcTPw_O8I9N42H9PDJk7E2hoxLT2ueKeU5gSs9l4CQziOv-VFkNz5eYdinX_yu3I2nR1dlRzu4HO3TU21EscjE2RBxs7rW3P_h-9HKGJ4cjnYdFXmeRNdFYp-pSCUKMUu_3p53r_nCuo8Os41GmZzvV7tqwg6rDALsCsn8lm1dALajHBWS3k0OSw5dprGAFm399mE7CKh0xjefTUEx17AIAAGWTbOxu1A1PWd_ZKkn-UqtmEo8YbBhydOw";

    public function __construct()
    {
        $this->apiClient = new AmoCRMApiClient(
            env('AMOCRM_CLIENT_ID'),
            env('AMOCRM_CLIENT_SECRET'),
            env('AMOCRM_REDIRECT_URI')
        );
    }

    public function showForm()
    {
        return view('form');
    }

    public function authorizeAMO()
    {
        $state = bin2hex(random_bytes(16));
        session(['oauth2state' => $state]);

        $authorizationUrl = $this->apiClient->getOAuthClient()->getOAuthProvider()->getAuthorizationUrl([
            'state' => $state,
            'mode' => 'post_message'
        ]);

        return redirect()->away($authorizationUrl);
    }

    public function handleAmoCRMCb(Request $request)
    {
        $code = $request->get('code');

        if (!$code) {
            abort(400, 'No authorization code provided');
        }

        try {
            $accessToken = $this->apiClient->getOAuthClient()->getAccessTokenByCode($code);
            $this->saveToken($accessToken);
            return redirect()->route('amocrm.form')->with('success', 'Authorization successful');
        } catch (AmoCRMoAuthApiException $e) {
            abort(500, 'Authorization error');
        }
    }

    private function saveToken(AccessTokenInterface $accessToken)
    {
        // Сохранение токенов в хранилище
        Cache::put('amocrm_access_token', $accessToken->getToken(), $accessToken->getExpires());
        Cache::put('amocrm_refresh_token', $accessToken->getRefreshToken(), 30 * 24 * 60 * 60);
    }

    public function submitForm(Request $request)
    {
        // Validate form data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'price' => 'required|numeric',
            'time_spent' => 'required|boolean'
        ]);

        $accessToken = Cache::get('amocrm_access_token');
        if (!$accessToken) {
            return redirect()->route('amocrm.authorize');
        }

        $this->apiClient->setAccessToken($accessToken)
            ->setAccountBaseDomain(env('AMOCRM_DOMAIN'));

        $contactsCollection = new ContactsCollection();
        $contact = new ContactModel();
        $contact->setName($request->name)
            ->setFirstName($request->name)
            ->setCustomFieldsValues(
                (new CustomFieldsValuesCollection())
                    ->add(
                        (new MultitextCustomFieldValuesModel())
                            ->setFieldCode('EMAIL')
                            ->setValues(
                                (new MultitextCustomFieldValueCollection())
                                    ->add((new MultitextCustomFieldValueModel())->setValue($request->email))
                            )
                    )
                    ->add(
                        (new MultitextCustomFieldValuesModel())
                            ->setFieldCode('PHONE')
                            ->setValues(
                                (new MultitextCustomFieldValueCollection())
                                    ->add((new MultitextCustomFieldValueModel())->setValue($request->phone))
                            )
                    )
            );

        $contactsCollection->add($contact);
        $contact = $this->apiClient->contacts()->add($contactsCollection)->first();

        // Create lead
        $leadsCollection = new LeadsCollection();
        $lead = new LeadModel();
        $lead->setName('New Lead')
            ->setPrice($request->price)
            ->setCustomFieldsValues(
                (new CustomFieldsValuesCollection())
                    ->add(
                        (new CheckboxCustomFieldValuesModel())
                            ->setFieldId(env('AMOCRM_TIME_SPENT_FIELD_ID'))
                            ->setValues(
                                (new CheckboxCustomFieldValueCollection())
                                    ->add((new CheckboxCustomFieldValueModel())->setValue($request->time_spent))
                            )
                    )
            )
            ->setContacts(
                (new ContactsCollection())
                    ->add($contact)
            );

        $leadsCollection->add($lead);
        $lead = $this->apiClient->leads()->add($leadsCollection)->first();

        return redirect()->back()->with('success', 'Заявка успешно отправлена в AmoCRM');
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
