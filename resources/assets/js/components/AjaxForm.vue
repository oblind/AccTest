<template>
  <form class="form" @submit.prevent="submit">
    <slot></slot>
    <p v-show="err" v-html="err"></p>
  </form>
</template>
<script>
export default {
  props: ['error'],
  methods: {
    submit(e) {
      this.$emit('submit', e)
    }
  },
  computed: {
    err() {
      let s = ''
      if (this.error)
        for (let i in this.error)
          s += this.error[i].join('<br>') + '<br>'
      return s
    }
  },
}
</script>
