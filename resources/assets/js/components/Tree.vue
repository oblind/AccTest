<style>
ul.tree {
  list-style: none;
  padding: 0;
  margin-top: 0;
  margin-bottom: 0;
  background-color: white;
  flex-grow: 1;
  overflow: auto;
}
.tree div {
  display: flex;
  align-items: center;
}
.tree span {
  padding: 5px;
  flex-grow: 1;
  display: flex;
}
.tree a {flex-grow: 1}
.tree b {padding: 5px}
.leaf {margin-left: 1em}
.selected span, .selected a {
  color: white;
  background-color: blue;
}
</style>
<template>
  <div style="display: flex">
    <tree :items="items" :selected="selected" v-show="nav" :style="{width: width || '150px'}"></tree>
    <div style="background-color: yellow; width: .5em; height: 5em; line-height: 5em; align-self: center; cursor: pointer" @click="nav = !nav">{{nav ? '<' : '>'}}</div>
  </div>
</template>
<script>
import Vue from 'vue'

export default {
  props: ['items', 'selected', 'width'],
  components: {
    tree: {
      name: 'tree',
      template: `<ul class="tree">
  <li v-for="(t, i) in items" :key="i">
    <div :class="t.data == selected ? ['selected'] : null">
      <b v-if="t.items && t.items.length" style="cursor: pointer" @click="expand(i)">{{t.expand ? '-' : '+'}}</b>
      <b v-else>.</b>
      <span v-html="t.caption"></span>
    </div>
    <tree v-if="t.items && t.items.length" class="leaf" :items="t.items" :selected="selected" v-show="t.expand" @expand="expand"></tree>
  </li>
</ul>
`,
      inheritAttrs: false,
      props: ['items', 'selected'],
      data() {
        return {
          exp: []
        }
      },
      methods: {
        expand(i) {
          Vue.set(this.items[i], 'expand', !this.items[i].expand)
        },
      }
    }
  },
  data() {
    return {
      nav: document.documentElement.clientWidth > 600,
    }
  },
}
</script>
