<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
use App\Http\Controllers\UserController;

$app->get('/', function() {
  return view('index');
});
$app->get('/test', function() {
  return view('test');
});

//登录
$app->post('/api/auth', 'AuthController@store');
//退出
$app->delete('/api/auth', ['middleware' => 'auth', 'uses' => 'AuthController@destroy']);

//设置
//返回安卓版本
$app->get('/api/option/android/version', 'OptionController@androidVersion');
//返回Windows版本
$app->get('/api/option/windows/version', 'OptionController@windowsVersion');
$app->group(['prefix' => '/api/option', 'middleware' => 'auth'], function($app) {
  $app->get('/', 'OptionController@index');
  $app->put('/', 'OptionController@update');
});

//组管理
$app->get('/api/group', 'GroupController@index');
$app->put('/api/group/{id}', 'GroupController@update');

//用户管理
//注册
$app->post('/api/user', 'UserController@store');
$app->group(['prefix' => '/api/user', 'middleware' => 'auth'], function($app) {
  //列出用户
  $app->get('/', 'UserController@index');
  //详情
  //$app->get('/{id}', 'UserController@show');
  //设置头像
  $app->post('/{id}/icon', 'UserController@setIcon');
  //保存用户信息
  $app->put('/{id}', 'UserController@update');
  //保存工位信息
  $app->put('/{id}/position', 'UserController@setPosition');  
  //删除
  $app->delete('/{id}', 'UserController@destroy');
});

//设备管理
//新建
$app->post('/api/device', 'DeviceController@store');
//状态
$app->get('/api/device/{id}', 'DeviceController@state');
$app->group(['prefix' => '/api/user/{id}/device', 'middleware' => 'auth'], function($app) {
  //通过审核
  $app->post('/{devId}/grant', 'DeviceController@grant');
  //修改
  $app->put('/{devId}', 'DeviceController@update');
  //删除
  $app->delete('/{devId}', 'DeviceController@destroy');
});

//数据管理
$app->group(['prefix' => '/api/user/{id}/device/{devId}/data', 'middleware' => 'auth'], function($app) {
  //列出
  $app->get('/', 'DataController@index');
  //详情
  $app->get('/{dataId}', 'DataController@show');
  //数据
  $app->get('/{dataId}/detail', 'DataController@detail');
  //下载
  $app->get('/{dataId}/download', 'DataController@download');
  //新建
  $app->post('/', 'DataController@store');
  //删除
  $app->delete('/{dataId}', 'DataController@destroy');
});
