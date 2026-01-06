<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Front\BlogController as FrontBlogController;
use App\Http\Controllers\Front\BlogCommentController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\ChatbotController;


// USER
Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/messages/{conversationId}', [ChatController::class, 'getMessages'])->name('chat.messages');
});

// ADMIN
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('/chat', [ChatController::class, 'adminIndex'])->name('admin.chat.index');
    Route::get('/chat/conversation/{conversationId}', [ChatController::class, 'adminShowConversation'])->name('admin.chat.show');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('admin.chat.send');
    Route::get('/chat/messages/{conversationId}', [ChatController::class, 'getMessages'])->name('admin.chat.messages');
});


if (Schema::hasTable('categories')) {
    $catUrls = \App\Models\Category::select('url')
        ->where('status', 1)
        ->pluck('url')
        ->toArray();

    foreach ($catUrls as $url) {
        Route::match(['get', 'post'], '/' . $url, 'ProductsController@listing');
    }
}

Route::get('/favicon.ico', function () {
    return response()->noContent();
});

Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');



require __DIR__.'/auth.php';







Route::prefix('/admin')->namespace('App\Http\Controllers\Admin')->group(function() {
    Route::match(['get', 'post'], 'login', 'AdminController@login'); 


    
    Route::group(['middleware' => ['admin']], function() { 
        Route::get('dashboard', 'AdminController@dashboard'); 
        Route::get('logout', 'AdminController@logout'); 
        Route::match(['get', 'post'], 'update-admin-password', 'AdminController@updateAdminPassword'); 
        Route::post('check-admin-password', 'AdminController@checkAdminPassword'); 
        Route::match(['get', 'post'], 'update-admin-details', 'AdminController@updateAdminDetails'); 
        Route::match(['get', 'post'], 'update-vendor-details/{slug}', 'AdminController@updateVendorDetails'); 

        // Update the vendor's commission percentage (by the Admin) in `vendors` table 
        Route::post('update-vendor-commission', 'AdminController@updateVendorCommission');

        Route::get('admins/{type?}', 'AdminController@admins'); 
        Route::get('view-vendor-details/{id}', 'AdminController@viewVendorDetails'); 
        Route::post('update-admin-status', 'AdminController@updateAdminStatus'); 
    

        // Sections (Sections, Categories, Subcategories, Products, Attributes)
        Route::get('sections', 'SectionController@sections');
        Route::post('update-section-status', 'SectionController@updateSectionStatus'); 
        Route::get('delete-section/{id}', 'SectionController@deleteSection'); 
        Route::match(['get', 'post'], 'add-edit-section/{id?}', 'SectionController@addEditSection'); 

        // Categories
        Route::get('categories', 'CategoryController@categories'); 
        Route::post('update-category-status', 'CategoryController@updateCategoryStatus'); 
        Route::match(['get', 'post'], 'add-edit-category/{id?}', 'CategoryController@addEditCategory'); 
        Route::get('append-categories-level', 'CategoryController@appendCategoryLevel'); 
        Route::get('delete-category/{id}', 'CategoryController@deleteCategory'); 
        Route::get('delete-category-image/{id}', 'CategoryController@deleteCategoryImage'); 

        // Brands
        Route::get('brands', 'BrandController@brands');
        Route::post('update-brand-status', 'BrandController@updateBrandStatus'); 
        Route::get('delete-brand/{id}', 'BrandController@deleteBrand'); 
        Route::match(['get', 'post'], 'add-edit-brand/{id?}', 'BrandController@addEditBrand'); 

        // Products
        Route::get('products', 'ProductsController@products'); 
        Route::post('update-product-status', 'ProductsController@updateProductStatus'); 
        Route::get('delete-product/{id}', 'ProductsController@deleteProduct'); 
        Route::match(['get', 'post'], 'add-edit-product/{id?}', 'ProductsController@addEditProduct'); 
        Route::get('delete-product-image/{id}', 'ProductsController@deleteProductImage'); 
        Route::get('delete-product-video/{id}', 'ProductsController@deleteProductVideo'); 

        // Attributes
        Route::match(['get', 'post'], 'add-edit-attributes/{id}', 'ProductsController@addAttributes'); 
        Route::post('update-attribute-status', 'ProductsController@updateAttributeStatus'); 
        Route::get('delete-attribute/{id}', 'ProductsController@deleteAttribute'); 
        Route::match(['get', 'post'], 'edit-attributes/{id}', 'ProductsController@editAttributes'); 

        // Images
        Route::match(['get', 'post'], 'add-images/{id}', 'ProductsController@addImages'); 
        Route::post('update-image-status', 'ProductsController@updateImageStatus'); 
        Route::get('delete-image/{id}', 'ProductsController@deleteImage'); 

        // Banners
        Route::get('banners', 'BannersController@banners');
        Route::post('update-banner-status', 'BannersController@updateBannerStatus'); 
        Route::get('delete-banner/{id}', 'BannersController@deleteBanner'); 
        Route::match(['get', 'post'], 'add-edit-banner/{id?}', 'BannersController@addEditBanner'); 

        // Filters
        Route::get('filters', 'FilterController@filters'); 
        Route::post('update-filter-status', 'FilterController@updateFilterStatus'); 
        Route::post('update-filter-value-status', 'FilterController@updateFilterValueStatus'); 
        Route::get('filters-values', 'FilterController@filtersValues'); 
        Route::match(['get', 'post'], 'add-edit-filter/{id?}', 'FilterController@addEditFilter'); 
        Route::match(['get', 'post'], 'add-edit-filter-value/{id?}', 'FilterController@addEditFilterValue'); 
        Route::post('category-filters', 'FilterController@categoryFilters'); 

        // Coupons
        Route::get('coupons', 'CouponsController@coupons'); 
        Route::post('update-coupon-status', 'CouponsController@updateCouponStatus'); 
        Route::get('delete-coupon/{id}', 'CouponsController@deleteCoupon'); 

        
        Route::match(['get', 'post'], 'add-edit-coupon/{id?}', 'CouponsController@addEditCoupon'); 

        // Users
        Route::get('users', 'UserController@users'); 
        Route::post('update-user-status', 'UserController@updateUserStatus'); 

        // Orders
        
        Route::get('orders', 'OrderController@orders');

        
        Route::get('orders/{id}', 'OrderController@orderDetails'); 

        
        
        Route::post('update-order-status', 'OrderController@updateOrderStatus');

        
        
        Route::post('update-order-item-status', 'OrderController@updateOrderItemStatus');

        // Orders Invoices
        
        Route::get('orders/invoice/{id}', 'OrderController@viewOrderInvoice'); 

        
        Route::get('orders/invoice/pdf/{id}', 'OrderController@viewPDFInvoice'); 

        // Shipping Charges module
        
        Route::get('shipping-charges', 'ShippingController@shippingCharges');

        
        Route::post('update-shipping-status', 'ShippingController@updateShippingStatus');

        
        Route::match(['get', 'post'], 'edit-shipping-charges/{id}', 'ShippingController@editShippingCharges'); 



        // Newsletter Subscribers module
        
        Route::get('subscribers', 'NewsletterController@subscribers');

        
        Route::post('update-subscriber-status', 'NewsletterController@updateSubscriberStatus');

        
        Route::get('delete-subscriber/{id}', 'NewsletterController@deleteSubscriber'); 



        // Export subscribers (`newsletter_subscribers` database table) as an Excel file using Maatwebsite/Laravel Excel Package in admin/subscribers/subscribers.blade.php
        Route::get('export-subscribers', 'NewsletterController@exportSubscribers');

        // User Ratings & Reviews
        
        Route::get('ratings', 'RatingController@ratings');

        
        Route::post('update-rating-status', 'RatingController@updateRatingStatus');

        
        Route::get('delete-rating/{id}', 'RatingController@deleteRating'); 
    });

});






