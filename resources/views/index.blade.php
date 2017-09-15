@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="./css/app.css">
<div id="app" class="app">
  <router-view></router-view>
</div>
<script src="./js/manifest.js"></script>
<script src="./js/vendor.js"></script>
<script src="./js/main.js"></script>
@endsection