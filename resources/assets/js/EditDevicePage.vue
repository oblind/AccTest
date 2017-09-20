<template>
  <div>
    <a :href="'#/user/' + $route.params.id + '/device/' + $route.params.devId">返回</a>
    <form class="form" v-if="device" style="margin-top: 1em" @submit="submit($event.target)">
      名称<input type="text" name="name" :value="device.name"><br>
      <input type="submit">
    </form>
  </div>
</template>
<script>
import axios from 'axios'

export default {
  name: 'EditDevicePage',
  computed: {
    device() {
      return this.$store.state.curDevice
    }
  },
  methods: {
    submit(form) {
      let p = this.$route.params
      console.log(form)
      //axios.post('./api/user/' + p.id + '/device/' + p.devId, {name: form.name.value}).then()
    }
  }
}
</script>
