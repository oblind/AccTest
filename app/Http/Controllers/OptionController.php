<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Option;

class OptionController extends Controller
{
  public static function hasPermission(&$err) {
    $u = Auth::user();
    if($u->groupId == 255)
      return true;
    else {
      $err = trans('error.noPermission');
      return false;
    }
  }

  //列出
  public function index() {
    $o = Option::first();
    return [
      'android' => [
        'version' => $o->androidVersion,
        'url' => $o->androidUrl
      ],
      'windows' => [
        'version' => $o->windowsVersion,
        'url' => $o->windowsUrl
      ],
    ];
  }

  //更新
  public function update(Request $request) {
    if(static::hasPermission($err)) {
      $o = Option::first();
      $r = $request->json()->all();
      $o->androidVersion = $r['android']['version'];
      $o->androidUrl = $r['android']['url'];
      $o->windowsVersion = $r['windows']['version'];
      $o->windowsUrl = $r['windows']['url'];
      $o->save();
    } else
      return response($err, 401);
  }

  public function androidVersion() {
    $o = Option::first();
    return ['version' => $o->androidVersion, 'url' => $o->androidUrl];
  }

  public function windowsVersion() {
    $o = Option::first();
    return ['version' => $o->windowsVersion, 'url' => $o->windowsUrl];
  }
}
