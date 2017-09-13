<style>
.menu, .menu a {color: white}
ul.menu {
  border-radius: .5em;
  background-color: rgba(0, 0, 0, .5);
}
ul.menu, ul.drop {
  list-style: none;
  padding: 0;
  margin: 0;
}
.menu>li {
  float: left;
  position: relative;
}
.drop {
  display: none;
  position: absolute;
  left: 0;
  top: 2em;
  z-index: 100;
}
ul.drop {
  color: black;
  background-color: white;
  min-width: 100%;
  border: 1px outset;
}
.menu>li, .drop>li {
  padding: .5em;
  cursor: pointer;
}
.drop>li:hover {
  color: white;
  background-color: red
}
.menu>li:hover .drop {display: list-item}
</style>
<template>
  <ul class="menu">
    <template v-for="(mi, i) in menu">
      <li :key="i" v-if="typeof mi.condition == 'undefined' || mi.condition($parent)" @click="mi.onclick && mi.onclick($parent)"><span v-html="mi.caption"></span>
        <ul class="drop" v-if="mi.items">
          <template v-for="(di, j) in mi.items">
            <li :key="j" v-if="typeof di.condition == 'undefined' || di.condition($parent)" v-html="di.caption" @click="di.onclick && di.onclick($parent)"></li>
          </template>
        </ul>
      </li>
    </template>
  </ul>
</template>
<script>
export default {
  props: ['menu'],
}
</script>

