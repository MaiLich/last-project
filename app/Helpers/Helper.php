<?php

use App\Models\Cart;




function totalCartItems()
{
    if (\Illuminate\Support\Facades\Auth::check()) {
        $user_id = \Illuminate\Support\Facades\Auth::user()->id;
        $totalCartItems = Cart::where('user_id', $user_id)->sum('quantity');
    } else {
        $session_id = \Illuminate\Support\Facades\Session::get('session_id');
        $totalCartItems = Cart::where('session_id', $session_id)->sum('quantity');
    }


    return $totalCartItems;
}





function getCartItems()
{

    if (\Illuminate\Support\Facades\Auth::check()) {
        $getCartItems = Cart::with([
            'product' => function ($query) {
                $query->select('id', 'category_id', 'product_name', 'product_code', 'product_color', 'product_image');
            }
        ])->orderBy('id', 'Desc')->where([
            'user_id'    => \Illuminate\Support\Facades\Auth::user()->id

        ])->get()->toArray();
    } else {
        $getCartItems = Cart::with([
            'product' => function ($query) {
                $query->select('id', 'category_id', 'product_name', 'product_code', 'product_color', 'product_image');
            }
        ])->orderBy('id', 'Desc')->where([

            'session_id' => \Illuminate\Support\Facades\Session::get('session_id')
        ])->get()->toArray();
    }









    return $getCartItems;
}
