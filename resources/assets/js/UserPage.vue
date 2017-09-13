<template>
  <page-ctrl>
    <div style="display: flex">
      <tree :items="tree" :selected="$store.state.selected"></tree>
      <div style="flex-grow: 1; overflow: auto">
        <table class="datable" v-if="user">
          <caption style="position: relative">用户信息<a v-if="users" :href="'#/user/' + user.id + '/edit'" style="position: absolute; left: 0">编辑</a></caption>
          <thead>
            <tr><th>用户名</th><th>邮箱</th><th>用户组</th><th>设备数</th></tr>
          </thead>
          <tbody>
            <tr><td>{{user.name}}</td><td>{{user.email}}</td><td>{{user.groupName}}</td><td>{{user.devCount}}</td></tr>
          </tbody>
        </table>
        <router-view></router-view>
      </div>
    </div>
  </page-ctrl>
</template>
<script>
import Vue from 'vue'
import Tree from './components/Tree'
import Datable from './components/Datable'
import PageCtrl from './PageCtrl'

export default {
  components: {PageCtrl, Tree, Datable},
  props: ['user', 'users'],
  data() {
    return {
      tr: []
    }
  },
  computed: {
    tree() {
      if(this.users && !this.tr.length) {
        let p = this.$route.params
        for(let i = 0; i < this.users.length; i++) {
          let n = {
            caption: this.users[i].link,
            data: this.users[i],
            items: [],
          }
          if(this.users[i].id == p.id)
            n.expand = true
          for(let j = 0; j < this.users[i].device.length; j++) {
            let d = {
              caption: this.users[i].device[j].link,
              data: this.users[i].device[j],
            }
            n.items.push(d)
          }
          this.tr.push(n)
        }
      }
      return this.tr
    }
  }
}
</script>
