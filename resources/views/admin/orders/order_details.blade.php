
@extends('admin.layout.layout')


@section('content')
    <div class="main-panel">
        <div class="content-wrapper">


            
            
            @if (Session::has('error_message'))                 <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Lỗi:</strong> {{ Session::get('error_message') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif



            
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif


            
            
            
            
            @if (Session::has('success_message'))                 <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Thành công:</strong> {{ Session::get('success_message') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif



            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Chi tiết đơn hàng</h3>
                            <h6 class="font-weight-normal mb-0"><a href="{{ url('admin/orders') }}">Quay lại danh sách đơn hàng</a></h6>
                        </div>
                        <div class="col-12 col-xl-4">
                            <div class="justify-content-end d-flex">
                                <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                                    <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button" id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <i class="mdi mdi-calendar"></i> Hôm nay (10 Jan 2021)
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuDate2">
                                        <a class="dropdown-item" href="#">Tháng 1 - Tháng 3</a>
                                        <a class="dropdown-item" href="#">Tháng 3 - Tháng 6</a>
                                        <a class="dropdown-item" href="#">Tháng 6 - Tháng 8</a>
                                        <a class="dropdown-item" href="#">Tháng 8 - Tháng 11</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Chi tiết đơn hàng</h4>
                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Mã đơn hàng: </label>
                                <label>#{{ $orderDetails['id'] }}</label>
                            </div>
                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Ngày đặt hàng: </label>
                                <label>{{ date('Y-m-d h:i:s', strtotime($orderDetails['created_at'])) }}</label>
                            </div>
                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Trạng thái đơn hàng: </label>
                                <label>{{ $orderDetails['order_status'] }}</label>
                            </div>
                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Tổng tiền đơn hàng: </label>
                                <label>{{ $orderDetails['grand_total'] }}đ</label>
                            </div>
                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Phí vận chuyển: </label>
                                <label>{{ $orderDetails['shipping_charges'] }}đ</label>
                            </div>

                            @if (!empty($orderDetails['coupon_code']))
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Mã giảm giá: </label>
                                    <label>{{ $orderDetails['coupon_code'] }}</label>
                                </div>
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Số tiền giảm: </label>
                                    <label>{{ $orderDetails['coupon_amount'] }}đ</label>
                                </div>
                            @endif

                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Phương thức thanh toán: </label>
                                <label>{{ $orderDetails['payment_method'] }}</label>
                            </div>
                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Cổng thanh toán: </label>
                                <label>{{ $orderDetails['payment_gateway'] }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Thông tin khách hàng</h4>
                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Họ tên: </label>
                                <label>{{ $userDetails['name'] }}</label>
                            </div>

                            @if (!empty($userDetails['address']))
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Địa chỉ: </label>
                                    <label>{{ $userDetails['address'] }}</label>
                                </div>
                            @endif

                            @if (!empty($userDetails['city']))
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Thành phố: </label>
                                    <label>{{ $userDetails['city'] }}</label>
                                </div>
                            @endif

                            @if (!empty($userDetails['state']))
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Tỉnh/Bang: </label>
                                    <label>{{ $userDetails['state'] }}</label>
                                </div>
                            @endif

                            @if (!empty($userDetails['country']))
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Quốc gia: </label>
                                    <label>{{ $userDetails['country'] }}</label>
                                </div>
                            @endif

                            @if (!empty($userDetails['pincode']))
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Mã bưu chính: </label>
                                    <label>{{ $userDetails['pincode'] }}</label>
                                </div>
                            @endif

                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Số điện thoại: </label>
                                <label>{{ $userDetails['mobile'] }}</label>
                            </div>
                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Email: </label>
                                <label>{{ $userDetails['email'] }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Địa chỉ giao hàng</h4>
                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Họ tên: </label>
                                <label>{{ $orderDetails['name'] }}</label>
                            </div>

                            @if (!empty($orderDetails['address']))
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Địa chỉ: </label>
                                    <label>{{ $orderDetails['address'] }}</label>
                                </div>
                            @endif

                            @if (!empty($orderDetails['city']))
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Thành phố: </label>
                                    <label>{{ $orderDetails['city'] }}</label>
                                </div>
                            @endif

                            @if (!empty($orderDetails['state']))
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Tỉnh/Bang: </label>
                                    <label>{{ $orderDetails['state'] }}</label>
                                </div>
                            @endif

                            @if (!empty($orderDetails['country']))
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Quốc gia: </label>
                                    <label>{{ $orderDetails['country'] }}</label>
                                </div>
                            @endif

                            @if (!empty($orderDetails['pincode']))
                                <div class="form-group" style="height: 15px">
                                    <label style="font-weight: 550">Mã bưu chính: </label>
                                    <label>{{ $orderDetails['pincode'] }}</label>
                                </div>
                            @endif

                            <div class="form-group" style="height: 15px">
                                <label style="font-weight: 550">Số điện thoại: </label>
                                <label>{{ $orderDetails['mobile'] }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Cập nhật trạng thái đơn hàng</h4>  

                            
                            @if (Auth::guard('admin')->user()->type != 'vendor')   

                                
                                <form action="{{ url('admin/update-order-status') }}" method="post">  
                                    @csrf 

                                    <input type="hidden" name="order_id" value="{{ $orderDetails['id'] }}">

                                    <select name="order_status" id="order_status" required>
                                        <option value="" selected>Chọn</option>
                                        @foreach ($orderStatuses as $status)
                                            <option value="{{ $status['name'] }}"  @if (!empty($orderDetails['order_status']) && $orderDetails['order_status'] == $status['name']) selected @endif>{{ $status['name'] }}</option>
                                        @endforeach
                                    </select>

                                    
                                    <input type="text" name="courier_name"    id="courier_name"    placeholder="Tên đơn vị vận chuyển">    
                                    <input type="text" name="tracking_number" id="tracking_number" placeholder="Mã vận đơn"> 

                                    <button type="submit">Cập nhật</button>
                                </form>
                                <br>

                                
                                @foreach ($orderLog as $key => $log)
                                    @php
                                        // echo '<pre>', var_dump($log), '</pre>';
                                        // echo '<pre>', var_dump($log['orders_products']), '</pre>';
                                        // echo '<pre>', var_dump($key), '</pre>';
                                        // echo '<pre>', var_dump($log['orders_products'][$key]), '</pre>';
                                        // echo '<pre>', var_dump($log['orders_products'][$key]['product_code']), '</pre>';
                                    @endphp

                                    <strong>{{ $log['order_status'] }}</strong>

                                    
                                    @if ($orderDetails['is_pushed'] == 1) 
                                        <span style="color: green">(Đơn hàng đã đẩy sang Shiprocket)</span>
                                    @endif

                                    

                                    
                                    @if (isset($log['order_item_id']) && $log['order_item_id'] > 0) 
                                        @php
                                            $getItemDetails = \App\Models\OrdersLog::getItemDetails($log['order_item_id']);
                                        @endphp
                                        - cho sản phẩm {{ $getItemDetails['product_code'] }}

                                        @if (!empty($getItemDetails['courier_name']))
                                            <br>
                                            <span>Tên đơn vị vận chuyển: {{ $getItemDetails['courier_name'] }}</span>
                                        @endif

                                        @if (!empty($getItemDetails['tracking_number']))
                                            <br>
                                            <span>Mã vận đơn: {{ $getItemDetails['tracking_number'] }}</span>
                                        @endif

                                    @endif

                                    <br>
                                    {{ date('Y-m-d h:i:s', strtotime($log['created_at'])) }}
                                    <br>
                                    <hr>
                                @endforeach

                            @else 
                                Tính năng này bị hạn chế.
                            @endif

                        </div>
                    </div>
                </div>


                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Sản phẩm đã đặt</h4>

                            <div class="table-responsive">
                                
                                <table class="table table-striped table-borderless">
                                    <tr class="table-danger">
                                        <th>Ảnh sản phẩm</th>
                                        <th>Mã</th>
                                        <th>Tên</th>
                                        <th>Kích cỡ</th>
                                        <th>Màu sắc</th>
                                        <th>Đơn giá</th>
                                        <th>Số lượng</th>
                                        <th>Thành tiền</th>


                                        @if (\Illuminate\Support\Facades\Auth::guard('admin')->user()->type != 'vendor')  
                                            <th>Sản phẩm của</th>
                                        @endif



                                        <th>Hoa hồng</th> 
                                        <th>Số tiền thực nhận</th> 

                                        <th>Trạng thái sản phẩm</th> 
                                        
                                    </tr>

                                    @foreach ($orderDetails['orders_products'] as $product)
                                        <tr>
                                            <td>
                                                @php
                                                    $getProductImage = \App\Models\Product::getProductImage($product['product_id']);
                                                @endphp

                                                <a target="_blank" href="{{ url('product/' . $product['product_id']) }}">
                                                    <img src="{{ asset('front/images/product_images/small/' . $getProductImage) }}">
                                                </a>
                                            </td>
                                            <td>{{ $product['product_code'] }}</td>
                                            <td>{{ $product['product_name'] }}</td>
                                            <td>{{ $product['product_size'] }}</td>
                                            <td>{{ $product['product_color'] }}</td>
                                            <td>{{ $product['product_price'] }}</td>
                                            <td>{{ $product['product_qty'] }}</td>
                                            <td>

                                                @if ($product['vendor_id'] > 0) 


                                                    @if ($orderDetails['coupon_amount'] > 0) 

                                                        @if (\App\Models\Coupon::couponDetails($orderDetails['coupon_code'])['vendor_id'] > 0) 
                                                            @php
                                                                // dd(\App\Models\Coupon::couponDetails($orderDetails['coupon_code'])['vendor_id']);
                                                            @endphp

                                                        {{ $total_price = ($product['product_price'] * $product['product_qty']) - $item_discount }}
                                                        @else 
                                                            {{ $total_price = $product['product_price'] * $product['product_qty'] }}
                                                        @endif

                                                    @else 
                                                        {{ $total_price = $product['product_price'] * $product['product_qty'] }}
                                                    @endif

                                                @else 
                                                    {{ $total_price = $product['product_price'] * $product['product_qty'] }}
                                                @endif
                                            </td> 


                                            @if (\Illuminate\Support\Facades\Auth::guard('admin')->user()->type != 'vendor')  
                                                @if ($product['vendor_id'] > 0) 
                                                    <td>
                                                        <a href="/admin/view-vendor-details/{{ $product['admin_id'] }}" target="_blank">Nhà bán</a>
                                                    </td>
                                                @else
                                                    <td>Quản trị</td>
                                                @endif
                                            @endif



                                            @if ($product['vendor_id'] > 0) 
                                                <td>{{ $commission = round($total_price * $product['commission'] / 100, 2) }}</td>
                                                <td>{{ $total_price - $commission }}</td>
                                            @else
                                                <td>0</td>
                                                <td>{{ $total_price }}</td>
                                            @endif


                                            <td>

                                                
                                                <form action="{{ url('admin/update-order-item-status') }}" method="post">  
                                                    @csrf 

                                                    <input type="hidden" name="order_item_id" value="{{ $product['id'] }}">

                                                    <select id="order_item_status" name="order_item_status" required>
                                                        <option value="">Chọn</option>
                                                        @foreach ($orderItemStatuses as $status)
                                                            <option value="{{ $status['name'] }}"  @if (!empty($product['item_status']) && $product['item_status'] == $status['name']) selected @endif>{{ $status['name'] }}</option>
                                                        @endforeach
                                                    </select>

                                                    
                                                    <input style="width: 110px" type="text" name="item_courier_name"    id="item_courier_name"    placeholder="Tên vận chuyển"    @if (!empty($product['courier_name']))    value="{{ $product['courier_name'] }}"    @endif> 
                                                    <input style="width: 110px" type="text" name="item_tracking_number" id="item_tracking_number" placeholder="Mã vận đơn" @if (!empty($product['tracking_number'])) value="{{ $product['tracking_number'] }}" @endif> 

                                                    <button type="submit">Cập nhật</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


        </div>
        <!-- content-wrapper ends -->

        {{-- Footer --}}
        @include('admin.layout.footer')
        <!-- partial -->
    </div>
@endsection
