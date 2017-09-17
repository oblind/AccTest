<template>
  <page-ctrl>
    <div style="display: flex">
      <tree :tree="tree" :selection="$store.state.selection" :width="width" :shown="shown" @split="split"></tree>
      <div style="padding: 5px; flex-grow: 1; overflow: auto">
        <table class="datable" v-if="user">
          <caption style="position: relative">用户信息
            <template v-if="users">
              <u v-if="$route.name == 'editUser'" style="position: absolute; left: 0; cursor: pointer" @click="$router.go(-1)">返回</u>
              <a v-else :href="'#/user/' + user.id + '/edit'" style="position: absolute; left: 0">编辑</a>
            </template>
          </caption>
          <thead>
            <tr><th>用户名</th><th>邮箱</th><th>用户组</th><th>设备数</th></tr>
          </thead>
          <tbody>
            <tr><td>{{user.name}}</td><td>{{user.email}}</td><td>{{user.groupName}}</td><td>{{user.devCount}}</td></tr>
          </tbody>
        </table>
        <datable v-if="$route.name == 'users'" :tbl="tbl" :data="user && user.device"></datable>
        <router-view v-else></router-view>
      </div>
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
        items: [],
        icons: [
          'img/user16.png',
          'img/phone16.png'
        ]
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
      if(this.users && !this.tr.items.length) {
        let p = this.$route.params
        for(let i = 0; i < this.users.length; i++) {
          let n = {
            caption: this.users[i].name,
            href: this.users[i].href,
            data: this.users[i],
            icon: 0,
            items: [],
          }
          if(this.users[i].id == p.id)
            n.expand = true
          for(let j = 0; j < this.users[i].device.length; j++) {
            let d = {
              caption: this.users[i].device[j].name,
              href: this.users[i].device[j].href,
              data: this.users[i].device[j],
              icon: 1
            }
            n.items.push(d)
          }
          this.tr.items.push(n)
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
