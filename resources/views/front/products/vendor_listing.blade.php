@extends('front.layout.layout')


@section('content')
    <div class="page-style-a">
        <div class="container">
            <div class="page-intro">
                <h2>{{ $getVendorShop }}</h2>
                <ul class="bread-crumb">
                    <li class="has-separator">
                        <i class="ion ion-md-home"></i>
                        <a href="{{ url('/') }}">Trang chủ</a>
                    </li>
                    <li class="is-marked">
                        <a href="javascript:;">{{ $getVendorShop }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-shop u-s-p-t-80">
        <div class="container">
            <div class="shop-intro">
                <ul class="bread-crumb">
                    <li class="has-separator">
                        <a href="{{ url('/') }}">Trang chủ</a>
                    </li>


                    <li>{{ $getVendorShop }}</li>



                </ul>
            </div>
            <div class="row">



                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="page-bar clearfix">


                    </div>



                    <div class="">
                        @include('front.products.vendor_products_listing')
                    </div>




                    @if (isset($_GET['sort']))
                        <div>
                            {{ $vendorProducts->appends(['sort' => $_GET['sort']])->links() }}
                        </div>
                    @else
                        <div>
                            {{ $vendorProducts->links() }}
                        </div>
                    @endif


                    <div>&nbsp;</div>
                </div>


            </div>
        </div>
    </div>
@endsection