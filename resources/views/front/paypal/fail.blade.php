
@extends('front.layout.layout')


@section('content')
        <div class="page-style-a">
        <div class="container">
            <div class="page-intro">
                <h2>Giỏ hàng</h2>
                <ul class="bread-crumb">
                    <li class="has-separator">
                        <i class="ion ion-md-home"></i>
                        <a href="{{ url('/') }}">Trang chủ</a>
                    </li>
                    <li class="is-marked">
                        <a href="#">Thanh toán thất bại!</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
            <div class="page-cart u-s-p-t-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-12" align="center">
                    <h3>Thanh toán của bạn thất bại!</h3>
                    <p>Vui lòng thử lại sau ít phút. Nếu vẫn gặp vấn đề, hãy liên hệ với chúng tôi để được hỗ trợ.</p>
                </div>
            </div>
        </div>
    </div>
    @endsection



 
@php
    use Illuminate\Support\Facades\Session;

    Session::forget('grand_total');  
    Session::forget('order_id');     
    Session::forget('couponCode');   
    Session::forget('couponAmount'); 
@endphp