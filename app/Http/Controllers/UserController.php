<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use DB;
use App\User;

class UserController extends Controller
{
  function getUser($id, &$err) {
    $u = Auth::user();
    if($u->id == $id || $u->groupId == 255 && $u = User::find($id))
      return $u;
    else
      $err = trans('error.userNotFound');
  }

  public function index() { //列出用户
    return Auth::user()->detail();
}
  
  /*public function create() {//注册界面
  }*/

  public function store(Request $request) {//注册，新建用户
    if($u = User::register($request->all(), $err))
      return response($u->detail())->withCookie(Cookie::make('token', $u->id, 30, null, null, false, false));  //保存30分钟
    else
      return response($err, 401);
  }

  /*public function show(Request $request, $id) { //详情
    if($u = $this->getUser($id, $err))
      return $u->detail();
    else
      return response($err, 401);
  }*/

  public function setIcon(Request $request, $id) { //设置头像
    if($u = $this->getUser($id, $err))
      if(($f = $request->file) && $f->isValid()) {
        if($u->saveIcon($f, $err))
          return;
      } else
        $err = trans('error.transferFailed');
    return response($err, 401);
  }

  public function setPosition(Request $request, $id) { //设置工位
    if($u = $this->getUser($id, $err)) {
      $u->info = json_encode($request->json()->all());
      $u->save();
    } else
      return response($err, 401);
  }

  public function update(Request $request, $id) { //保存
    if($u = $this->getUser($id, $err)) {
      $u->name = $request->get('name');
      $a = Auth::user();
      if($a->groupId == 255 && $a->id != $u->id)
        $u->groupId = $request->get('groupId');
      $u->save();
      return ['name' => $u->name, 'groupId' => $u->groupId];
    } else
      return response($err, 401);  
  }

  public function destroy(Request $request, $id) {  //删除
    $u = Auth::user();
    if($u->groupId == 255 && $u->id != $id) {
      if($u = User::find($id)) {
        $u->delete();
        DB::statement('alter table users auto_increment=1');
      } else
        return response(trans('error.userNotFound'), 401);
    } else
      return response(trans('error.noPermission'), 401);
  }
}
