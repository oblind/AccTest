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
.tree img {
  max-width: 24px;
  max-height: 24px;
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
.split {display: flex}
.split:hover {background-color: rgba(0, 0, 0, .3)}
.split-btn {
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
    <tree v-show="show" :style="{width: wid}" :tree="tree" :icons="tree.icons" :selection="sele" @itemSelect="itemSelect"></tree>
    <div v-if="mobile" class="split" :style="{cursor: show ? 'col-resize' : null}" @touchstart="touchstart">
      <div class="split-btn" @click.stop="toggle" @mousedown.stop>{{show ? '<' : '>'}}</div>
    </div>
    <div v-else class="split" :style="{cursor: show ? 'col-resize' : null}" @mousedown="touchstart">
      <div class="split-btn" @click.stop="toggle" @mousedown.stop>{{show ? '<' : '>'}}</div>
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
  <li v-for="(t, i) in aitems" :key="i">
    <div :class="t == selection ? ['selection'] : null">
      <b v-if="t.items && t.items.length" style="cursor: pointer" @click="expand(i, !t.tn.expand)">{{t.tn.expand ? '-' : '+'}}</b>
      <b v-else></b>
      <img v-if="icon(t)" :src="icon(t)">
      <a v-if="t.tn.href" :href="t.tn.href" v-html="t.tn.caption" @click.stop="select(t, i)"></a>
      <span v-else v-html="t.tn.caption" @click.stop="select(t, i)"></span>
    </div>
    <tree v-if="t.items && t.items.length" class="leaf" :id="i" :tree="t" :icons="icons" :selection="selection" v-show="t.tn.expand" @itemSelect="itemSelect" @expand="expand"></tree>
  </li>
</ul>
`,
      props: ['id', 'tree', 'icons', 'selection'],
      data() {
        return {
          it: null,
          selectionIndex: -1,
        }
      },
      computed: {
        aitems() {
          if(!this.it)
            this.it = this.tree.items
          return this.it
        }
      },
      methods: {
        icon(t) {
          return t.icon || this.icons && typeof t.tn.iconIndex != 'undefined' && this.icons[t.tn.iconIndex]
        },
        expand(i, t) {
          this.it[i].tn.expand = t
          Vue.set(this.it, i, this.it[i])
        },
        select(t, i) {
          let path = [i]
          if(typeof this.id != 'undefined')
            path.unshift(this.id)
          this.$emit('itemSelect', t, path)
        },
        itemSelect(t, path) {
          this.selectionIndex = path[0]
          if(typeof this.id != 'undefined')
            path.unshift(this.id)
          this.$emit('itemSelect', t, path)
        }
      }
    }
  },
  data() {
    return {
      sel: null,
      move: false,
      w: 150,
      s: document.documentElement.clientWidth > 600,
      sset: false,
      selectionIndex: -1
    }
  },
  computed: {
    mobile() {
      return /Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)
    },
    sele() {
      if(this.selection) this.sel = this.selection
      return this.sel
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
    itemSelect(t, path) {
      this.sel = t
      this.selectionIndex = path[0]
      this.$emit('itemSelect', t, path)
    },
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
