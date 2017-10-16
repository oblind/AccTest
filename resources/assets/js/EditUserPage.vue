<style>
.datable button {white-space: nowrap}
</style>
<template>
  <div>
    <div v-if="user" style="display: flex; flex-wrap: wrap; align-items: flex-start">
      <form class="form" @submit.prevent="editUser($event.target)">
        用户名<input type="text" name="name" :value="user.name"><br>
        <template v-if="groups && user != $store.state.user">
          用户组<select name="groupId">
            <option v-for="(g, i) in groups" :value="g.id"  :selected="g.id == user.groupId" :key="i">{{g.name}}</option>
          </select>
        </template>
        <input type="submit" class="submit" value="提交">
        <img :src="user.icon" style="display: block; margin: .2em auto">
        <input type="button" value="更改头像" @click.prevent="$event.target.nextSibling.click()"><input type="file" name="icon" accept="image/jpeg, image/png" style="display: none" @change="setIcon">
      </form>
      <form class="form" @submit.prevent="save">
        <datable :tbl="tbl" :data="position.train">
          <button class="act" @click.prevent="add">新建</button>
        </datable>
        <table class="datable">
          <caption>工位信息<button class="act" @click.prevent="position.position.push('工位')">新建</button></caption>
          <thead><tr><th>工位</th><th>操作</th></tr></thead>
          <tbody>
            <tr v-for="(p, i) in position.position" :key="i">
              <td><input type="text" v-model="position.position[i]"></td><td><button @click.prevent="position.position.length > 1 && position.position.splice(i, 1)">删除</button></td>
            </tr>
          </tbody>
        </table>
        车厢数<input type="number" name="count" :value="user.info.count">
        <input type="submit" value="提交">
      </form>
    </div>
    <button v-if="user.id != $store.state.user.id" @click="del">删除用户</button>
  </div>
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
      pos: null,
      tbl: {
        caption: '车辆信息',
        columns: {
          prefix: {
            caption: '线路编号',
            type: 'text'
          },
          startTrain: {
            caption: '首列车号',
            type: 'number'
          },
          endTrain: {
            caption: '尾列车号',
            type: 'number'
          }
        },
        action: [{
          caption: '删除',
          onclick(data, i) {
            if(data.length > 1) {
              Vue.delete(data, i)
            }
          }
        }]
      }
    }
  },
  computed: {
    groups() {
      return this.$store.state.groups
    },
    position() {
      if(!this.pos)
        this.pos = JSON.parse(JSON.stringify(this.user.info))
      return this.pos
    }
  },
  methods: {
    add() {
      this.pos.train.push({
        prefix: '',
        startTrain: 1,
        endTrain: 10,
      })
    },
    save(e) {
      for(let i = 0; i < this.pos.train.length; i++) {
        let d = this.pos.train[i]
        d.startTrain = parseInt(d.startTrain)
        d.endTrain = parseInt(d.endTrain)
        if(d.endTrain < d.startTrain) {
          this.$store.commit('error', '规则' + (i + 1) + ': 结束车号不能小于起始车号')
          return
        }
      }
      this.pos.count = parseInt(e.target.count.value)
      this.pos.train.forEach(t => delete t.train)
      axios.put('api/user/' + this.user.id + '/position', this.pos).then(() => {
        this.user.info = JSON.parse(JSON.stringify(this.pos))
        this.$store.state.fixPosition(this.user.info)
        this.$store.commit('message', '修改成功')
      }).catch(res => this.$store.commit('error', res.response.data))
    },
    editUser(form) {
      let d = {name: form.name.value}
      if(this.user != this.$store.state.user)
        d.groupId = form.groupId.value
      axios.put('api/user/' + this.user.id, d).then(res => {
        this.$store.commit('editUser', res.data)
        this.$store.commit('message', '修改成功')
      }).catch(res => this.$store.commit('error', res.response.data))
    },
    setIcon(e) {
      let f = e.target.files[0]
      if(f.size > 0x180000)
        this.$store.commit('error', '图片不能大于1.5M')
      else {
        let fd = new FormData()
        fd.append('file', f)
        axios.post('api/user/' + this.user.id + '/icon', fd).then(res => {
          Vue.set(this.$store.state.curUser, 'icon', 'img/user/' + this.user.id + '.png?' + Math.floor(Math.random() * 1000))
          this.$store.commit('message', '上传成功')
        }).catch(res => {
          this.$store.commit('error', res.response.data)
        })
      }
      e.target.value = ''
    },
    del() {
      if(confirm('确定要删除用户 ?'))
        axios.delete('api/user/' + this.user.id).then(res => {
          this.$store.commit('delUser', this.user)
        }).catch(res => this.$store.commit('error', res.response.data))
    }
  }
}
</script>
