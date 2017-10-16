<template>
  <div style="background-color: red" v-update:datasets="datasets" :labels="labels">
    <canvas></canvas>
  </div>
</template>
<script>
import Chart from 'chart.js'

export default {
  props: ['labels', 'datasets'],
  data() {
    return {
      cht: null
    }
  },
  directives: {
    update: {
      bind(el, binding) {
        console.log('bind', el, binding)
      },
      update(el, binding) {
        console.log('update', el, binding)
      }
    }
  },
  mounted() {
    this.cht = new Chart(this.$el.childNodes[0].getContext('2d'), {
      type: 'line',
      data: {
        labels: this.labels,
        datasets: this.datasets
      }
    })
  }
}
</script>
