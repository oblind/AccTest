<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use App\User;

class DeviceController extends Controller
{
  //新建
  public function store(Request $request, $id) {

  }

  //通过审核
  public function grant(Request $request, $id, $devId) {
    $u = Auth::user();
    if($u->groupId == 255 && ($d = User::find($id)->device()->find($devId))) {
      $d->state = 1;
      $d->save();
    } else
      return response('', 401);
  }

  //删除
  public function destroy(Request $request, $id, $devId) {
    $u = Auth::user();
    if(($u->id == $id || $u->groupId == 255) && ($d = User::find($id)->device()->find($devId))) {
      $d->delete();
      DB::statement('alter table devices auto_increment=1');
    } else
      return response('', 401);
    
  }
}
