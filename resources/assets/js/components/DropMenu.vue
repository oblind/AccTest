<style>
.menu, .menu a {color: white}
ul.menu {
  display: flex;
  align-items: center;
  border-radius: .5em;
  background-color: rgba(0, 0, 0, .5);
}
ul.menu, ul.drop {
  list-style: none;
  padding: 0;
  margin: 0;
}
.menu li {position: relative}
.menu img {
  max-width: 24px;
  max-height: 24px;
}
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
.menu>li, .menu>li>a, .drop>li, .drop>li>a {
  display: flex;
  align-items: center;
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
      <li :key="i" v-if="typeof mi.condition == 'undefined' || mi.condition($parent)" @click="mi.onclick && mi.onclick.call($parent)">
        <a v-if="mi.href" :href="mi.href"><img v-if="mi.icon" :src="mi.icon">{{mi.caption}}</a>
        <template v-else>
          <img v-if="mi.icon" :src="mi.icon"><span v-html="mi.caption"></span>
        </template>
        <mi v-if="mi.items" :items="mi.items" :root="$parent"></mi>
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
      props: ['items', 'root'],
      template: `<ul class="drop">
  <template v-for="(mi, i) in items">
    <li :key="i" v-if="typeof mi.condition == 'undefined' || mi.condition($parent)" @click.stop="mi.onclick && mi.onclick.call(root)">
      <a v-if="mi.href" :href="mi.href">{{mi.caption + (mi.items ? ' >' : '')}}</a>
      <span v-else v-html="mi.caption + (mi.items ? ' >' : '')"></span>
      <mi v-if="mi.items" :items="mi.items" :root="root"></mi>
    </li>
  </template>
</ul>
`,
    }
  }
}
</script>

