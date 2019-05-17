<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
//注册
Route::post('reg/regdo','LoginController@regdo');
//登录
Route::post('reg/logindo','LoginController@logindo');
//个人中心
//加入购物车
Route::post('goods/addcart','LoginController@addcart');
