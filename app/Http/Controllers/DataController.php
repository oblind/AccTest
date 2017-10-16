<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use App\User;
use App\Device;
use App\Data;

class DataController extends Controller
{
  function getDevice($id, $devId, &$err) {
    $u = Auth::user();
    if(($u->id == $id || $u->groupId == 255 && ($u = User::find($id))) && ($dev = $u->device()->find($devId)))
      return $dev;
    else
      $err = response(trans('error.noPermission'), 401);
  }

  function getData($id, $devId, $dataId, &$err) {
    if(($d = $this->getDevice($id, $devId, $err)) && ($d = $d->data()->find($dataId)))
      return $d;
    else
      $err = response(trans('error.noPermission'), 401);
  }

  //新建
  public function store(Request $request, $id, $devId) {
    if($d = $this->getDevice($id, $devId, $err)) {
      if(($f = $request->file) && $f->isValid()) {
        if(strtolower($f->getClientOriginalExtension()) == 'pwx') {
          $d = new Data();
          $d->deviceId = $devId;
          $d->filename = $f->getClientOriginalName();
          $d->fileSize = filesize($f);
          $d->created_at = date('Y-m-d H:i:s');
          $d->file = file_get_contents($f);
          $d->save();
          return $d;
        } else
          return response(trans('error.fileFormatError'), 401);
      } else
        return response(trans('error.transferFailed'), 401);
    } else
      return $err;
  }

  //列出
  public function index(Request $request, $id, $devId) {
    if($d = $this->getDevice($id, $devId, $err))
      return $d->data()->get();
    else
      return $err;
  }

  //详情
  public function show(Request $request, $id, $devId, $dataId) {
    if($d = $this->getData($id, $devId, $dataId, $err))
      return $d;
    else
      return $err;
  }

  //数据
  public function detail(Request $request, $id, $devId, $dataId) {
    if($d = $this->getData($id, $devId, $dataId, $err))
      return $d->detail();
    else
      return $err;
  }

  //下载
  public function download(Request $request, $id, $devId, $dataId) {
    if($d = $this->getData($id, $devId, $dataId, $err)) {
      $r = response($d->file);
      $r->header('Content-Disposition', 'attachment; filename=' . $d->filename);
      $r->header('Content-Length', $d->fileSize);
      return $r;
    } else
      return $err;
  }
  
  //删除
  public function destroy(Request $request, $id, $devId, $dataId){
    if($d = $this->getData($id, $devId, $dataId, $err)) {
      $d->delete();
      DB::statement('alter table data auto_increment=1');
    } else
      return $err;
  }
}
