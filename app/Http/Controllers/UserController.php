<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\User;

class UserController extends BaseController
{
  public function index() //列出用户
  {
    $u = Auth::user();
    if($u->groupId == 255)
      return User::with('group', 'device', 'device.data')->get();
    else
      return response('', 401);
  }
  
  /*public function create()//注册界面
  {
  }*/

  public function store(Request $request) //注册
  {
    if($u = User::register($request->all(), $err)) {
      return response('ok')->withCookie(Cookie::make('token', $u->id, 1440 * 7));  //保存7天
    } else
      return response($err, 401);
  }

  public function show(Request $request, $id)  //详情
  {
    $u = Auth::user();
    if($u->id == $id || $u->groupId == 255)
      return $u->detail();
    else
      return response('', 401);
  }
  
  /*public function edit()  //编辑界面
  {
  }*/

  public function update(Request $request, $id)  //保存
  {
    $u = Auth::user();
    if($id == $u->id || $u->groupId == 255) {

    } else
      return response('', 401);  
  }

  public function destroy(Request $request, $id) //删除
  {
    if(Auth::user()->groupId == 255) {

    } else
      return response('', 401);  
  }
}
