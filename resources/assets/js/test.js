import Vue from 'vue'
import TestPage from './TestPage'

Vue.component('TestPage', TestPage)
new Vue({
  el: '#app',
  template: '<TestPage></TestPage>'
})