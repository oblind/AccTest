<template>
  <AjaxForm :error="error" @submit="submit">
    邮箱<input type="email" name="email" :value="email" required><img src="img/close.png" style="margin-left: .5em; cursor: pointer" @click="clear"><br>
    密码<input type="password" name="password"><img src="img/close.png" style="margin-left: .5em; cursor: pointer" @click="clear"><br>
    <label><input type="checkbox" name="remember_me" :checked="remember_me">自动登录</label>
    <input type="submit" value="登录" class="submit"><br>
    <input type="button" value="注册" @click="register">
  </AjaxForm>
</template>
<script>
import AjaxForm from './AjaxForm'
export default {
  components: {
    AjaxForm
  },
  props: ['email', 'remember_me', 'error'],
  methods: {
    clear(e) {
      e.target.previousSibling.value = null
    },
    submit(e) {
      this.$emit('login', e.target)
    },
    register(e) {
      this.$emit('register', e.target.parentNode)
    }
  },
}
</script>
