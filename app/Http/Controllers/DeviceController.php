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
    $d->save();
    return [
      'id' => $d->id,
      'state' => $d->state,
      'info' => json_decode($u->info)
    ];
  }

  //状态
  public function state(Request $request, $id) {
    if($d = Device::find($id)) {
      $u = User::find($d->userId);
      return ['state' => $d->state, 'info' => json_decode($u->info)];
    } else
      return response(['error' => trans('error.deviceNotFound')], 401);
  }

  function getDevice($id, $devId, &$u, &$err) {
    $u = Auth::user();
    if($u->id == $id || $u->groupId == 255 && ($u = User::find($id)))
      if($dev = $u->device()->find($devId))
        return $dev;
      else
        $err = trans('error.deviceNotFound');
    else
      $err = response(trans('error.noPermission'), 401);
  }

  //通过审核
  public function grant(Request $request, $id, $devId) {
    if($d = $this->getDevice($id, $devId, $u, $err)) {
      if(($d = $u->device())->where('state', '=', 1)->count() >= $u->group()->first()->capacity)
        return response(trans('error.deviceOverproof'), 401);
      $d = $u->device()->find($devId);
      $d->state = 1;
      $d->save();
    } else
      return $err;
  }

  //修改
  public function update(Request $request, $id, $devId) {
    if($d = $this->getDevice($id, $devId, $u, $err)) {
      $d->name = $request->name;
      $d->save();      
    } else
      return $err;
  }
  
  //删除
  public function destroy(Request $request, $id, $devId) {
    if($d = $this->getDevice($id, $devId, $u, $err)) {
      if(!$d->data()->count()) {
        $d->delete();
        DB::statement('alter table devices auto_increment=1');
      } else
        return response(trans('error.clearDataFirst'), 401);  
    } else
      return $err;
  }
}
