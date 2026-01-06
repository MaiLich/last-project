<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {

    return $request->user();
});

Route::namespace('App\Http\Controllers\API')->group(function () {

    Route::get('push-order/{id}', 'APIController@pushOrder');


    // ================== API WEBSITE THƯƠNG MẠI ĐIỆN TỬ ==================

    // Lấy TẤT CẢ user hoặc 1 user cụ thể (GET)

    // Người dùng phải gửi Authorization Header (Bearer Token)
    Route::get('users/{id?}', 'APIController@getUsers');

    // Tạo 1 user (POST)

    // Có thể gửi dữ liệu bằng form-data hoặc raw JSON
    Route::post('add-user', 'APIController@addUser');

    // Tạo nhiều user (POST)

    Route::post('add-multiple-users', 'APIController@addMultipleUsers');

    // Cập nhật toàn bộ thông tin user (PUT)

    Route::put('update-user-details/{id?}', 'APIController@updateUserDetails');

    // Cập nhật CHỈ tên user (PATCH)

    Route::patch('update-user-name/{id?}', 'APIController@updateUserName');

    // Xóa 1 user (DELETE)

    Route::delete('delete-user/{id?}', 'APIController@deleteUser');

    // Xóa nhiều user cùng lúc (DELETE)

    Route::delete('delete-multiple-users/{ids?}', 'APIController@deleteMultipleUsers');

    // Đăng ký user mới và tạo Access Token (POST)

    Route::post('register-user', 'APIController@registerUser');

    // Đăng nhập user và tạo Access Token mới (POST)

    Route::post('login-user', 'APIController@loginUser');

    // Đăng xuất user và xóa Access Token hiện tại (POST)

    Route::post('logout-user', 'APIController@logoutUser');



    // Đăng ký user mới và tạo token bằng Passport (POST)

    Route::post('register-user-with-passport', 'APIController@registerUserWithPassport');

    // Đăng nhập user bằng Passport (POST)

    Route::post('login-user-with-passport', 'APIController@loginUserWithPassport');

    // ================== QUẢN LÝ TỒN KHO QUA API BÊN THỨ BA ==================

    // Cập nhật tồn kho thông qua API bên thứ ba (POST)

    Route::post('update-stock', 'APIController@updateStock');

    // Webhook cập nhật tồn kho từ hệ thống bên thứ ba (POST)

    Route::post('update-stock-with-webhook', 'APIController@updateStockWithWebhook');
});
