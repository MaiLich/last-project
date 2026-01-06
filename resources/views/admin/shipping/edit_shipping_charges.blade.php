@extends('admin.layout.layout')


@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Chỉnh sửa phí vận chuyển</h3>
                        </div>
                        <div class="col-12 col-xl-4">
                            <div class="justify-content-end d-flex">
                                <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                                    <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button"
                                        id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="true">
                                        <i class="mdi mdi-calendar"></i> Hôm nay (10 Tháng 1 2021)
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
                            <h4 class="card-title">{{ $title }}</h4>




                            @if (Session::has('error_message'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
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






                            @if (Session::has('success_message'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Thành công:</strong> {{ Session::get('success_message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif



                            <form class="forms-sample"
                                action="{{ url('admin/edit-shipping-charges/' . $shippingDetails['id']) }}" method="post">
                                @csrf

                                <div class="form-group">
                                    <label for="country">Quốc gia</label>
                                    <input type="text" class="form-control" value="{{ $shippingDetails['country'] }}"
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label for="0_500g">Phí (0-500g)</label>
                                    <input type="text" class="form-control" id="0_500g" placeholder="Nhập phí vận chuyển"
                                        name="0_500g" value="{{ $shippingDetails['0_500g'] }}">
                                </div>
                                <div class="form-group">
                                    <label for="501g_1000g">Phí (501g-1000g)</label>
                                    <input type="text" class="form-control" id="501g_1000g"
                                        placeholder="Nhập phí vận chuyển" name="501g_1000g"
                                        value="{{ $shippingDetails['501g_1000g'] }}">
                                </div>
                                <div class="form-group">
                                    <label for="1001_2000g">Phí (1001-2000g)</label>
                                    <input type="text" class="form-control" id="1001_2000g"
                                        placeholder="Nhập phí vận chuyển" name="1001_2000g"
                                        value="{{ $shippingDetails['1001_2000g'] }}">
                                </div>
                                <div class="form-group">
                                    <label for="2001g_5000g">Phí (2001g-5000g)</label>
                                    <input type="text" class="form-control" id="2001g_5000g"
                                        placeholder="Nhập phí vận chuyển" name="2001g_5000g"
                                        value="{{ $shippingDetails['2001g_5000g'] }}">
                                </div>
                                <div class="form-group">
                                    <label for="above_5000g">Phí (Trên 5000g)</label>
                                    <input type="text" class="form-control" id="above_5000g"
                                        placeholder="Nhập phí vận chuyển" name="above_5000g"
                                        value="{{ $shippingDetails['above_5000g'] }}">
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Lưu</button>
                                <button type="reset" class="btn btn-light">Hủy</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.layout.footer')
    </div>
@endsection