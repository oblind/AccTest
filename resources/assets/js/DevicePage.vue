<template>
  <div v-if="$route.name == 'device'">
    <table class="datable" v-if="device">
      <caption style="position: relative">设备信息
        <a class="act" :href="'#/user/' + user.id + '/device/' + device.id + '/edit'">编辑</a>
      </caption>
      <thead>
        <tr><th>名称</th><th v-show="user.groupId == 255">标识</th><th>注册日期</th><th>状态</th><th>操作</th></tr>
      </thead>
      <tbody>
        <tr><td>{{device.name}}</td><td v-show="user.groupId == 255">{{device.token}}</td><td>{{device.created_at}}</td><td>{{state[device.state]}}</td><td><button v-if="device.state == 0" @click="pass">通过</button><button @click="del">删除</button></td></tr>
      </tbody>
    </table>
    <datable :tbl="tbl" :data="device && device.data">
      <button @click="$event.target.nextSibling.click()" class="act">上传</button><input type="file" style="display: none" id="uf" @change="upload">
    </datable>
  </div>
  <router-view v-else :device="device"></router-view>
</template>
<script>
import axios from 'axios'
import Vue from 'vue'
import Datable from './components/Datable'

export default {
  components: {Datable},
  props: ['user'],
  data() {
    return {
      state: ['待审核', '正常'],
      tbl: {
        caption: '数据信息',
        columns: {
          filename: {
            caption: '文件名',
            href: 'href'
          },
          userName: '用户',
          position: '工位',
          size: '大小',
          created_at: '上传时间',
        },
        action: [
          {
            caption: '下载',
            href: 'download'
          }, {
            caption: '删除',
            onclick(data, i) {
              if(confirm('删除 ' + data[i].filename + ' ?'))
                axios.delete('api/user/' + this.user.id + '/device/' + this.device.id + '/data/' + data[i].id)
                .then(res => data.splice(i, 1))
                .catch(res => this.$store.commit('error', res.response.data))
            }
          }
        ]
      },
    }
  },
  computed: {
    device() {
      let id = this.$route.params.devId
      if(!this.$store.state.curDevice || this.$store.state.curDevice.id != this.$route.params.devId) {
        this.$store.state.curDevice = this.$store.state.curUser.device.find(d => d.id == id)
        this.$store.state.selection = this.$store.state.curDevice
        this.$store.state.curUser.tn.expand = true
      }
      return this.$store.state.curDevice
    },
    admin() {
      return this.user && this.user.groupId == 255
    },
  },
  methods: {
    pass() {
      if(this.user.device.filter(d => d.state == 1).length >= this.user.group.capacity)
        this.$store.commit('error', '您的设备数量已达到上限')
      else if(confirm('通过该设备审核 ?'))
        axios.post('api/user/' + this.user.id + '/device/' + this.device.id + '/grant').then(() => {
          this.$store.commit('passDevice', this.device)
        }).catch(res => this.$store.commit('error', res.response.data))
    },
    del() {
      if(confirm('删除该设备 ?'))
        axios.delete('api/user/' + this.user.id + '/device/' + this.device.id).then(() => {
          this.$store.commit('deleteDevice', this.device)
        }).catch(res => this.$store.commit('error', res.response.data))
    },
    upload(e) {
      let fn = e.target.files.length && e.target.files[0].name
      if(!/\.pwx$/i.test(fn))
        alert('只能上传.pwx文件')
      else if(this.device.data.find(d => d.filename == fn))
        alert('文件已经存在')
      else {
        let fd = new FormData()
        fd.append('file', e.target.files[0])
        axios.post('api/user/' + this.user.id + '/device/' + this.device.id + '/data', fd)
          .then(res => {
            this.$store.state.fixData(this.user, this.device, res.data)
            this.device.data.push(res.data)
            this.$store.commit('message', '上传成功')
          })
          .catch(res => this.$store.commit('error', res.response.data))
      }
      e.target.value = ''
    }
  },
}
</script>
