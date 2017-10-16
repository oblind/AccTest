<template>
  <form v-if="option" class="form" style="margin: 1em auto" @submit.prevent="submit">
    安卓客户端<br>
    版本<input type="number" name="androidVersion" v-model="option.android.version"><br>
    地址<input type="text" name="androidUrl" v-model="option.android.url"><br><br>
    Windows客户端<br>
    版本<input type="number" name="windowsVersion" v-model="option.windows.version"><br>
    地址<input type="text" name="windowsUrl" v-model="option.windows.url"><br>
    <input type="submit" value="提交">
  </form>
</template>
<script>
import axios from 'axios'

export default {
  data() {
    return {
      opt: null
    }
  },
  computed: {
    option() {
      if(!this.opt && this.$store.state.option)
        this.opt = JSON.parse(JSON.stringify(this.$store.state.option))
      return this.opt
    }
  },
  methods: {
    submit() {
      let o = this.opt
      o.android.version = parseInt(o.android.version)
      o.windows.version = parseInt(o.windows.version)
      if(isNaN(o.android.version))
        this.$store.commit('error', '安卓版本: 请输入数字')
      else if(o.android.version > 0xffff || o.android.version < 0)
        this.$store.commit('error', '安卓版本范围: 0-65535')
      else if(isNaN(o.windows.version))
        this.$store.commit('error', 'Windows版本: 请输入数字')
      else if(o.android.version > 0xffff || o.android.version < 0)
        this.$store.commit('error', 'Windows版本范围: 0-65535')
      else
        axios.put('api/option', o).then(() => {
          this.$store.commit('option', o)
          this.$store.commit('message', '修改成功')
        })
        .catch(res => this.$store.commit('error', res.response.data))
    }
  }
}
</script>
