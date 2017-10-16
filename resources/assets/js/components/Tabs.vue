<style>
div.tabs {
  height: 100%;
  display: flex;
  flex-direction: column;
}
div.bottom {flex-direction: column-reverse}
.tabs ul {
  list-style: none;
  width: 100%;
  margin: 0;
  padding: 0;
  display: flex;
}
.tabs li {
  color: #ddd;
  background-color: #777;
  padding: .2em .5em;
  border-width: 1px;
  flex-grow: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  border: 1px solid;
  border-color: white #333 white white;
  border-radius: .5em .5em 0 0;
}
.bottom>ul>li {
  border-radius: 0 0 .5em .5em;
  border-top-color: #333;
  border-bottom-color: #333;
}
li.selected {
  background-color: #ddd;
  color: black;
  border-bottom-style: none;
}
li.selected.bottom {
  border-top-style: none;
  border-bottom-style: solid;
}
.page {
  padding: .2em;
  border: 1px solid;
  border-color: white #333 #333 white;
  border-top-style: none;
  background-color: #ddd;
  flex-grow: 1;
}
.bottom>.page {
  border-top-style: solid;
  border-bottom-style: none;
}

</style>
<template>
  <div class="tabs" :class="bottomTab ? ['bottom'] : null">
    <ul>
      <li v-for="(t, i) in tabs" :class="getClass(i)" :key="i" @click="click(i)" v-html="t"></li>
      <div style="clear: both"></div>
    </ul>
    <template v-for="(t, i) in tabs">
      <div v-show="tabIdx == i" :key="i" class="page">
        <slot :name="i"></slot>
      </div>
    </template>
  </div>
</template>
<script>
export default {
  props: ['tabs', 'tabIndex', 'bottomTab'],
  data() {
    return {
      ti: 0
    }
  },
  computed: {
    tabIdx() {
      if(typeof this.tabIndex != 'undefined')
        this.ti = this.tabIndex
      return this.ti
    }
  },
  methods: {
    getClass(i) {
      let r = this.bottomTab ? ['bottom'] : ['top']
      if(i == this.tabIdx)
        r.push('selected')
      return r
    },
    click(i) {
      this.ti = i
      this.$emit('tabIndex', i)
    }
  }
}
</script>
