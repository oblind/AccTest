<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Group;

class GroupController extends Controller
{
  //列出
  public function index() {
    $u = Auth::user();
    if($u->groupId == 255)
      return Group::all();
    else
      return response('', 401);
  }

  //新建
  public function store(Request $request) {
  }

  //显示
  public function show(Request $request, $id) {
  }

  //修改
  public function update(Request $request, $id, $devId) {
  }
  
  //删除
  public function destroy(Request $request, $id, $devId) {
  }
}
