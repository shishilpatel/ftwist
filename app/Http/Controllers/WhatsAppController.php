<?php

namespace App\Http\Controllers;

use App\Models\WhatsApp;
use Illuminate\Http\Request;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;
use Netflie\WhatsAppCloudApi\WebHook;
use stdClass;

class WhatsAppController extends Controller
{

    public $whatsapp_cloud_api;

    public function __construct()
    {
        $whatsapp_cloud_api = new WhatsAppCloudApi([
            'from_phone_number_id' => env("PHONE_NUMBER_ID"),
            'access_token' => env("ACCESS_TOKEN"),
        ]);
        //$whatsapp_cloud_api->sendTextMessage();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$result = $whatsapp_cloud_api->sendTextMessage('919081190819', 'Hey there! Im using WhatsApp Cloud API.');
        $result = $this->whatsapp_cloud_api->sendTextMessage('919081190819', 'hello_world');

        dd($result);
    }

    public function webhookHandler()
    {
        // Instantiate the WhatsAppCloudApi super class.
        $webhook = new WebHook();

        return $webhook->verify($_GET, env("MY_TOKEN"));
    }


    public function read()
    {
        $webhook = new WebHook();
        $recieved = new stdClass();
        //$sent = $webhook->read(json_decode('{"object":"whatsapp_business_account","entry":[{"id":"101223516234933","changes":[{"value":{"messaging_product":"whatsapp","metadata":{"display_phone_number":"919019290192","phone_number_id":"107064175639982"},"statuses":[{"id":"wamid.HBgMOTE5MDgxMTkwODE5FQIAERgSNTFFNDUzMERDMzJBODQzNDg0AA==","status":"sent","timestamp":"1677269115","recipient_id":"919081190819","conversation":{"id":"273a60dfe223db2a4f3880e33e4ab52c","expiration_timestamp":"1677355560","origin":{"type":"user_initiated"}},"pricing":{"billable":true,"pricing_model":"CBP","category":"user_initiated"}}]},"field":"messages"}]}]}', true));
        $recieved = $webhook->read(json_decode('{"object":"whatsapp_business_account","entry":[{"id":"101223516234933","changes":[{"value":{"messaging_product":"whatsapp","metadata":{"display_phone_number":"919019290192","phone_number_id":"107064175639982"},"contacts":[{"profile":{"name":"Kabir Singh"},"wa_id":"919081190819"}],"messages":[{"from":"919081190819","id":"wamid.HBgMOTE5MDgxMTkwODE5FQIAEhgUM0FCNDQzRTgwMjkxNEExQzVGOTkA","timestamp":"1679146567","text":{"body":"Hello"},"type":"text"}]},"field":"messages"}]}]}', true));

        dd($recieved);

        $whatsapp = new WhatsApp();

        $whatsapp->msg_id = $recieved->id();//
        $whatsapp->business_phone_id = $recieved->businessPhoneNumberId();//
        $whatsapp->business_phone_number = $recieved->businessPhoneNumber();//
        $whatsapp->customer_name = $recieved->customer()->name();
        $whatsapp->received_at = $recieved->receivedAt();//
        $whatsapp->message = $recieved->message();
        $whatsapp->customer_id = $recieved->customer()->phoneNumber();//

        /*$whatsapp->conversation_id = $sent->conversationId();
        $whatsapp->conversation_type = $sent->conversationType();
        $whatsapp->conversation_expires_at = $sent->conversationExpiresAt();
        $whatsapp->status_value = $sent->status();
        $whatsapp->error = $sent->errorCode();*/

        $whatsapp->save();

        dd($sent->status(), $recieved, $sent, $whatsapp);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
