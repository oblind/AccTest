<style>
.container {
  padding: 5px;
  flex-grow: 1;
}
</style>
<template>
  <page-ctrl>
    <div style="display: flex">
      <tree :tree="tree" :selection="$store.state.selection" :width="width" :shown="shown" @split="split"></tree>
      <div v-if="$route.name == 'users'" class="container">
        <table class="datable" v-if="user">
          <caption style="position: relative">用户信息
            <template v-if="users">
              <a :href="'#/user/' + user.id + '/edit'" style="position: absolute; left: 0">编辑</a>
            </template>
          </caption>
          <thead>
            <tr><th>用户名</th><th>邮箱</th><th>用户组</th><th>设备数</th></tr>
          </thead>
          <tbody>
            <tr><td>{{user.name}}</td><td>{{user.email}}</td><td>{{user.groupName}}</td><td>{{user.device.length + '/' + user.group.capacity}}</td></tr>
          </tbody>
        </table>
        <datable :tbl="tbl" :data="user && user.device"></datable>
      </div>
      <router-view v-else class="container"></router-view>
    </div>
  </page-ctrl>
</template>
<script>
import cookie from 'js-cookie'
import Vue from 'vue'
import Tree from './components/Tree'
import Datable from './components/Datable'
import PageCtrl from './PageCtrl'

export default {
  components: {PageCtrl, Tree, Datable},
  data() {
    return {
      tr: {
        icons: [
          'img/user16.png',
          'img/phone16.png'
        ],
        items: null
      },
      width: 150,
      shown: true,
      tbl: {
        caption: '设备列表',
        columns: {
          name: {
            caption: '名称',
            href: 'href'
          },
          token: '标识',
          created_at: '注册日期',
          state: {
            caption: '状态',
            items: ['待审核', '正常']
          }
        }
      }
    }
  },
  computed: {
    user() {
      return this.$store.state.curUser
    },
    users() {
      return this.$store.state.users
    },
    tree() {
      if(this.users) {
        this.tr.items = this.users
        let p = this.$route.params
        for(let i = 0; i < this.users.length; i++) {
          let n = this.users[i]
          n.tn = {
            caption: n.name,
            href: n.href,
            icon: 0
          }
          n.items = n.device
          for(let j = 0; j < n.device.length; j++) {
            let d = n.device[j]
            d.tn = {
              caption: d.name,
              href: d.href,
              icon: 1
            }
          }
        }
      }
      return this.tr
    }
  },
  methods: {
    split(width, shown) {
      this.width = width
      this.shown = shown
      cookie.set('tree', {width, shown})
    }
  },
  beforeRouteUpdate(to, from, next) {
    if(this.user.id != to.params.id) {
      this.$store.state.curUser = this.users.find(u => u.id == to.params.id)
    }
    if(to.name == 'users')
      this.$store.state.selection = this.user
    next()
  },
  mounted() {
    let t = cookie.get('tree')
    if(t) {
      t = JSON.parse(t)
      this.width = t.width
      this.shown = t.shown
    }
  }
}
</script>
