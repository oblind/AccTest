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
</style>
<template>
  <nav style="display: flex">
    <tree :items="tree.items" :icons="tree.icons" :selection="selection" v-show="show" :style="{width: wid}"></tree>
    <div style="width: .5em; display: flex" :style="{cursor: show ? 'col-resize' : null}" @mousedown="mousedown">
      <div style="background-color: yellow; cursor: pointer; height: 3em; line-height: 3em; align-self: center" @click="toggle" @mousedown.stop>{{show ? '<' : '>'}}</div>
    </div>
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
      move: false,
      w: 150,
      s: document.documentElement.clientWidth > 600,
      selectionIndex: -1
    }
  },
  computed: {
    wid() {
      return (this.move || !this.width ? this.w : this.width) + 'px'
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
    mousedown(e) {
      if(this.show) {
        this.move = true
        this.x0 = e.pageX
        this.w0 = this.width || this.w
      }
    }
  },
  mounted() {
    let h = document.addEventListener ? 'addEventListener' : 'attachEvent'
    document[h]('mouseup', e => {
      if(this.move) {
        this.move = false
        this.$emit('split', this.w, this.s)
      }
    })
    document[h]('mousemove', e => {
      if(this.move)
        this.w = this.w0 + e.pageX - this.x0
    })
  }
}
</script>
