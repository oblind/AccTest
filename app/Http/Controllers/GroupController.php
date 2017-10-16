<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Group;

class GroupController extends Controller
{
  function getGroup($id, &$err) {
    $u = Auth::user();
    if($u->groupId == 255)
      if($g = Group::find($id))
        return $g;
      else
        $err = trans('error.groupNotFound');
    else
      $err = trans('error.noPermission');
  }

  //列出
  public function index() {
    $u = Auth::user();
    if($u->groupId == 255)
      return Group::all();
    else
      return [Group::find($u->groupId)];
  }

  //新建
  public function store(Request $request) {
  }

  //显示
  public function show(Request $request, $id) {
  }

  //修改
  public function update(Request $request, $id) {
    if($g = $this->getGroup($id, $err)) {
      $g->name = $request->name;
      $g->capacity = $request->capacity;
      $g->save();
    } else
      return response($err, 401);
  }
  
  //删除
  public function destroy(Request $request, $id) {
    if($g = $this->getGroup($id, $err))
      $g->delete();
    else
      return response($err, 401);
  }
}
