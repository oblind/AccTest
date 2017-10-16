@extends('layouts.app')
@section('content')
<div id="app">frame
  <template v-for="i in 5">
    <router-link :to="'/user/' + i" :key="i">@{{i}}</router-link><br>
  </template>
  <router-view></router-view>
</div>
<script src="js/manifest.js"></script>
<script src="js/vendor.js"></script>
<script src="js/test.js"></script>
@endsection