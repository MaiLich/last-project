{{-- This is the filters sidebar which is included by 'listing.blade.php' --}}
@php
    $productFilters = \App\Models\ProductsFilter::productFilters(); // Get all the (enabled/active) Filters
    // dd($productFilters);
@endphp

<!-- Shop-Left-Side-Bar-Wrapper -->
<div class="col-lg-3 col-md-3 col-sm-12">
    <!-- Fetch-Categories-from-Root-Category  -->
    <div class="fetch-categories">
        <h3 class="title-name">Danh mục sản phẩm</h3>
        
        <!-- Danh mục chính giống footer -->
        <ul>
            <li>
                <a href="{{ url('men') }}">Thời trang Nam</a>
            </li>
            <li>
                <a href="{{ url('women') }}">Thời trang Nữ</a>
            </li>
            <li>
                <a href="{{ url('kids') }}">Thời trang Trẻ em</a>
            </li>
        </ul>
    </div>
    <!-- Fetch-Categories-from-Root-Category  /- -->

    {{-- Nếu không dùng search thì mới hiển thị filters --}}
    @if (!isset($_REQUEST['search']))

        <!-- Filters -->
        <!-- Filter-Size -->
        @php
            $getSizes = \App\Models\ProductsFilter::getSizes($url);
        @endphp
        <div class="facet-filter-associates">
            <h3 class="title-name">Kích cỡ</h3>
            <form class="facet-form" action="#" method="post">
                <div class="associate-wrapper">
                    @foreach ($getSizes as $key => $size)
                        <input type="checkbox" class="check-box size" id="size{{ $key }}" name="size[]" value="{{ $size }}">
                        <label class="label-text" for="size{{ $key }}">{{ $size }}</label>
                    @endforeach
                </div>
            </form>
        </div>
        <!-- Filter-Size /- -->

        <!-- Filter-Color -->
        @php
            $getColors = \App\Models\ProductsFilter::getColors($url);
        @endphp
        <div class="facet-filter-associates">
            <h3 class="title-name">Màu sắc</h3>
            <form class="facet-form" action="#" method="post">
                <div class="associate-wrapper">
                    @foreach ($getColors as $key => $color)
                        <input type="checkbox" class="check-box color" id="color{{ $key }}" name="color[]" value="{{ $color }}">
                        <label class="label-text" for="color{{ $key }}">{{ $color }}</label>
                    @endforeach
                </div>
            </form>
        </div>
        <!-- Filter-Color /- -->

        <!-- Filter-Brand -->
        @php
            $getBrands = \App\Models\ProductsFilter::getBrands($url);
        @endphp
        <div class="facet-filter-associates">
            <h3 class="title-name">Thương hiệu</h3>
            <form class="facet-form" action="#" method="post">
                <div class="associate-wrapper">
                    @foreach ($getBrands as $key => $brand)
                        <input type="checkbox" class="check-box brand" id="brand{{ $key }}" name="brand[]" value="{{ $brand['id'] }}">
                        <label class="label-text" for="brand{{ $key }}">{{ $brand['name'] }}</label>
                    @endforeach
                </div>
            </form>
        </div>
        <!-- Filter-Brand /- -->

        <!-- Filter-Price -->
        <div class="facet-filter-associates">
            <h3 class="title-name">Mức giá</h3>
            <form class="facet-form" action="#" method="post">
                <div class="associate-wrapper">
                    @php
                        $prices = array('0-100000', '100000-200000', '200000-500000', '500000-100000', '10000-100');
                    @endphp
                    @foreach ($prices as $key => $price)
                        @php
                            $priceDisplay = str_replace(['0-', '-100000'], ['Dưới ', 'Trở lên '], $price);
                            $priceDisplay = '₫ ' . str_replace('-', ' - ₫ ', $priceDisplay);
                        @endphp
                        <input type="checkbox" class="check-box price" id="price{{ $key }}" name="price[]" value="{{ $price }}">
                        <label class="label-text" for="price{{ $key }}">{{ $priceDisplay }}</label>
                    @endforeach
                </div>
            </form>
        </div>
        <!-- Filter-Price /- -->

        {{-- Dynamic Filters --}}
        @foreach ($productFilters as $filter)
            @php
                $filterAvailable = \App\Models\ProductsFilter::filterAvailable($filter['id'], $categoryDetails['categoryDetails']['id']);
            @endphp
            @if ($filterAvailable == 'Yes')
                @if (count($filter['filter_values']) > 0)
                    <div class="facet-filter-associates">
                        <h3 class="title-name">{{ $filter['filter_name'] }}</h3>
                        <form class="facet-form" action="#" method="post">
                            <div class="associate-wrapper">
                                @foreach ($filter['filter_values'] as $value)
                                    <input type="checkbox" class="check-box {{ $filter['filter_column'] }}" id="{{ $value['filter_value'] }}" name="{{ $filter['filter_column'] }}[]" value="{{ $value['filter_value'] }}">
                                    <label class="label-text" for="{{ $value['filter_value'] }}">{{ ucwords($value['filter_value']) }}</label>
                                @endforeach
                            </div>
                        </form>
                    </div>
                @endif
            @endif
        @endforeach

    @endif
</div>
<!-- Shop-Left-Side-Bar-Wrapper /- -->