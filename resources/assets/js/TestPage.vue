<style>
input[type=text] {
  margin: .2em 0 .2em .2em;
}
</style>
<template>
<div>
  <form @submit.prevent="submit">
    <div>{{txt}}</div>
    token<input type="text" name="token"><br>
    name<input type="text" name="name"><br>
    email<input type="text" name="email"><br>
    user name<input type="text" name="userName"><br>
    <input type="submit">
  </form>
  <form @submit.prevent="query">
    <div>{{queryTxt}}</div>
    id<input type="text" name="id"><br>
    <input type="submit">
  </form>
</div>
</template>
<script>
import axios from 'axios'

let state = ['待审核', '通过']
export default {
  data() {
    return {
      txt: '',
      queryTxt: ''
    }
  },
  methods: {
    submit(e) {
      let f = e.target
      axios.post("api/device", {token: f.token.value, name: f.name.value, email: f.email.value, userName: f.userName.value}).then(res => {
        this.txt = state[res.data.state]
      }).catch(res => {
        this.txt = res.response.data.error
      })
    },
    query(e) {
      let f = e.target
      axios.get('api/device/' + f.id.value).then(res => {
        this.queryTxt = state[res.data.state]
      }).catch(res => {
        this.queryTxt = res.response.data.error
      })
    }
  }
}
</script>
