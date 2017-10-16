<style>
.canvas {
  background-color: white;
  width: 100%;
  min-height: 300px;
}
</style>
<template>
  <tabs :tabs="pages" :tabIndex="pageIndex" @tabIndex="onPageIndex">
    <tabs :tabs="general" :tabIndex="generalIndex" :bottomTab="true" @tabIndex="onGeneralIndex" slot="0">
      <canvas id="chartAcc" class="canvas" slot="0"></canvas>
      <canvas id="chartPwx" class="canvas" slot="1"></canvas>
    </tabs>
    <div slot="1">
      <div style="max-height: 15em; overflow: auto">
        <datable :tbl="tbl" :data="ticks" :selection="selection" @rowSelect="rowSelect"></datable>
      </div>
      <tabs :tabs="detail" :tabIndex="detailIndex" @tabIndex="onDetailIndex">
        <div slot="0">
          <canvas id="chartTime" class="canvas"></canvas>
        </div>
        <div slot="1">
          <canvas id="chartFreq" class="canvas"></canvas>
        </div>
      </tabs>
    </div>
  </tabs>
</template>
<script>
import axios from 'axios'
import Chart from 'chart.js'
import Zoom from 'chartjs-plugin-zoom'
import Vue from 'vue'
import Tabs from './components/Tabs'
import Datable from './components/Datable'

