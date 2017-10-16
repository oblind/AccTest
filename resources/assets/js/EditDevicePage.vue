<template>
  <form class="form" v-if="device" @submit.prevent="submit($event.target)">
    名称<input type="text" name="name" :value="device.name"><br>
    <input type="submit" value="提交">
  </form>
</template>
<script>
import axios from 'axios'
import Vue from 'vue'

export default {
  props: ['device'],
  methods: {
    submit(form) {
      let p = this.$route.params
      axios.put('api/user/' + p.id + '/device/' + p.devId, {name: form.name.value}).then(res => {
        this.$store.commit('editDevice', {id: p.id, devId: p.devId, name: form.name.value})
        this.$store.commit('message', '修改成功')
      }).catch(res => this.$store.commit('error', res.response.data))
    }
  },
  beforeRouteEnter(to, from, next) {
    console.log(to.name)
    next()
  }
}
</script>
