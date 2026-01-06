 
 
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
    
    <!-- Shop-Page -->
    <div class="page-shop u-s-p-t-80">
        <div class="container">
            <!-- Shop-Intro -->
            <div class="shop-intro">
                <ul class="bread-crumb">
                    <li class="has-separator">
                        <a href="{{ url('/') }}">Trang chủ</a>
                    </li>


                    <li>{{ $getVendorShop }}</li>



                </ul>
            </div>
            <!-- Shop-Intro /- -->
            <div class="row">



                <!-- Shop-Right-Wrapper -->
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <!-- Page-Bar -->
                    <div class="page-bar clearfix">
                        

                        <!-- //end Toolbar Sorter 2  -->
                    </div>
                    <!-- Page-Bar /- -->


                    <!-- Row-of-Product-Container -->

                    <div class="">
                        @include('front.products.vendor_products_listing')
                    </div>

                    <!-- Row-of-Product-Container /- -->



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
                <!-- Shop-Right-Wrapper /- -->


            </div>
        </div>
    </div>
    <!-- Shop-Page /- -->
@endsection