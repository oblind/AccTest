<template>
  <Register v-if="register" :error="registerError" @register="registerRegister" @login="registerLogin"></Register>
  <Login v-else :remember_me="remember_me" :error="loginError" @login="loginLogin" @register="loginRegister"></Login>
</template>
<script>
import axios from 'axios'
import cookie from 'js-cookie'
import Login from './components/Login'
import Register from './components/Register'

export default {
  components: {Login, Register},
  data() {
    return {
      register: false,
      email: cookie.get('email'),
      remember_me: Number.parseInt(cookie.get('remember_me')),
      loginError: null,
      registerError: null
    }
  },
  methods: {
    loginLogin(form) {
      let remember_me = form.remember_me.checked
      axios.post('./api/auth', {email: form.email.value, password: form.password.value,
        remember_me: form.remember_me.checked})
        .then(res => {
          let id = cookie.get('token')
          this.$store.commit('users', {users: res.data, id})
          cookie.set('remember_me', form.remember_me.checked ? 1 : 0)
        }).catch(res => this.loginError = res.response.data)
    },
    loginRegister(form) {
      this.register = true
    },
    registerRegister(form) {
      axios.post('./api/user', {name: form.name.value, email: form.email.value,
        password: form.password.value, password_confirmation: form.password_confirmation.value})
        .then(res => {
          let id = res.data[0].id
          this.$store.commit('users', {users: res.data, id})
        })
        .catch(res => this.registerError = res.response.data)
    },
    registerLogin(form) {
      this.register = false
    },
  }
}
</script>