// User download order PDF invoice
Route::get('orders/invoice/download/{id}', 'App\Http\Controllers\Admin\OrderController@viewPDFInvoice');






// Second: FRONT section routes:
Route::namespace('App\Http\Controllers\Front')->group(function() {
    Route::get('/', 'IndexController@index');


    
    
    $catUrls = \App\Models\Category::select('url')->where('status', 1)->get()->pluck('url')->toArray(); 
    
    foreach ($catUrls as $key => $url) {
        
        Route::match(['get', 'post'], '/' . $url, 'ProductsController@listing'); 
    }


    
    Route::get('vendor/login-register', 'VendorController@loginRegister'); 

    
    Route::post('vendor/register', 'VendorController@vendorRegister'); 

    
    Route::get('vendor/confirm/{code}', 'VendorController@confirmVendor'); 

    
    Route::get('/product/{id}', 'ProductsController@detail');

    
    Route::post('get-product-price', 'ProductsController@getProductPrice');

    
    Route::get('/products/{vendorid}', 'ProductsController@vendorListing');

    
    Route::post('cart/add', 'ProductsController@cartAdd');

    
    Route::get('cart', 'ProductsController@cart')->name('cart');

    
    Route::post('cart/update', 'ProductsController@cartUpdate');

    
    Route::post('cart/delete', 'ProductsController@cartDelete');



    
    Route::get('user/login-register', ['as' => 'login', 'uses' => 'UserController@loginRegister']); 

    
    Route::post('user/register', 'UserController@userRegister');

    
    Route::post('user/login', 'UserController@userLogin');

    
    Route::get('user/logout', 'UserController@userLogout');

    
    Route::match(['get', 'post'], 'user/forgot-password', 'UserController@forgotPassword'); 

    
    Route::get('user/confirm/{code}', 'UserController@confirmAccount'); 

    
    Route::get('search-products', 'ProductsController@listing');

    
    Route::post('check-pincode', 'ProductsController@checkPincode');

    
    Route::match(['get', 'post'], 'contact', 'CmsController@contact');

    
    Route::post('add-subscriber-email', 'NewsletterController@addSubscriber');

    
    Route::post('add-rating', 'RatingController@addRating');




    
    Route::group(['middleware' => ['auth']], function() {
        
        Route::match(['GET', 'POST'], 'user/account', 'UserController@userAccount');

        
        Route::post('user/update-password', 'UserController@userUpdatePassword');

        
        Route::post('/apply-coupon', 'ProductsController@applyCoupon'); 

        
        Route::match(['GET', 'POST'], '/checkout', 'ProductsController@checkout');

        
        Route::post('get-delivery-address', 'AddressController@getDeliveryAddress');

        
        Route::post('save-delivery-address', 'AddressController@saveDeliveryAddress');

        
        Route::post('remove-delivery-address', 'AddressController@removeDeliveryAddress');

        
        Route::get('thanks', 'ProductsController@thanks');

        
        Route::get('user/orders/{id?}', 'OrderController@orders'); 



        
        
        Route::get('paypal', 'PaypalController@paypal');

        
        Route::post('pay', 'PaypalController@pay')->name('payment'); 

        
        Route::get('success', 'PaypalController@success');

        
        Route::get('error', 'PaypalController@error');



        
        
        Route::get('iyzipay', 'IyzipayController@iyzipay');

        
        Route::get('iyzipay/pay', 'IyzipayController@pay'); 
    });

});

