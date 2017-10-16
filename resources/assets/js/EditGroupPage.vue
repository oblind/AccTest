<template>
  <form v-if="group" @submit.prevent="submit" class="form">
    ID: {{group.id}}<br>
    组名<input type="text" name="name" :value="group.name"><br>
    上限<input type="text" name="capacity" :value="group.capacity"><br>
    <input type="submit" value="提交">
  </form>
</template>
<script>
import axios from 'axios'
import Datable from './components/Datable'

export default {
  props: ['group'],
  methods: {
    submit(e) {
      let d = {
        'name': e.target.name.value,
        'capacity': parseInt(e.target.capacity.value)
      }
      axios.put('api/group/' + this.group.id, d).then(res => {
        this.group.name = d.name
        this.group.capacity = d.capacity
        this.$store.commit('message', '修改成功')
      }).catch(res => this.$store.commit('error', res.response.data))
    }
  }
}
</script>
