<template>
  <form class="form" v-if="user" style="margin-top: 1em">
    用户名<input type="text" :value="user.name"><br>
    <input type="submit" class="submit">
  </form>
</template>
<script>

export default {
  computed: {
    user() {
      return this.$store.state.curUser
    }
  }
}
</script>
