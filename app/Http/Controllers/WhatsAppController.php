<?php

namespace App\Http\Controllers;

use App\Models\WebhookRaw;
use App\Models\WhatsApp;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

/*WhatsApp Packages Start*/

use Netflie\WhatsAppCloudApi\Response\ResponseException;
use Netflie\WhatsAppCloudApi\WebHook;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;
use Netflie\WhatsAppCloudApi\Message\Template\Component;

/*WhatsApp Packages End*/

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Http;
use Netflie\WhatsAppCloudApi\Message\OptionsList\Row;
use Netflie\WhatsAppCloudApi\Message\OptionsList\Section;
use Netflie\WhatsAppCloudApi\Message\OptionsList\Action;
use Illuminate\Support\Facades\Storage;


class WhatsAppController extends Controller
{

    public WhatsAppCloudApi $whatsapp_cloud_api;
    public $recieved;
    public string $endpoint;
    public WebHook $webhook;

    public function __construct()
    {
        $this->whatsapp_cloud_api = new WhatsAppCloudApi([
            'from_phone_number_id' => env("PHONE_NUMBER_ID"),
            'access_token' => env("ACCESS_TOKEN"),
        ]);
        $this->recieved = '';
        $this->endpoint = "https://graph.facebook.com/v16.0/";
        $this->webhook = new WebHook();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendTextMessage();
        //$result = $whatsapp_cloud_api->sendTextMessage('919081190819', 'Hey there! Im using WhatsApp Cloud API.');
        //$result = $this->whatsapp_cloud_api->sendTextMessage('919081190819', 'hello_world');
        $result = $this->sendTemplate('919081190819', 'Kabir Singh', 'welcome_template', true);
        //$this->whatsapp_cloud_api->sendTemplate('919081190819', 'welcome_template', 'en_US');
    }

    /**
     * @throws ResponseException
     */
    public function webhookHandler(Request $request)
    {
        Log::debug('In Webhook Handler Function');
        // Instantiate the WhatsAppCloudApi super class.
        $webhook = new WebHook();
        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            echo $webhook->verify($_GET, env("MY_TOKEN"));
        } else {
            $data = json_decode(file_get_contents('php://input'), true);


            $this->recieved = $webhook->read($data);

            $this->whatsapp_cloud_api->markMessageAsRead($this->recieved->id());

            $webhookCall = new WebhookRaw();
            $webhookCall->payload = json_encode($data);

            $webhookCall->save();

            //$this->sendMessage($recieved->customer()->phoneNumber(), $recieved->customer()->name(), $re
            //$this->recieved->message());
            return response($data);
        }
    }

    function sendTemplate($mobile, $name, $template, $components = false)
    {
        if ($components) {
            $component_header = [
                [
                    'type' => 'text',
                    'text' => $name,
                ],
            ];

            $component_body = [

            ];

            $component_buttons = [
                [
                    'type' => 'button',
                    'sub_type' => 'quick_reply',
                    'index' => 0,
                    'parameters' => [
                        [
                            'type' => 'text',
                            'text' => 'Shop',
                        ]
                    ]
                ],
                [
                    'type' => 'button',
                    'sub_type' => 'quick_reply',
                    'index' => 1,
                    'parameters' => [
                        [
                            'type' => 'text',
                            'text' => 'Track Order',
                        ]
                    ]
                ],
                [
                    'type' => 'button',
                    'sub_type' => 'quick_reply',
                    'index' => 2,
                    'parameters' => [
                        [
                            'type' => 'text',
                            'text' => 'Support',
                        ]
                    ]
                ]
            ];

        }
        $component = new Component($component_header, $component_body, $component_buttons);

        try {
            $this->whatsapp_cloud_api->sendTemplate($mobile, $template, 'en_US', $component);
        } catch (ResponseException $exception) {
            echo "here";
            dd($exception->response()->decodedBody(), $component);
        }
    }

    public function sendTextMessage()
    {
        $webhookCall = new WebhookRaw();

        $record = array();
        /*
         * 10 = Text
         * 8 = Image
         * 9 = Video
         *
         * */
        $all = $webhookCall::select('payload')->get();
        foreach($all as $payL){
            $payload = $this->webhook->read(json_decode($payL->payload, true));
            $whatsapp = new WhatsApp();
            $messageType = $this->checkMessageTypeSupported($payload);

            $whatsapp->status = 'Received';
            $whatsapp->messaging_product = 'WhatsApp';
            $whatsapp->timestamp = $payload->receivedAt();
            $whatsapp->phone_number = $payload->customer()->phoneNumber();
            $whatsapp->name = $payload->customer()->name();
            if ($messageType === 'Text') {
                $whatsapp->wam_id = $payload->id();
                $whatsapp->body = $payload->message();
                $whatsapp->message_type = 'Text';
            } elseif ($messageType === 'Media') {
                $mediaID = $this->GetMediaID($payload);
                $url = $this->GetAttachmentUrl($mediaID);
                //$url = "https://lookaside.fbsbx.com/whatsapp_business/attachments/?mid=218034757576520&ext=1681585462&hash=ATulS-rNk8Kus3cDiej7iz2N-AvYnmG0OinIvRzUSkOsZg";
                $whatsapp->media_url = $this->GetAttachment($url, $this->getMediaExtension($payload));
                $whatsapp->message_type = 'Media';

            } elseif ($messageType === 'Unsupported') {

            }
        }

        $whatsapp->save();
        //dd($whatsapp, $payload, $message);

        //$json = json_decode($this->GetAttachmentUrl($type));
        //$json->url;
        //
    }

    public function GetAttachment($url, $extension)
    {
        try {
            $response = Http::withToken(env('ACCESS_TOKEN'))->get($url);
        } catch (RequestException $exception) {
            echo $exception->getMessage();
        }

        //dd($response);
        if ($response->ok()) {
            $binary = $response->getBody();
            Storage::disk("public")->put("/img/" . rand(1000000000, 2000000000) . "." . $extension, $binary);
            return url("storage/app/public/img/" . rand(1000000000, 2000000000) . "." . $extension);
        } else {
            return "https://google.com";
        }
    }

    public function GetMediaID($payload)
    {
        return $payload->imageId();
    }

    public function GetMediaExtension($payload): string
    {
        return explode('/', $payload->mimeType())[1];
    }

    public function GetAttachmentUrl($mediaId)
    {
        $url = "https://graph.facebook.com/v16.0/$mediaId?phone_number_id=" . env('PHONE_NUMBER_ID');
        try {
            //$response = Http::withToken(env('ACCESS_TOKEN'))->async()->get($url);
            $response = Http::withToken(env('ACCESS_TOKEN'))->get($url);
            //$response->tooManyRequests();
            sleep(60);
            return $response->body();
        } catch (RequestException $exception) {
            echo $exception->getMessage();
        }


    }

    public function checkMessageTypeSupported($payload)
    {
        if (class_basename($payload) === "Text") {
            return "Text";
        } elseif (class_basename($payload) === "Media") {
            return "Media";
        } elseif (class_basename($payload) === "Unsupported") {
            return "Unsupported";
        } else {
            return "something went wrong";
        }
    }

    public function read()
    {
        /*$webhook = new WebHook();
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

        /*$whatsapp->save();

        dd($sent->status(), $recieved, $sent, $whatsapp);*/

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
