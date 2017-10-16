@extends('layouts.app')
@section('content')
<style>
body {
  background-color: #777;
  max-width: 1000px;
  margin: 0 auto;
}
.app {background-color: #bdb}
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
  white-space: nowrap;
  line-height: 2.5em;
}
.menu {
  position: absolute;
  bottom: .2em;
  right: .5em;
}
.msg {
  background: white;
  padding: .2em .5em;
  border-radius: .5em;
  box-shadow: .2em .2em rgba(0, 0, 0, .5);
  display: inline-block;
}
.err {
  color: red;
  background-color: yellow;
}
</style>
<link rel="stylesheet" href="css/app.css">
<div id="app" class="app">
  <div class="header">
    <div class="title">上海同辆科技有限公司</div>
    <drop-menu v-if="admin" :menu="menu"></drop-menu>
  </div>
  <div v-show="$store.state.message" style="text-align: center; position: fixed; left: 0; right: 0; top: 110px; z-index: 100; display: none">
    <div :class="$store.state.error ? ['err'] : null" class="msg" v-html="$store.state.message"></div>
  </div>
  <router-view :admin="admin" :users="$store.state.users"></router-view>
  <footer style="text-align: center">
    <template v-if="option" v-show="option" style="display: none">
      <a :href="option.android.url">安卓客户端 版本: rev.@{{option.android.version}}</a>
      <a :href="option.windows.url">Windows客户端 版本: @{{option.windows.version}}</a><br>    
    </template>
    联系电话: 021-51244254
  </footer>
</div>
<script src="js/manifest.js"></script>
<script src="js/vendor.js"></script>
<script src="js/main.js"></script>
@endsection