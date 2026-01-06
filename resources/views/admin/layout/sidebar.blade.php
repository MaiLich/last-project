{{-- Sidebar Skydash - FIX dính trạng thái cũ bằng request()->is() --}}

@php
    $ACTIVE = 'background: #052CA3 !important; color: #FFF !important';
    $INACTIVE_SUB = 'background: #fff !important; color: #052CA3 !important';

    $is = fn($pattern) => request()->is($pattern);
@endphp

<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">

        {{-- Dashboard --}}
        <li class="nav-item">
            <a style="{{ $is('admin/dashboard') ? $ACTIVE : '' }}" class="nav-link" href="{{ url('admin/dashboard') }}">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Bảng điều khiển</span>
            </a>
        </li>

        @if (Auth::guard('admin')->user()->type == 'vendor')

            {{-- Thông tin nhà bán --}}
            @php $vendorOpen = $is('admin/update-vendor-details/*'); @endphp
            <li class="nav-item">
                <a style="{{ $vendorOpen ? $ACTIVE : '' }}"
                   class="nav-link"
                   data-toggle="collapse"
                   href="#ui-vendors"
                   aria-expanded="{{ $vendorOpen ? 'true' : 'false' }}"
                   aria-controls="ui-vendors">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Thông tin nhà bán</span>
                    <i class="menu-arrow"></i>
                </a>

                <div class="collapse {{ $vendorOpen ? 'show' : '' }}" id="ui-vendors">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        <li class="nav-item">
                            <a style="{{ $is('admin/update-vendor-details/personal') ? $ACTIVE : $INACTIVE_SUB }}"
                               class="nav-link"
                               href="{{ url('admin/update-vendor-details/personal') }}">Thông tin cá nhân</a>
                        </li>
                        <li class="nav-item">
                            <a style="{{ $is('admin/update-vendor-details/business') ? $ACTIVE : $INACTIVE_SUB }}"
                               class="nav-link"
                               href="{{ url('admin/update-vendor-details/business') }}">Thông tin kinh doanh</a>
                        </li>
                        <li class="nav-item">
                            <a style="{{ $is('admin/update-vendor-details/bank') ? $ACTIVE : $INACTIVE_SUB }}"
                               class="nav-link"
                               href="{{ url('admin/update-vendor-details/bank') }}">Thông tin ngân hàng</a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- Quản lý danh mục (vendor) --}}
            @php $catOpen = $is('admin/products*') || $is('admin/coupons*'); @endphp
            <li class="nav-item">
                <a style="{{ $catOpen ? $ACTIVE : '' }}"
                   class="nav-link"
                   data-toggle="collapse"
                   href="#ui-catalogue"
                   aria-expanded="{{ $catOpen ? 'true' : 'false' }}"
                   aria-controls="ui-catalogue">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Quản lý danh mục</span>
                    <i class="menu-arrow"></i>
                </a>

                <div class="collapse {{ $catOpen ? 'show' : '' }}" id="ui-catalogue">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        <li class="nav-item">
                            <a style="{{ $is('admin/products*') ? $ACTIVE : $INACTIVE_SUB }}"
                               class="nav-link" href="{{ url('admin/products') }}">Sản phẩm</a>
                        </li>
                        <li class="nav-item">
                            <a style="{{ $is('admin/coupons*') ? $ACTIVE : $INACTIVE_SUB }}"
                               class="nav-link" href="{{ url('admin/coupons') }}">Mã giảm giá</a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- Quản lý đơn hàng (vendor) --}}
            @php $ordersOpen = $is('admin/orders*'); @endphp
            <li class="nav-item">
                <a style="{{ $ordersOpen ? $ACTIVE : '' }}"
                   class="nav-link"
                   data-toggle="collapse"
                   href="#ui-orders"
                   aria-expanded="{{ $ordersOpen ? 'true' : 'false' }}"
                   aria-controls="ui-orders">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Quản lý đơn hàng</span>
                    <i class="menu-arrow"></i>
                </a>

                <div class="collapse {{ $ordersOpen ? 'show' : '' }}" id="ui-orders">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        <li class="nav-item">
                            <a style="{{ $ordersOpen ? $ACTIVE : $INACTIVE_SUB }}"
                               class="nav-link" href="{{ url('admin/orders') }}">Đơn hàng</a>
                        </li>
                    </ul>
                </div>
            </li>

        @else

            {{-- Cài đặt --}}
            @php $settingsOpen = $is('admin/update-admin-password') || $is('admin/update-admin-details'); @endphp
            <li class="nav-item">
                <a style="{{ $settingsOpen ? $ACTIVE : '' }}"
                   class="nav-link"
                   data-toggle="collapse"
                   href="#ui-settings"
                   aria-expanded="{{ $settingsOpen ? 'true' : 'false' }}"
                   aria-controls="ui-settings">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Cài đặt</span>
                    <i class="menu-arrow"></i>
                </a>

                <div class="collapse {{ $settingsOpen ? 'show' : '' }}" id="ui-settings">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        <li class="nav-item">
                            <a style="{{ $is('admin/update-admin-password') ? $ACTIVE : $INACTIVE_SUB }}"
                               class="nav-link" href="{{ url('admin/update-admin-password') }}">Cập nhật mật khẩu quản trị</a>
                        </li>
                        <li class="nav-item">
                            <a style="{{ $is('admin/update-admin-details') ? $ACTIVE : $INACTIVE_SUB }}"
                               class="nav-link" href="{{ url('admin/update-admin-details') }}">Cập nhật thông tin quản trị</a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- Quản lý tài khoản quản trị --}}
            @php $adminsOpen = $is('admin/admins*'); @endphp
            <li class="nav-item">
                <a style="{{ $adminsOpen ? $ACTIVE : '' }}"
                   class="nav-link"
                   data-toggle="collapse"
                   href="#ui-admins"
                   aria-expanded="{{ $adminsOpen ? 'true' : 'false' }}"
                   aria-controls="ui-admins">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Quản lý tài khoản quản trị</span>
                    <i class="menu-arrow"></i>
                </a>

                <div class="collapse {{ $adminsOpen ? 'show' : '' }}" id="ui-admins">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        <li class="nav-item">
                            <a style="{{ $is('admin/admins/admin') ? $ACTIVE : $INACTIVE_SUB }}"
                               class="nav-link" href="{{ url('admin/admins/admin') }}">Quản trị viên</a>
                        </li>
                        <li class="nav-item">
                            <a style="{{ $is('admin/admins/vendor') ? $ACTIVE : $INACTIVE_SUB }}"
                               class="nav-link" href="{{ url('admin/admins/vendor') }}">Nhà bán</a>
                        </li>
                        <li class="nav-item">
                            <a style="{{ $is('admin/admins') ? $ACTIVE : $INACTIVE_SUB }}"
                               class="nav-link" href="{{ url('admin/admins') }}">Tất cả</a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- Quản lý danh mục (admin) --}}
            @php
                $catOpen = $is('admin/sections*') || $is('admin/categories*') || $is('admin/brands*')
                        || $is('admin/products*') || $is('admin/coupons*') || $is('admin/filters*');
            @endphp
            <li class="nav-item">
                <a style="{{ $catOpen ? $ACTIVE : '' }}"
                   class="nav-link"
                   data-toggle="collapse"
                   href="#ui-catalogue"
                   aria-expanded="{{ $catOpen ? 'true' : 'false' }}"
                   aria-controls="ui-catalogue">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Quản lý danh mục</span>
                    <i class="menu-arrow"></i>
                </a>

                <div class="collapse {{ $catOpen ? 'show' : '' }}" id="ui-catalogue">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        <li class="nav-item"><a style="{{ $is('admin/sections*') ? $ACTIVE : $INACTIVE_SUB }}" class="nav-link" href="{{ url('admin/sections') }}">Danh mục cha</a></li>
                        <li class="nav-item"><a style="{{ $is('admin/categories*') ? $ACTIVE : $INACTIVE_SUB }}" class="nav-link" href="{{ url('admin/categories') }}">Danh mục</a></li>
                        <li class="nav-item"><a style="{{ $is('admin/brands*') ? $ACTIVE : $INACTIVE_SUB }}" class="nav-link" href="{{ url('admin/brands') }}">Thương hiệu</a></li>
                        <li class="nav-item"><a style="{{ $is('admin/products*') ? $ACTIVE : $INACTIVE_SUB }}" class="nav-link" href="{{ url('admin/products') }}">Sản phẩm</a></li>
                        <li class="nav-item"><a style="{{ $is('admin/coupons*') ? $ACTIVE : $INACTIVE_SUB }}" class="nav-link" href="{{ url('admin/coupons') }}">Mã giảm giá</a></li>
                        <li class="nav-item"><a style="{{ $is('admin/filters*') ? $ACTIVE : $INACTIVE_SUB }}" class="nav-link" href="{{ url('admin/filters') }}">Bộ lọc</a></li>
                    </ul>
                </div>
            </li>

            {{-- Orders --}}
            @php $ordersOpen = $is('admin/orders*'); @endphp
            <li class="nav-item">
                <a style="{{ $ordersOpen ? $ACTIVE : '' }}"
                   class="nav-link"
                   data-toggle="collapse"
                   href="#ui-orders"
                   aria-expanded="{{ $ordersOpen ? 'true' : 'false' }}"
                   aria-controls="ui-orders">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Quản lý đơn hàng</span>
                    <i class="menu-arrow"></i>
                </a>

                <div class="collapse {{ $ordersOpen ? 'show' : '' }}" id="ui-orders">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        <li class="nav-item">
                            <a style="{{ $ordersOpen ? $ACTIVE : $INACTIVE_SUB }}"
                               class="nav-link" href="{{ url('admin/orders') }}">Đơn hàng</a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- Ratings --}}
            @php $ratingsOpen = $is('admin/ratings*'); @endphp
            <li class="nav-item">
                <a style="{{ $ratingsOpen ? $ACTIVE : '' }}"
                   class="nav-link"
                   data-toggle="collapse"
                   href="#ui-ratings"
                   aria-expanded="{{ $ratingsOpen ? 'true' : 'false' }}"
                   aria-controls="ui-ratings">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Quản lý đánh giá</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse {{ $ratingsOpen ? 'show' : '' }}" id="ui-ratings">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        <li class="nav-item">
                            <a style="{{ $ratingsOpen ? $ACTIVE : $INACTIVE_SUB }}"
                               class="nav-link" href="{{ url('admin/ratings') }}">Đánh giá & nhận xét sản phẩm</a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- Users --}}
            @php $usersOpen = $is('admin/users*'); @endphp
            <li class="nav-item">
                <a style="{{ $usersOpen ? $ACTIVE : '' }}"
                   class="nav-link"
                   data-toggle="collapse"
                   href="#ui-users"
                   aria-expanded="{{ $usersOpen ? 'true' : 'false' }}"
                   aria-controls="ui-users">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Quản lý người dùng</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse {{ $usersOpen ? 'show' : '' }}" id="ui-users">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        <li class="nav-item">
                            <a style="{{ $usersOpen ? $ACTIVE : $INACTIVE_SUB }}"
                               class="nav-link" href="{{ url('admin/users') }}">Người dùng</a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- Banners --}}
            @php $bannersOpen = $is('admin/banners*'); @endphp
            <li class="nav-item">
                <a style="{{ $bannersOpen ? $ACTIVE : '' }}"
                   class="nav-link"
                   data-toggle="collapse"
                   href="#ui-banners"
                   aria-expanded="{{ $bannersOpen ? 'true' : 'false' }}"
                   aria-controls="ui-banners">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Quản lý banner</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse {{ $bannersOpen ? 'show' : '' }}" id="ui-banners">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        <li class="nav-item">
                            <a style="{{ $bannersOpen ? $ACTIVE : $INACTIVE_SUB }}"
                               class="nav-link" href="{{ url('admin/banners') }}">Banner trang chủ</a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- Shipping --}}
            @php $shipOpen = $is('admin/shipping-charges*'); @endphp
            <li class="nav-item">
                <a style="{{ $shipOpen ? $ACTIVE : '' }}"
                   class="nav-link"
                   data-toggle="collapse"
                   href="#ui-shipping"
                   aria-expanded="{{ $shipOpen ? 'true' : 'false' }}"
                   aria-controls="ui-shipping">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Quản lý vận chuyển</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse {{ $shipOpen ? 'show' : '' }}" id="ui-shipping">
                    <ul class="nav flex-column sub-menu" style="background: #fff !important; color: #052CA3 !important">
                        <li class="nav-item">
                            <a style="{{ $shipOpen ? $ACTIVE : $INACTIVE_SUB }}"
                               class="nav-link" href="{{ url('admin/shipping-charges') }}">Phí vận chuyển</a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- Blog --}}
            <li class="nav-item">
                <a style="{{ $is('admin/blog*') ? $ACTIVE : '' }}"
                   class="nav-link"
                   href="{{ route('admin.blog.index') }}">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Quản lý blog</span>
                    <i class="menu-arrow"></i>
                </a>
            </li>

            {{-- Chat --}}
            <li class="nav-item">
                <a style="{{ $is('admin/chat*') ? $ACTIVE : '' }}"
                   class="nav-link"
                   href="{{ route('admin.chat.index') }}">
                    <i class="icon-layout menu-icon"></i>
                    <span class="menu-title">Tin nhắn người dùng</span>
                    <i class="menu-arrow"></i>
                </a>
            </li>

        @endif

    </ul>
</nav>
