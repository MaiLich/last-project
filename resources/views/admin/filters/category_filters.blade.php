{{-- Trang này được include bởi add_edit_product.php để hiển thị các bộ lọc liên quan <select> cho sản phẩm mới THÙY THEO DANH MỤC đã chọn --}}

@php

    $productFilters = \App\Models\ProductsFilter::productFilters(); 
  
    if (isset($product['category_id'])) {
        $category_id = $product['category_id'];
    }
@endphp


@foreach ($productFilters as $filter) {{-- hiển thị TẤT CẢ bộ lọc (đang bật/hoạt động) --}}
    @php
  
    @endphp

    @if (isset($category_id)) 
        @php
            
            $filterAvailable = \App\Models\ProductsFilter::filterAvailable($filter['id'], $category_id); // $category_id đến từ AJAX (xem categoryFilters() trong Admin/FilterController.php)
        @endphp

        @if ($filterAvailable == 'Yes') 
            <div class="form-group">
                <label for="{{ $filter['filter_column'] }}">Chọn {{ $filter['filter_name'] }}</label> 
                <select name="{{ $filter['filter_column'] }}" id="{{ $filter['filter_column'] }}" class="form-control text-dark"> 
                    <option value="">Chọn giá trị bộ lọc</option>
                    @foreach ($filter['filter_values'] as $value) 
                        @php
                            
                        @endphp
                        <option value="{{ $value['filter_value'] }}" @if (!empty($product[$filter['filter_column']]) && $product[$filter['filter_column']] == $value['filter_value']) selected @endif>{{ ucwords($value['filter_value']) }}</option> {{-- $value['filter_value'] ví dụ: '4GB' --}} {{-- $product[$filter['filter_column']] ví dụ: $product['screen_size'] có thể bằng '5 to 5.4 in' --}}
                    @endforeach
                </select>
            </div>
        @endif
    @endif
@endforeach
