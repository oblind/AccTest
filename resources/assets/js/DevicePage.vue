<template>
  <div v-if="$route.name == 'device'">
    <table class="datable" v-if="device">
      <caption style="position: relative">设备信息
        <a style="position: absolute; left: 0" :href="'#/user/' + user.id + '/device/' + device.id + '/edit'">编辑</a>
      </caption>
      <thead>
        <tr><th>名称</th><th>标识</th><th>使用人</th><th>注册日期</th><th>状态</th><th>操作</th></tr>
      </thead>
      <tbody>
        <tr><td>{{device.name}}</td><td>{{device.token}}</td><td>{{device.userName}}</td><td>{{device.created_at}}</td><td>{{state[device.state]}}</td><td><button v-if="admin && device.state == 0" @click="pass(device)">通过</button><button @click="del">删除</button></td></tr>
      </tbody>
    </table>
    <datable :tbl="tbl" :data="device && device.data"></datable>
  </div>
  <router-view v-else :device="device"></router-view>
</template>
<script>
import axios from 'axios'
import Vue from 'vue'
import Datable from './components/Datable'

export default {
  components: {Datable},
  data() {
    return {
      state: ['待审核', '正常'],
      tbl: {
        caption: '数据信息',
        columns: {
          filename: '文件名',
          created_at: '时间',
        },
        action: [
          {
            caption: '下载',
            onclick() {
              alert('下载')
            }
          }
        ]
      },
    }
  },
  computed: {
    user() {
      return this.$store.state.curUser
    },
    device() {
      if(this.user) {
        let id = this.$route.params.devId
        for(let i = 0; i < this.user.device.length; i++)
          if(this.user.device[i].id == id) {
            this.index = i
            this.$store.state.curDevice = this.$store.state.selection = this.user.device[i]
            break;
          }
      }
      return this.$store.state.curDevice
    },
    admin() {
      return this.$store.state.user && this.$store.state.user.groupId == 255
    },
  },
  methods: {
    pass(d) {
      if(confirm('通过该设备审核 ?'))
        axios.post('./api/user/' + this.user.id + '/device/' + d.id + '/grant').then(() => {
          Vue.set(d, 'state', 1)
        })
    },
    del() {
      if(confirm('删除该设备 ?'))
        axios.delete('./api/user/' + this.user.id + '/device/' + this.device.id).then(() => {
          Vue.delete(this.user.device, this.index)
          if(this.user.device.length == 0)
            this.$router.push('/user/' + this.user.id)
          else {
            if(this.index == this.user.device.length)
              this.index--
            this.$router.push('/user/' + this.user.id + '/device/' + this.user.device[this.index].id)
          }
        })
    }
  }
}
</script>
