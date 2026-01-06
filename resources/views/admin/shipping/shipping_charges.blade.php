
@extends('admin.layout.layout')


@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Phí vận chuyển</h4>
                            


                            
                            
                            
                            @if (Session::has('success_message'))                                 <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Thành công:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif


                            <div class="table-responsive pt-3">
                                
                                <table id="shipping" class="table table-bordered"> 
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Quốc gia</th>
                                            <th>Mức phí (0g đến 500g)</th>
                                            <th>Mức phí (501g đến 1000g)</th>
                                            <th>Mức phí (1001g đến 2000g)</th>
                                            <th>Mức phí (2001g đến 5000g)</th>
                                            <th>Mức phí (Trên 5000g)</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($shippingCharges as $shipping)
                                            <tr>
                                                <td>{{ $shipping['id'] }}</td>
                                                <td>{{ $shipping['country'] }}</td>
                                                <td>{{ $shipping['0_500g'] }}</td>
                                                <td>{{ $shipping['501g_1000g'] }}</td>
                                                <td>{{ $shipping['1001_2000g'] }}</td>
                                                <td>{{ $shipping['2001g_5000g'] }}</td>
                                                <td>{{ $shipping['above_5000g'] }}</td>
                                                <td>
                                                    @if ($shipping['status'] == 1)
                                                        <a class="updateShippingStatus" id="shipping-{{ $shipping['id'] }}" shipping_id="{{ $shipping['id'] }}" href="javascript:void(0)"> 
                                                            <i style="font-size: 25px" class="mdi mdi-bookmark-check" status="Active"></i> 
                                                        </a>
                                                    @else 
                                                        <a class="updateShippingStatus" id="shipping-{{ $shipping['id'] }}" shipping_id="{{ $shipping['id'] }}" href="javascript:void(0)"> 
                                                            <i style="font-size: 25px" class="mdi mdi-bookmark-outline" status="Inactive"></i> 
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ url('admin/edit-shipping-charges/' . $shipping['id']) }}">
                                                        <i style="font-size: 25px" class="mdi mdi-pencil-box"></i> 
                                                    </a>

                                                    
                                                    
                                                         
                                                    
                                                    
                                                         
                                                    
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                        <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Bản quyền © 2025. Mọi quyền được bảo lưu.</span>
            </div>
        </footer>
        <!-- partial -->
    </div>
@endsection
