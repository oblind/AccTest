<style>
.container {
  flex-grow: 1;
  position: relative;
  overflow: hidden;
}
</style>
<template>
  <div style="display: flex">
    <tree :tree="tree" :selection="$store.state.selection" :width="width" :shown="shown" @itemSelect="itemSelect" @split="split"></tree>
    <template v-if="$route.name == 'user'">
      <div v-if="user" class="container">
        <img :src="user.icon" style="display: block; margin: .2em auto 0 auto">
        <table class="datable">
          <caption style="position: relative">用户信息
            <a :href="'#/user/' + user.id + '/edit'" class="act">编辑</a>
          </caption>
          <thead>
            <tr><th>用户名</th><th>邮箱</th><th>用户组</th><th>设备数</th></tr>
          </thead>
          <tbody>
            <tr><td>{{user.name}}</td><td>{{user.email}}</td><td>{{user.groupName}}</td><td>{{user.device.filter(d => d.state == 1).length + '/' + user.device.length + '/' + user.group.capacity}}</td></tr>
          </tbody>
        </table>
        <table class="datable">
          <caption>车辆信息</caption>
          <thead>
            <tr><th>车号</th><th>车厢数</th><th>工位</th></tr>
          </thead>
          <tbody>
            <tr><td>{{train}}</td><td>{{user.info.count}}</td><td>{{user.info.pos}}</td></tr>
          </tbody>
        </table>
        <datable :tbl="tbl" :data="user.device"></datable>
      </div>
    </template>
    <router-view v-else :user="user" class="container"></router-view>
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
            'img/question16.png',
            'img/phone16.png'
        ],
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
      let id = this.$route.params.id
      if(!this.$store.state.curUser || this.$store.state.curUser.id != id) {
        this.$store.state.curUser = this.users.find(u => u.id == id)
        if(!this.$store.state.curUser) {
          this.$store.state.curUser = this.admin
          this.$store.state.selection = this.admin
          this.$router.replace('/user/' + this.admin.id)
        } else if(this.$route.name == 'user' || this.$route.name == 'editUser')
          this.$store.state.selection = this.$store.state.curUser
      }
      return this.$store.state.curUser
    },
    tree() {
      let p = this.$route.params
      for(let i = 0; i < this.users.length; i++) {
        let n = this.users[i]
        if(!n.tn)
          n.tn = {}
        n.tn.caption = n.name
        n.tn.href = n.href
        n.tn.icon = n.icon
        n.tn.iconIndex = 0
        n.items = n.device
        for(let j = 0; j < n.device.length; j++) {
          let d = n.device[j]
          if(!d.tn)
            d.tn = {}
          d.tn.caption = d.name,
          d.tn.href = d.href,
          d.tn.iconIndex = d.state ? 2 : 1
        }
      }
      this.tr.items = this.users
      return this.tr
    },
    train() {
      return this.user.info.train.map(t => t.train).join(', ')
    }
  },
  methods: {
    itemSelect(t, path) {
      this.$store.state.selection = t
    },
    split(width, shown) {
      this.width = width
      this.shown = shown
      cookie.set('tree', {width, shown})
    },
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
