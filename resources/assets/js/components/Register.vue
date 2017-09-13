<template>
  <AjaxForm :error="error" @submit="submit">
    　用户名<input type="text" name="name"><br>
    　　邮箱<input type="text" name="email"><br>
    　　密码<input type="password" name="password"><br>
    确认密码<input type="password" name="password_confirmation"><br>
    <input type="submit" value="注册" class="submit">
    <input type="button" value="登录" @click="login">
  </AjaxForm>
</template>
<script>
import AjaxForm from './AjaxForm'
export default {
  components: {
    AjaxForm
  },
  props: ['error'],
  methods: {
    submit: function(e) {
      this.$emit('register', e.target)
    },
    login: function(e) {
      this.$emit('login', e.target)
    }
  },
}
</script>
