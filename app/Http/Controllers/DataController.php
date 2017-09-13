<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class DataController extends Controller
{
  //新建
  public function store(Request $request, $id) {

  }

  //删除
  public function destroy(Request $request, $id, $devId, $dataId) {
    $u = Auth::user();
    if(($u->id == $id || $u->groupId == 255) && ($dev = $u->device()->find($devId)) && ($d = $dev->data()->find($dataId))) {
      $d->delete();
      DB::statement('alter table data auto_increment=1');
    } else
      return response('', 401);
  }
}
