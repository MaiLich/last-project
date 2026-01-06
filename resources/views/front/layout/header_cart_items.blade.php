


<div class="mini-cart-wrapper">
    <div class="mini-cart">
        <div class="mini-cart-header">
            GIỎ HÀNG CỦA BẠN
            <button type="button" class="button ion ion-md-close" id="mini-cart-close"></button>
        </div>
        <ul class="mini-cart-list">

             
            
            @php $total_price = 0 @endphp

            @php
                $getCartItems = getCartItems(); 
            @endphp

            @foreach ($getCartItems as $item) 
                @php
                    $getDiscountAttributePrice = \App\Models\Product::getDiscountAttributePrice($item['product_id'], $item['size']); 
                    // dd($getDiscountAttributePrice);
                @endphp
                <li class="clearfix">
                    <a href="{{ url('product/' . $item['product_id']) }}">
                    <img src="{{ asset('front/images/product_images/small/' . $item['product']['product_image']) }}" alt="Product">
                    <span class="mini-item-name">{{ $item['product']['product_name'] }}</span>
                    <span class="mini-item-price">{{ number_format($getDiscountAttributePrice['final_price'], 0, ',', '.') }}₫</span>
                    <span class="mini-item-quantity"> x {{ $item['quantity'] }} </span>
                    </a>
                </li>
                
                @php $total_price = $total_price + ($getDiscountAttributePrice['final_price'] * $item['quantity']) @endphp
            @endforeach



        </ul>
        <div class="mini-shop-total clearfix">
            <span class="mini-total-heading float-left">Tổng cộng:</span>
            <span class="mini-total-price float-right">{{ number_format($total_price, 0, ',', '.') }}₫</span>
        </div>
        <div class="mini-action-anchors">
            <a href="{{ url('cart') }}"     class="cart-anchor">Xem giỏ hàng</a>
            <a href="{{ url('checkout') }}" class="checkout-anchor">Thanh toán</a>
        </div>
    </div>
</div>



 
