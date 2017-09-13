<style>
.header {
  text-align: center;
  width: 100%;
  height: 100px;
  position: relative;
  overflow: hidden;
  background: url(/acc/img/Header.jpg);
}
.title {
  color: yellow;
  font-size: 2em;
  font-weight: bold;
  line-height: 2.5em;
}
.menu {
  position: absolute;
  bottom: .2em;
  right: .5em;
}
</style>
<template>
  <div>
    <div class="header">
      <div class="title">上海同辆科技有限公司</div>
      <drop-menu v-if="user" :menu="menus"></drop-menu>
    </div>
    <slot></slot>
  </div>
</template>
<script>
import Vue from 'vue'
import DropMenu from './components/DropMenu'

export default {
  components: {DropMenu},
  props: ['menu'],
  computed: {
    user() {
      return this.$store.state.user
    },
    menus() {
      let m = [
        {
          caption: '退出',
          onclick(e) {
            e.$store.commit('logout')
          }
        }
      ]
      if(this.menu) m.unshift(...this.menu)
      return m
    }
  },
}
</script>
