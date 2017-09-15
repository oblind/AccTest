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
.menu>li {float: left}
.menu li {position: relative}
.menu span {
  display: flex;
  align-items: center;
}
.drop {
  display: none;
  position: absolute;
  left: 100%;
  top: 0;
  z-index: 100;
}
.menu>li>.drop {
  position: absolute;
  left: 0;
  top: 100%;
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
  background-color: #37f;
}
.menu>li:hover>.drop, .drop>li:hover>.drop {display: list-item}
</style>
<template>
  <ul class="menu">
    <template v-for="(mi, i) in menu">
      <li :key="i" v-if="typeof mi.condition == 'undefined' || mi.condition($parent)" @click="mi.onclick && mi.onclick($parent)"><span v-html="mi.caption"></span>
        <mi v-if="mi.items" :items="mi.items"></mi>
      </li>
    </template>
  </ul>
</template>
<script>
export default {
  props: ['menu'],
  components: {
    mi: {
      name: 'mi',
      props: ['items'],
      template: `<ul class="drop">
  <template v-for="(mi, i) in items">
    <li :key="i" v-if="typeof mi.condition == 'undefined' || mi.condition($parent)" @click="mi.onclick && mi.onclick($parent)"><span v-html="mi.caption + (mi.items ? ' >' : '')"></span>
      <mi v-if="mi.items" :items="mi.items"></mi>
    </li>
  </template>
</ul>
`,
    }
  }
}
</script>