export default {
  components: {Tabs, Datable},
  props: ['device'],
  data() {
    return {
      pageIndex: 0,
      pages: [
        '历程',
        '详情'
      ],
      generalIndex: 0,
      general: [
        '加速度',
        '平稳性'
      ],
      detailIndex: 0,
      detail: [
        '时域',
        '频域'
      ],
      chtAcc: null,
      chtPwx: null,
      chtTime: null,
      chtFreq: null,
      chartAcc: {
        type: 'line',
        data: {}
      },
      chartPwx: {
        type: 'line',
        data: {}
      },
      chartTime: {
        type: 'line',
        data: {}
      },
      chartFreq: {
        type: 'line',
        options: {
          scales: {
            xAxes: [{
              type: 'logarithmic',
              ticks: {
                callback: v => Math.round(v * 1000) / 1000
              }
            }]
          }
        },
        data: {}
      },
      tbl: {
        columns: {
          time: '时间',
          speed: '时速',
          pwxy: '横向平稳性',
          pwxz: '垂向平稳性',
          accy: 'RMS-Y',
          accz: 'RMS-Z'
        }
      },
      ticks: null,
      pwx: false,
      selection: null
    }
  },
  computed: {
    data() {
      let id = this.$route.params.dataId
      return this.device.data.find(d => d.id == id)
    }
  },
  methods: {
    rowSelect(row) {
      this.selection = row
      this.data.dtl.chartTime[0].data = []
      this.data.dtl.chartTime[1].data = []
      this.data.dtl.chartFreq[0].data = []
      this.data.dtl.chartFreq[1].data = []
      let i = 0
      for(; i < 1024; i += 10) {
        this.data.dtl.chartTime[0].data.push(row.acc[0][i])
        this.data.dtl.chartTime[1].data.push(row.acc[1][i])
      }
      let d = this.$store.state.frequency / 1024
      for(i = 0; i < 52; i++) {
        this.data.dtl.chartFreq[0].data.push({
          x: (i + 1) * d,
          y: row.freq[0][i]
        })
        this.data.dtl.chartFreq[1].data.push({
          x: (i + 1) * d,
          y: row.freq[1][i]
        })
      }
      for(; i < 103; i += 5) {
        this.data.dtl.chartFreq[0].data.push({
          x: (i + 1) * d,
          y: row.freq[0][i]
        })
        this.data.dtl.chartFreq[1].data.push({
          x: (i + 1) * d,
          y: row.freq[1][i]
        })
      }
      for(; i < 512; i += 10) {
        this.data.dtl.chartFreq[0].data.push({
          x: (i + 1) * d,
          y: row.freq[0][i]
        })
        this.data.dtl.chartFreq[1].data.push({
          x: (i + 1) * d,
          y: row.freq[1][i]
        })
      }
      this.chtTime.update()
      this.chtFreq.update()
    },
    onPageIndex(i) {
      this.pageIndex = i
    },
    onGeneralIndex(i) {
      this.generalIndex = i
    },
    onDetailIndex(i) {
      this.detailIndex = i
    }
  },
  mounted() {
    Chart.defaults.global.animation = false
    Chart.defaults.global.maintainAspectRatio = false
    Chart.defaults.global.elements.line.tension = 0
    Chart.defaults.global.elements.point.pointStyle = 'dash'
    Chart.defaults.global.pan = {
      enabled: true,
      mode: 'y'
    }
    Chart.defaults.global.zoom = {
      enabled: true,
      mode: 'y'
    }
    this.chtAcc = new Chart(document.getElementById('chartAcc').getContext('2d'), this.chartAcc)
    this.chtPwx = new Chart(document.getElementById('chartPwx').getContext('2d'), this.chartPwx)
    this.chtTime = new Chart(document.getElementById('chartTime').getContext('2d'), this.chartTime)
    this.chtFreq = new Chart(document.getElementById('chartFreq').getContext('2d'), this.chartFreq)
    if(!this.data.dtl)
      axios.get('api/user/' + this.$route.params.id + '/device/' + this.$route.params.devId + '/data/' + this.$route.params.dataId + '/detail')
      .then(res => {
        window.d = this
        this.data.dtl = res.data
        this.ticks = res.data.data
        let accy = [], accz = [], pwxy = [], pwxz = []
        this.data.dtl.labels = []
        let f = res.data.info.frequency
        if(!this.$store.state.labelDetail[f]) {
          let l = []
          this.$store.state.frequency = f
          this.$store.state.labelDetail[f] = {}
          for(let i = 0; i < 1024; i += 10)
            l.push(Math.round(i * 100 / f) / 100)
          this.$store.state.labelDetail[f].time = l
        }
        const fix = (f, t = 10) => Math.round(f * t) / t
        res.data.data.forEach(d => {
          d.time = new Date(d.time).toTime()
          this.data.dtl.labels.push(d.time)
          d.accy = fix(d.accy, 1000)
          accy.push(d.accy)
          d.accz = fix(d.accz, 1000)
          accz.push(d.accz)
          d.pwxy = fix(d.pwxy)
          pwxy.push(d.pwxy)
          d.pwxz = fix(d.pwxz)
          pwxz.push(d.pwxz)
        });
        this.data.dtl.chartAcc = [
          {
            label: '横向',
            borderColor: 'red',
            borderWidth: 1,
            fill: false,
            data: accy
          }, {
            label: '垂向',
            borderColor: 'blue',
            borderWidth: 1,
            fill: false,
            data: accz
          }
        ]
        this.data.dtl.chartPwx = [
          {
            label: '横向',
            borderColor: 'red',
            borderWidth: 1,
            fill: false,
            data: pwxy
          }, {
            label: '垂向',
            borderColor: 'blue',
            borderWidth: 1,
            fill: false,
            data: pwxz
          }
        ]
        this.data.dtl.chartTime = [
          {
            label: '横向',
            borderColor: 'red',
            borderWidth: 1,
            fill: false,
            data: []
          }, {
            label: '垂向',
            borderColor: 'blue',
            borderWidth: 1,
            fill: false,
            data: []
          }
        ]
        this.data.dtl.chartFreq = [
          {
            label: '横向',
            borderColor: 'red',
            borderWidth: 1,
            fill: false,
            data: []
          }, {
            label: '垂向',
            borderColor: 'blue',
            borderWidth: 1,
            fill: false,
            data: []
          }
        ]
        this.chartFreq.options.scales.xAxes[0].ticks.min = f / 1024
        this.chartFreq.options.scales.xAxes[0].ticks.max = f / 2
        this.chartAcc.data.labels = this.data.dtl.labels
        this.chartAcc.data.datasets = this.data.dtl.chartAcc
        this.chartPwx.data.labels = this.data.dtl.labels
        this.chartPwx.data.datasets = this.data.dtl.chartPwx
        this.chartTime.data.labels = this.$store.state.labelDetail[f].time
        this.chartTime.data.datasets = this.data.dtl.chartTime
        this.chartFreq.data.datasets = this.data.dtl.chartFreq
        this.chtAcc.update()
        if(this.data.dtl.data.length)
          this.rowSelect(this.data.dtl.data[0])
      })
      .catch(res => this.$store.commit('error', res.response.data))
    else {
      let f = this.$store.state.frequency
      this.chartFreq.options.scales.xAxes[0].ticks.min = f / 1024
      this.chartFreq.options.scales.xAxes[0].ticks.max = f / 2

      this.ticks = this.data.dtl.data
      this.chartAcc.data.labels = this.data.dtl.labels
      this.chartAcc.data.datasets = this.data.dtl.chartAcc
      this.chartPwx.data.labels = this.data.dtl.labels
      this.chartPwx.data.datasets = this.data.dtl.chartPwx
      this.chartTime.data.labels = this.$store.state.labelDetail[f].time
      this.chartTime.data.datasets = this.data.dtl.chartTime
      this.chartFreq.data.datasets = this.data.dtl.chartFreq
      this.chtAcc.update()
      if(this.data.dtl.data.length)
        this.rowSelect(this.data.dtl.data[0])
    }
  }
}
</script>
