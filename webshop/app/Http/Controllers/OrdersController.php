<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\OrderProducts;
use App\Models\Orders;
use App\Models\Products;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class OrdersController extends Controller
{
    public function index()
    {
        return Orders::all();
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'customer_email'  => 'required',
            'products' => 'required|array',
        ]);

        try{

            $customer = Customers::select('id')->where('email', '=', $request->customer_email)->first();
            // dd($customer);
            if(!empty($customer)){

            $order = new Orders;
            $order->customer_id = $customer->id;

            $order->save();

            $products = Products::select('id','price')->whereIn('product_name', $request->products)->get();
            $amount = 0;
            foreach($products as $product) {
            $new_product= new OrderProducts;
            $new_product->product_id = $product->id;
            $new_product->order_id = $order->id;
            $new_product->save();

            $amount += $product->price;
            }
            $order->amount = $amount;
            $order->save();
            $products = OrderProducts::with('products')->select('product_id')->where('order_id', $order->id)->get();
            return response()->json(['message'=>"Order successfully created",'order_id' => $order->id,"products" =>$products], 200);
            }
            else{
                return response()->json(['message' => 'Customer not found'], 404);
            }

        }
        catch(Exception $e){
              Log::info("Order creation error: " . $e->getMessage());
        }

        
    }

    public function show($id)
    {
            $order =Orders::with('customer','products')->select('id','customer_id','amount')->where('id',$id)->first();
            if(empty($order)){
                return response()->json(['message' => 'No order found'], 404);
            }
            return response()->json(['order' => $order], 200);
    }

    public function destroy($id)
    {
        $order = Orders::find($id);
        if(!empty($order)){
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully']);
        }
        else{
            return response()->json(['message' => 'No order found'], 404);
        }
    }

    public function attachProduct(Request $request,$id){

        $this->validate($request, [
            'products' => 'required|array',
        ]);

        try{
        $order = Orders::find($id);
        if(!empty($order)){

            if($order->payment_status == 1){
                return response()->json(['message' => 'Order already paid'], 404);
            }
            $products = Products::select('id','price')->whereIn('product_name', $request->products)->get();
            $amount = $order->amount;
            foreach($products as $product) {
            $new_product= new OrderProducts;
            $new_product->product_id = $product->id;
            $new_product->order_id = $order->id;
            $new_product->save();

            $amount += $product->price;
            }
            $order->amount = $amount;
            $order->save();
            return response()->json(['message' => 'Order sucessfully updated'], 200);

        }
        else{
            return response()->json(['message' => 'No order found'], 404);
        }

    }
    catch(Exception $e){
          Log::info("Order Updation error: " . $e->getMessage());
    }

    }

    public function payOrder($id){

        try{
            $order = Orders::find($id);
                if(!empty($order)){

                    $data = [
                        "order_id" => $order->id,
                        "customer_email"=> $order->customer->email,
                        "value"=> $order->amount
                    ];
                    $url ="https://superpay.view.agentur-loop.com/pay";
                    $response = $this->callCurl($url,$data);
                    Log::info("Paymemt Response: ",["data" => $data,"Response" => $response]);
                    if(!empty($response['message'])){
                        if($response['message'] == "Payment Successful"){
                            $order->payment_status = 1;
                            $order->save();
                            return response()->json(['message' => 'Payment Successfully Paid'], 200);
                        }
                        else{
                            return response()->json(['message' => $response['message']], 200);

                        }
                    }
                    else{
                        return response()->json(['message' => 'Payment error'], 404);
                    }
                }
                else{
                    return response()->json(['message' => 'No order found'], 404);
                }
            }
            catch(Exception $e){
                Log::info("Order Updation error: " . $e->getMessage());
          }
    }

    public function callCurl($url,$data) {

        $headers = [
            'Authorization' => 'Bearer your_access_token',
            'Accept' => 'application/json',
        ];
        try {
            $client = new Client();
            $response = $client->post($url, [
                'headers' => $headers,
                'json' => $data, // Send the data as JSON in the request body
            ]);
            $responseData = $response->getBody()->getContents();

            $parsedData = json_decode($responseData, true);

            return $parsedData;
        } catch (GuzzleException $e) {
            // Handle any exceptions (e.g., connection error)
            return ['error' => $e->getMessage()];
        }

    }

}
