<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use App\User;
use App\Device;

class DeviceController extends Controller
{
  //新建
  public function store(Request $request) {
    //管理员不存在
    if(!$u = User::where('email', $request->get('email'))->first())
      return response(['error' => trans('error.noAdmin')], 401);
    //新设备
    if(!$d = Device::where('token', $request->get('token'))->first()) {
      $d = new Device;
      $d->userId = $u->id;
      $d->token = $request->get('token');
      $d->state = 0;
      $d->created_at = date('Y-m-d H:i:s');
    } elseif($d->userId != $u->id) {
      $d->userId = $u->id;
      $d->state = 0;
    }
    $d->name = $request->get('name');
    $d->userName = $request->get('userName');
    $d->save();
    return response(['id' => $d->id, 'state' => $d->state]);
  }

  public function show(Request $request, $id) {
    if($d = Device::find($id))
      return response(['state' => $d->state]);
    else return response(['error' => trans('error.notFound')], 401);
  }

  //通过审核
  public function grant(Request $request, $id, $devId) {
    $u = Auth::user();
    if($u->id != $id) {
      if($u->groupId == 255)
        $u = User::find($id);
      else
        return response(trans('error.noPermission'), 401);
    }
    if(($d = $u->device())->count() >= $u->group()->first()->capacity)
      return response(trans('error.deviceOverproof'), 401);
    $d = $u->device()->find($devId);
    $d->state = 1;
    $d->save();
  }

  //修改
  public function update(Request $request, $id, $devId) {
    $u = Auth::user();
    if(($u->id == $id || $u->groupId == 255) && ($d = User::find($id)->device()->find($devId))) {
      $d->name = $request->name;
      $d->save();
      return $d->with('data')->first();
    } else
      return response('error.noPermission', 401);
  }
  
  //删除
  public function destroy(Request $request, $id, $devId) {
    $u = Auth::user();
    if(($u->id == $id || $u->groupId == 255) && ($d = User::find($id)->device()->find($devId))) {
      $d->delete();
      DB::statement('alter table devices auto_increment=1');
    } else
      return response('error.noPermission', 401);
  }
}
