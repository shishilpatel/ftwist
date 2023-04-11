<?php

use Illuminate\Support\Facades\Route;
use Sawirricardo\Whatsapp\Laravel\Facades\Whatsapp;
use Sawirricardo\Whatsapp\Data\TextMessageData;
use App\Models;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    //return env('PHONE_NUMBER_ID');
    return view('welcome');
});
Route::get('/waba', [\App\Http\Controllers\WhatsAppController::class, 'index']);
Route::get('/webhook', [\App\Http\Controllers\WhatsAppController::class, 'webhookHandler']);
Route::post('/webhook', [\App\Http\Controllers\WhatsAppController::class, 'webhookHandler']);
Route::any('/read', [\App\Http\Controllers\WhatsAppController::class, 'read']);


/*Route::get('/test', function () {

    $product = new Models\Product();
    //dd($product);
    $row = 0;
    $headers = [];
    $filepath = "Book1.csv";
    $insert = array();
    $code = 123111;
    $sql = '';
    $imageName = '';
    if (($handle = fopen($filepath, "r")) !== FALSE) {

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (++$row == 1) {
                $headers = array_flip($data); // Get the column names from the header.
                continue;
            } else {

                $product->added_by = "admin";
                $product->user_id = 1;
                $product->name = $data[0];
                $product->slug = str_replace(" ", "-", $data[0]);
                $product->product_type = "physical";
                $product->category_ids = '[{"id":"17","position":1}]';
                $product->brand_id = 1;
                $product->unit = "kg";
                $product->min_qty = 1;
                $product->refundable = 1;
                $product->digital_product_type = null;
                $product->digital_file_ready = null;
                $product->images = '["' . str_replace(" ", "-", $data[0]) . '.jpg"]';
                $product->thumbnail = str_replace(" ", "-", $data[0]) . '.jpg';
                $product->featured = 1;
                $product->flash_deal = null;
                $product->video_provider = "youtube";;
                $product->video_url = null;
                $product->colors = "[]";
                $product->variant_product = 0;
                $product->attributes = null;
                $product->choice_options = "[]";
                $product->variation = "[]";
                $product->published = 0;
                $product->unit_price = $data[4];
                $product->purchase_price = $data[4];
                $product->tax = 5;
                $product->tax_type = "percent";
                $product->discount = 0;
                $product->discount_type = "flat";
                $product->current_stock = 10;
                $product->minimum_order_qty = 1;
                $product->details = $data[1];
                $product->free_shipping = 0;
                $product->attachment = null;
                $product->created_at = date("Y-m-d h:m:s");
                $product->updated_at = date("Y-m-d h:m:s");
                $product->status = 1;
                $product->featured_status = 1;
                $product->meta_title = $data[0];
                $product->details = $data[1];
                $product->meta_description = "";
                $product->meta_image = $data[5];
                $product->request_status = 1;
                $product->denied_note = null;
                $product->shipping_cost = 0;
                $product->multiply_qty = 1;
                $product->temp_shipping_cost = null;
                $product->is_shipping_cost_updated = null;
                $product->code = "";

                $path = storage_path('app/public/product/') . str_replace(" ", "-", $data[0]) . '.jpg';

                copy($data[5], $path);
                $product->save();
                dd($product);
            }

        }
        fclose($handle);
    }
});*/
