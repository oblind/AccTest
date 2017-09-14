<template>
  <form class="form" v-if="device" style="margin-top: 1em" @submit="submit($event.target)">
    名称<input type="text" name="name" :value="device.name"><br>
    <input type="submit">
  </form>
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
