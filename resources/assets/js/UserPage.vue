<style>
.container {
  padding: 5px;
  flex-grow: 1;
}
.msg {
  background: white;
}
.err {
  color: red;
  background-color: yellow;
}
.msg, .err {
  display: inline-block;
  padding: .2em .5em;
  margin: 0 auto;
  border-radius: .5em;
}
</style>
<template>
  <div style="display: flex">
    <tree :tree="tree" :selection="$store.state.selection" :width="width" :shown="shown" @split="split"></tree>
    <div class="container">
      <div v-show="$store.state.message" style="text-align: center">
        <div :class="[$store.state.error ? 'err' : 'msg']">{{$store.state.message}}</div>
      </div>
      <div v-if="$route.name == 'users'">
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
      <router-view v-else class="container" :user="user"></router-view>
    </div>
  </div>
</template>
<script>
import cookie from 'js-cookie'
import Vue from 'vue'
import Tree from './components/Tree'
import Datable from './components/Datable'

export default {
  components: {Tree, Datable},
  props: ['admin', 'users'],
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
          token: {
            caption: '标识',
            condition() {
              return this.admin && this.admin.groupId == 255
            }
          },
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
      if(this.admin) {
        let id = this.$route.params.id
        if(this.admin.id == id)
          this.$store.state.curUser = this.admin
        else if(this.users)
          this.$store.state.curUser = this.users.find(u => u.id == id)
        if(this.$store.state.curUser) {
          if(this.$route.name == 'users' || this.$route.name == 'editUser')
            this.$store.state.selection = this.$store.state.curUser
          return this.$store.state.curUser
        } else
          setTimeout(() => this.$router.replace('/user/' + this.admin.id), 0)
      }
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
    if(this.user) {
      if(this.user.id != to.params.id) {
        this.$store.state.curUser = this.users.find(u => u.id == to.params.id)
      }
      if(to.name == 'users')
        this.$store.state.selection = this.$store.state.curUser
    }
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
