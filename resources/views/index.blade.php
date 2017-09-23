@extends('layouts.app')
@section('content')
<style>
body {
  background-color: #777;
  max-width: 990px;
  margin: 0 auto;
}
.app {
  background-color: #bdb;
}
.header {
  text-align: center;
  width: 100%;
  height: 100px;
  position: relative;
  background: url(/acc/img/Header.jpg);
}
.title {
  color: yellow;
  font-size: 2em;
  font-weight: bold;
  word-wrap: none;
  line-height: 2.5em;
}
.menu {
  position: absolute;
  bottom: .2em;
  right: .5em;
}
</style>
<link rel="stylesheet" href="css/app.css">
<div id="app" class="app">
  <div class="header">
    <div class="title">上海同辆科技有限公司</div>
    <drop-menu v-if="admin" :menu="menu"></drop-menu>
  </div>
  <router-view :admin="admin" :users="$store.state.users"></router-view>
  <div style="text-align: center">联系电话: 021-51244254
    <a href="ftp/AccTest.apk">客户端下载</a>
  </div>
</div>
<script src="js/manifest.js"></script>
<script src="js/vendor.js"></script>
<script src="js/main.js"></script>
@endsection