<style>
.form select {
  font-size: 1em;
  width: 10em;
}
</style>
<template>
  <div>
    <a :href="'#/user/' + $route.params.id">返回</a>
    <form class="form" v-if="user" style="margin-top: 1em" @submit.prevent="submit($event.target)">
      用户名<input type="text" name="name" :value="user.name"><br>
      <div v-if="groups">用户组
        <select name="groupId">
          <option v-for="(g, i) in groups" :value="g.id"  :selected="g.id == user.groupId" :key="i">{{g.name}}</option>
        </select>
      </div>
      <input type="submit" class="submit">
    </form>
  </div>
</template>
<script>
import axios from 'axios'

export default {
  props: ['user'],
  computed: {
    groups() {
      return this.$store.state.groups
    }
  },
  methods: {
    submit(form) {
      axios.put('api/user/' + this.$route.params.id, {name: form.name.value, groupId: form.groupId.value}).then(res => {
        this.$store.commit('editUser', res.data)
        this.$store.commit('message', '修改成功')
      }).catch(res => this.$store.commit('error', res.response.data))
    }
  }
}
</script>
