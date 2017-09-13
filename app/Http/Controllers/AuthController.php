<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\User;

class AuthController extends BaseController
{
  public function store(Request $request) {
    if($u = User::login($request->all(), $err))
      return response($u)->withCookie(Cookie::make('token', $u->id,
        $request->get('remember_me') ? 1440 * 7 : 30, null, null, false, false));  //保存7天
    else
      return response($err, 401);
  }

  public function destroy() {
    return response('')->withCookie(Cookie::make('token', 0, -60)); //退出
  }
}
