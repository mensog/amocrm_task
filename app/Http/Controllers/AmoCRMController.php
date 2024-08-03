<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Exceptions\AmoCRMApiException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Models\ContactModel;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\CheckboxCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\CheckboxCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\CheckboxCustomFieldValueModel;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Models\LeadModel;

class AmoCRMController extends Controller
{
    private $apiClient;

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
        return view('amocrm_form');
    }

    public function auth()
    {
        $state = bin2hex(random_bytes(16));
        session(['oauth2state' => $state]);

        $authorizationUrl = $this->apiClient->getOAuthClient()->getOAuthProvider()->getAuthorizationUrl([
            'state' => $state,
            'mode' => 'post_message'
        ]);
        dd($authorizationUrl);
        return redirect()->away($authorizationUrl);
    }

    public function handleAmoCRMCb(Request $request)
    {
        $code = $request->get('code');
        $state = $request->get('state');
        
        if (!$code || !$state || $state !== session('oauth2state')) {
            Log::error('Invalid authorization state or missing authorization code', [
                'code' => $code,
                'state' => $state,
                'session_state' => session('oauth2state')
            ]);
            abort(400, 'Invalid authorization state or missing authorization code');
        }

        try {
            $accessToken = $this->apiClient->getOAuthClient()->getAccessTokenByCode($code, [
                'redirect_uri' => env('AMOCRM_REDIRECT_URI'),
            ]);
            $this->saveToken($accessToken);
            return redirect()->route('amocrm.form')->with('success', 'Authorization successful');
        } catch (AmoCRMoAuthApiException $e) {
            Log::error('Authorization error: ' . $e->getMessage(), [
                'code' => $code,
                'exception' => $e
            ]);
            return response()->json(['error' => 'Authorization error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('General error: ' . $e->getMessage(), [
                'code' => $code,
                'exception' => $e
            ]);
            return response()->json(['error' => 'General error: ' . $e->getMessage()], 500);
        }
    }

    private function saveToken(AccessTokenInterface $accessToken)
    {
        Cache::put('amocrm_access_token', $accessToken->getToken(), now()->addSeconds($accessToken->getExpires() - time()));
        Cache::put('amocrm_refresh_token', $accessToken->getRefreshToken(), now()->addDays(30));
        Cache::put('amocrm_expires', $accessToken->getExpires(), now()->addSeconds($accessToken->getExpires() - time()));
    }

    private function getAccessToken()
    {
        return new \League\OAuth2\Client\Token\AccessToken([
            'access_token' => Cache::get('amocrm_access_token'),
            'refresh_token' => Cache::get('amocrm_refresh_token'),
            'expires' => Cache::get('amocrm_expires'),
        ]);
    }

    public function submitForm(Request $request)
    {
        $validated = $request->validate([
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

        $this->apiClient->setAccessToken($this->getAccessToken());
        $this->apiClient->setAccountBaseDomain(env('AMOCRM_DOMAIN'));

        try {
            $contactsCollection = new ContactsCollection();
            $contact = new ContactModel();
            $contact->setName($validated['name'])
                ->setCustomFieldsValues(
                    (new CustomFieldsValuesCollection())
                        ->add(
                            (new MultitextCustomFieldValuesModel())
                                ->setFieldCode('EMAIL')
                                ->setValues(
                                    (new MultitextCustomFieldValueCollection())
                                        ->add((new MultitextCustomFieldValueModel())->setValue($validated['email']))
                                )
                        )
                        ->add(
                            (new MultitextCustomFieldValuesModel())
                                ->setFieldCode('PHONE')
                                ->setValues(
                                    (new MultitextCustomFieldValueCollection())
                                        ->add((new MultitextCustomFieldValueModel())->setValue($validated['phone']))
                                )
                        )
                );

            $contactsCollection->add($contact);
            $contact = $this->apiClient->contacts()->add($contactsCollection)->first();

            $leadsCollection = new LeadsCollection();
            $lead = new LeadModel();
            $lead->setName('New Lead')
                ->setPrice($validated['price'])
                ->setCustomFieldsValues(
                    (new CustomFieldsValuesCollection())
                        ->add(
                            (new CheckboxCustomFieldValuesModel())
                                ->setFieldId(env('AMOCRM_TIME_SPENT_FIELD_ID'))
                                ->setValues(
                                    (new CheckboxCustomFieldValueCollection())
                                        ->add((new CheckboxCustomFieldValueModel())->setValue($validated['time_spent']))
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
        } catch (AmoCRMApiException $e) {
            Log::error('AmoCRM API error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $validated
            ]);
            return response()->json(['error' => 'AmoCRM API error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('General error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $validated
            ]);
            return response()->json(['error' => 'General error: ' . $e->getMessage()], 500);
        }
    }

    public function leadAdd(Request $request)
    {
        Log::info($request);
    }

    public function contactAdd(Request $request)
    {
        Log::info($request);
    }
}
