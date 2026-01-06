<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;






    public function orders_products()
    {
        return $this->hasMany('App\Models\OrdersProduct', 'order_id');
    }







    public function order_items()
    {
        return $this->hasMany('App\Models\OrdersProduct', 'order_id');
    }


    public static function pushOrder($order_id)
    {
        $orderDetails = Order::with('order_items')->where('id', $order_id)->first()->toArray();



        $orderDetails['order_id']              = $orderDetails['id'];
        $orderDetails['order_date']            = $orderDetails['created_at'];
        $orderDetails['pickup_location']       = "Test";
        $orderDetails['channel_id']            = "1855855";
        $orderDetails['comment']               = 'Test Order';
        $orderDetails['billing_customer_name'] = $orderDetails['name'];
        $orderDetails['billing_last_name']     = '';
        $orderDetails['billing_address']       = $orderDetails['address'];
        $orderDetails['billing_address_2']     = '';
        $orderDetails['billing_city']          = $orderDetails['city'];
        $orderDetails['billing_pincode']       = $orderDetails['pincode'];
        $orderDetails['billing_state']         = $orderDetails['state'];
        $orderDetails['billing_country']       = $orderDetails['country'];
        $orderDetails['billing_email']         = $orderDetails['email'];
        $orderDetails['billing_phone']         = (int) $orderDetails['mobile'];

        $orderDetails['shipping_is_billing']   = true;

        $orderDetails['shipping_customer_name'] = $orderDetails['name'];
        $orderDetails['shipping_last_name']     = '';
        $orderDetails['shipping_address']       = $orderDetails['address'];
        $orderDetails['shipping_address_2']     = '';
        $orderDetails['shipping_city']          = $orderDetails['city'];
        $orderDetails['shipping_pincode']       = $orderDetails['pincode'];
        $orderDetails['shipping_state']         = $orderDetails['state'];
        $orderDetails['shipping_country']       = $orderDetails['country'];
        $orderDetails['shipping_email']         = $orderDetails['email'];
        $orderDetails['shipping_phone']         = (int) $orderDetails['mobile'];

        foreach ($orderDetails['order_items'] as $key => $item) {
            $orderDetails['order_items'][$key]['name']          = $item['product_name'];
            $orderDetails['order_items'][$key]['sku']           = $item['product_code'];
            $orderDetails['order_items'][$key]['units']         = $item['product_qty'];
            $orderDetails['order_items'][$key]['selling_price'] = $item['product_price'];
            $orderDetails['order_items'][$key]['discount']      = '';
            $orderDetails['order_items'][$key]['tax']           = '';
            $orderDetails['order_items'][$key]['hsn']           = '';
        }



        $orderDetails['shipping_charges']    = 0;
        $orderDetails['giftwrap_charges']    = 0;
        $orderDetails['transaction_charges'] = 0;
        $orderDetails['total_discount']      = 0;
        $orderDetails['sub_total']           = $orderDetails['grand_total'];
        $orderDetails['length']              = 1;
        $orderDetails['breadth']             = 1;
        $orderDetails['height']              = 1;
        $orderDetails['weight']              = 1;





        $orderDetails = json_encode($orderDetails);






        $c = curl_init();
        $url = 'https://apiv2.shiprocket.in/v1/external/auth/login';


        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, 'email=stackdevelopers2@gmail.com&password=123456');
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);

        $server_output = curl_exec($c);


        curl_close($c);



        $server_output = json_decode($server_output, true);




        $url = 'https://apiv2.shiprocket.in/v1/external/orders/create/adhoc';
        $c = curl_init($url);

        curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, $orderDetails);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($c, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $server_output['token'] . ''
        ]);

        $result = curl_exec($c);


        curl_close($c);



        $result = json_decode($result, true);




        if (isset($result['status_code']) && $result['status_code'] == 1) {
            Order::where('id', $order_id)->update(['is_pushed' => 1]);

            $status  = true;
            $message = 'Order successfully pushed to ShipRocket';
        } else {
            $status  = false;
            $message = 'Order has not been pushed to ShipRocket. Please contact Admin';
        }


        return [
            'status'  => $status,
            'message' => $message
        ];
    }
}
