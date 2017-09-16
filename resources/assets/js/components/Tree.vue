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
  align-items: center;
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
.selection span, .selection a {
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
  <nav style="display: flex">
    <tree :items="tree.items" :icons="tree.icons" :selection="selection" v-show="show" :style="{width: wid}"></tree>
    <div class="split" :style="{cursor: show ? 'ew-resize' : 'pointer'}" :draggable="show" @click="toggle" @dragstart="ondragstart">{{show ? '<' : '>'}}</div>
  </nav>
</template>
<script>
import Vue from 'vue'

export default {
  props: ['tree', 'selection', 'width', 'shown'],
  components: {
    tree: {
      name: 'tree',
      template: `<ul class="tree">
  <li v-for="(t, i) in items" :key="i">
    <div :class="getClass(t, i)">
      <b v-if="t.items && t.items.length" style="cursor: pointer" @click="expand(i)">{{t.expand ? '-' : '+'}}</b>
      <b v-else></b>
      <img v-if="icon(t)" :src="icon(t)">
      <a v-if="t.href" :href="t.href" v-html="t.caption"></a>
      <span v-else v-html="t.caption"></span>
    </div>
    <tree v-if="t.items && t.items.length" class="leaf" :id="i" :items="t.items" :icons="icons" :selection="selection" v-show="t.expand" @expand="expand" @selected="selected"></tree>
  </li>
</ul>
`,
      props: ['id', 'items', 'icons', 'selection'],
      data() {
        return {
          selectionIndex: -1
        }
      },
      methods: {
        icon(t) {
          return this.icons && typeof t.icon != 'undefined' && this.icons[t.icon]
        },
        getClass(t, i) {
          let c = t.data == this.selection ? ['selection'] : null
          if(c && this.selection != i) {
            this.selectionIndex = i
            this.$emit('selected', this.id)
          }
          return c
        },
        expand(i) {
          Vue.set(this.items[i], 'expand', !this.items[i].expand)
        },
        selected(i) {
          this.selectionIndex = i
        }
      }
    }
  },
  data() {
    return {
      w: 150,
      s: document.documentElement.clientWidth > 600,
      selectionIndex: -1
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
    document[h]('dragover', e => e.dataTransfer.types[0] == 'tree' && e.preventDefault())
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