Route::get('/blog', [FrontBlogController::class,'index'])->name('front.blog.index');
Route::get('/blog/{slug}', [FrontBlogController::class,'show'])->name('front.blog.show');

Route::middleware('auth')->group(function () {
    Route::post('/blog/{slug}/comments', [BlogCommentController::class,'store'])
        ->name('front.blog.comment.store');
});

Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
    // CRUD bài viết
    Route::get('blog',               [BlogPostController::class,'index'])->name('blog.index');
    Route::get('blog/create',        [BlogPostController::class,'create'])->name('blog.create');
    Route::post('blog',              [BlogPostController::class,'store'])->name('blog.store');
    Route::get('blog/{post}/edit',   [BlogPostController::class,'edit'])->name('blog.edit');
    Route::put('blog/{post}',        [BlogPostController::class,'update'])->name('blog.update');
    Route::delete('blog/{post}',     [BlogPostController::class,'destroy'])->name('blog.destroy');

    // Bật/tắt publish nhanh
    Route::patch('blog/{post}/toggle', [BlogPostController::class,'toggle'])->name('blog.toggle');

    // Duyệt bình luận theo bài
    Route::get('blog/{post}/comments',   [BlogPostController::class,'comments'])->name('blog.comments');
    Route::patch('blog/comments/{id}/approve', [BlogPostController::class,'approveComment'])->name('blog.comments.approve');
    Route::patch('blog/comments/{id}/reject',  [BlogPostController::class,'rejectComment'])->name('blog.comments.reject');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/messages/{conversationId}', [ChatController::class, 'getMessages'])->name('chat.messages');
});

Route::prefix('admin')->middleware(['admin'])->group(function () {
    Route::get('/chat', [ChatController::class, 'adminIndex'])->name('admin.chat.index');
    Route::get('/chat/conversation/{id}', [ChatController::class, 'adminShowConversation'])->name('admin.chat.show');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('admin.chat.send');
    Route::get('/chat/messages/{conversationId}', [ChatController::class, 'getMessages'])->name('admin.chat.messages');
});
