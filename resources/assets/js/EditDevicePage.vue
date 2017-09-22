<template>
  <div>
    <a :href="'#/user/' + $route.params.id + '/device/' + $route.params.devId">返回</a>
    <form class="form" v-if="device" style="margin-top: 1em" @submit.prevent="submit($event.target)">
      名称<input type="text" name="name" :value="device.name"><br>
      <input type="submit">
    </form>
  </div>
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
        location.reload()
        //this.$store.commit('device', res.data)
      })
    }
  }
}
</script>
