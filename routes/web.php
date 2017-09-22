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

//登录界面
//$app->get('auth/create', 'AuthController@create');
//登录
$app->post('/api/auth', 'AuthController@store');
//退出
$app->delete('/api/auth', ['middleware' => 'auth', 'uses' => 'AuthController@destroy']);

//用户管理
//注册
$app->post('/api/user', 'UserController@store');
$app->group(['prefix' => '/api/user', 'middleware' => 'auth'], function($app) {
  //列出用户
  $app->get('/', 'UserController@index');
  //注册界面
  //$app->get('/user/create', 'UserController@create');
  //详情
  $app->get('/{id}', 'UserController@show');
  //编辑界面
  //$app->get('/user/{id}/edit', 'UserController@edit');
  //保存
  $app->put('/{id}', 'UserController@update');
  //删除
  $app->delete('/{id}', 'UserController@destroy');
});

//组管理
$app->get('/api/group', 'GroupController@index');

//设备管理
//新建
$app->post('/api/device', 'DeviceController@store');
$app->get('/api/device/{id}', 'DeviceController@show');
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
  //新建
  $app->post('/', 'DataController@store');
  //删除
  $app->delete('/{dataId}', 'DataController@destroy');
});
