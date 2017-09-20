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
.tree span, .tree a {
  padding: 5px;
  flex-grow: 1;
  display: flex;
  align-items: center;
}
.tree b {
  width: 1em;
  text-align: center;
}
.split {
  background-color: yellow;
  cursor: pointer;
  width: .5em;
  height: 3em;
  line-height: 3em;
  align-self: center;
}
.leaf {margin-left: 1em}
.selection span, .selection a {
  color: white;
  background-color: #37f;
}
</style>
<template>
  <nav style="display: flex">
    <tree v-show="show" :style="{width: wid}" :tree="tree" :icons="tree.icons" :selection="selection"></tree>
    <div v-if="mobile" style="display: flex" :style="{cursor: show ? 'col-resize' : null}" @touchstart="touchstart">
      <div class="split" @click.stop="toggle" @mousedown.stop>{{show ? '<' : '>'}}</div>
    </div>
    <div v-else style="display: flex" :style="{cursor: show ? 'col-resize' : null}" @mousedown="touchstart">
      <div class="split" @click.stop="toggle" @mousedown.stop>{{show ? '<' : '>'}}</div>
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
      <a v-if="t.tn.href" :href="t.tn.href" v-html="t.tn.caption"></a>
      <span v-else v-html="t.tn.caption"></span>
    </div>
    <tree v-if="t.items && t.items.length" class="leaf" :id="i" :tree="t" :icons="icons" :selection="selection" v-show="t.expand" @expand="expand" @selected="selected"></tree>
  </li>
</ul>
`,
      props: ['id', 'tree', 'icons', 'selection'],
      data() {
        return {
          selectionIndex: -1
        }
      },
      computed: {
        items() {
          if(this.tree.items && this.tree.items.find(e => e == this.selection))
            Vue.set(this.tree, 'expand', true)
          return this.tree.items
        }
      },
      methods: {
        icon(t) {
          return this.icons && typeof t.tn.icon != 'undefined' && this.icons[t.tn.icon]
        },
        getClass(t, i) {
          let c = t == this.selection ? ['selection'] : null
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
      sset: false,
      selectionIndex: -1
    }
  },
  computed: {
    mobile() {
      return /Mobile/.test(navigator.userAgent)
    },
    wid() {
      if(!this.move && this.width)
        this.w = this.width
      return this.w + 'px'
    },
    show() {
      if(this.sset)
        this.sset = false
      else if(typeof this.shown != 'undefined')
        this.s = this.shown
      return this.s
    }
  },
  methods: {
    toggle() {
      this.s = !this.s
      this.sset = true
      this.$emit('split', this.width, this.s)
    },
    touchstart(e) {
      if(this.show) {
        this.move = true
        let t = e.targetTouches ? e.targetTouches[0] : e
        this.x0 = t.pageX
        this.w0 = this.width || this.w
      }
    }
  },
  mounted() {
    let e = this.mobile ? {
      touchmove: 'touchmove',
      touchend: 'touchend'
    } : {
      touchmove: 'mousemove',
      touchend: 'mouseup'
    }
    document.addEventListener(e.touchmove, e => {
      if(this.move) {
        e.preventDefault()
        let t = e.targetTouches ? e.targetTouches[0] : e
        this.w = this.w0 + t.pageX - this.x0
      }
    }, {passive: false})
    document.addEventListener(e.touchend, e => {
      if(this.move) {
        this.move = false
        this.$emit('split', this.w, this.s)
      }
    })
  }
}
</script>
