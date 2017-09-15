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
  flex-grow: 1;
  display: flex;
}
.tree a {
  padding: 5px;
  flex-grow: 1;
  display: flex;
}
.tree b {
  width: 1em;
  text-align: center;
}
.leaf {margin-left: 1em}
.selected span, .selected a {
  color: white;
  background-color: #37f;
}
.split {
  background-color: yellow;
  width: .5em;
  height: 5em;
  line-height: 5em;
  align-self: center;
}
</style>
<template>
  <div style="display: flex">
    <tree :items="items" :selected="selected" v-show="show" :style="{width: wid}"></tree>
    <div class="split" :style="{cursor: show ? 'ew-resize' : 'pointer'}" :draggable="show" @click="toggle" @dragstart="ondragstart">{{show ? '<' : '>'}}</div>
  </div>
</template>
<script>
import Vue from 'vue'

export default {
  props: ['items', 'selected', 'width', 'shown'],
  components: {
    tree: {
      name: 'tree',
      template: `<ul class="tree">
  <li v-for="(t, i) in items" :key="i">
    <div :class="t.data == selected ? ['selected'] : null">
      <b v-if="t.items && t.items.length" style="cursor: pointer" @click="expand(i)">{{t.expand ? '-' : '+'}}</b>
      <b v-else></b>
      <a v-if="t.href" :href="t.href">{{t.caption}}</a>
      <span v-else v-html="t.caption"></span>
    </div>
    <tree v-if="t.items && t.items.length" class="leaf" :items="t.items" :selected="selected" v-show="t.expand" @expand="expand"></tree>
  </li>
</ul>
`,
      props: ['items', 'selected'],
      methods: {
        expand(i) {
          Vue.set(this.items[i], 'expand', !this.items[i].expand)
        },
      }
    }
  },
  data() {
    return {
      w: 150,
      s: document.documentElement.clientWidth > 600,
    }
  },
  computed: {
    wid() {
      return (this.width || this.w) + 'px'
    },
    show() {
      return typeof this.shown == 'undefined' ? this.s : this.shown
    }
  },
  methods: {
    toggle() {
      this.s = !this.s
      this.$emit('split', this.width, this.s)
    },
    ondragstart(e) {
      e.dataTransfer.effectAllowed = 'move'
      e.dataTransfer.setData('tree', this.id)
      this.x0 = e.pageX
    }
  },
  mounted() {
    let h = document.addEventListener ? 'addEventListener' : 'attachEvent'
    let vm = this
    this.id = Math.floor(Math.random() * 1000)
    document[h]('dragover', e => {
      e.preventDefault()
    })
    document[h]('drop', e => {
      if(e.dataTransfer.getData('tree') == vm.id) {
        let w = (vm.width || vm.w) + e.pageX - vm.x0
        if(w < 70)
          vm.s = false
        else
          vm.w = w
        vm.$emit('split', vm.w, vm.s)
      }
    })
  }
}
</script>
