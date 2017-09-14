<template>
<table class="datable">
  <caption>
    <span v-html="tbl.caption" v-if="tbl.caption"></span>
    <slot></slot>
  </caption>
  <template v-if="tbl.columns">
    <thead><tr>
      <template v-for="(c, i) in tbl.columns">
        <th v-if="typeof c != 'object'" style="cursor: pointer" @click="colClick(i)" :key="i"><span v-html="c"></span><img :style="{display: i == tbl.orderby ? '' : 'none'}" :src="tbl.desc ? tbl.descimg || def_descimg : tbl.ascimg || def_ascimg"></th>
        <template v-else-if="typeof c.key == 'undefined'">
          <th v-if="typeof c.condition == 'undefined' || colCondition(i, c)" style="cursor: pointer" @click="colClick(i)" :key="i"><span v-html="c.caption"></span><img :style="{display: i == tbl.orderby ? '' : 'none'}" :src="tbl.desc ? tbl.descimg || def_descimg : tbl.ascimg || def_ascimg"></th>
        </template>
        <template v-else>
          <th v-if="typeof c.condition == 'undefined' || colCondition(c.key, c)" style="cursor: pointer" @click="colClick(c.key)" :key="i"><span v-html="c.caption"></span><img :style="{display: c.key == tbl.orderby ? '' : 'none'}" :src="tbl.desc ? tbl.descimg || def_descimg : tbl.ascimg || def_ascimg"></th>
        </template>
      </template><th v-if="tbl.action">操作</th></tr>
    </thead>
    <tbody>
      <template v-if="data && data.length">
        <tr v-for="(row, i) in data" :key="i">
          <template v-for="(c, j) in tbl.columns">
            <td v-if="typeof c != 'object'" v-html="row[j]" :key="j"></td>
            <template v-else>
              <template v-if="!hide[j]">
                <td v-if="!c.type && typeof c.items == 'function'" v-html="getItems(row[j], c.items(row, j))" :key="j"></td>
                <td v-else-if="!c.type && c.items" v-html="getItems(row[j], c.items)" :key="j"></td>
                <td v-else-if="!c.type && c.filter" v-html="c.filter(row[j])" :key="j"></td>
                <td v-else-if="!c.type" v-html="row[j]" :key="j"></td>
                <td v-else-if="c.type == 'radio'" :key="j">
                  <label v-for="(o, k) in c.items" :key="k"><input type="radio" :value="k" v-model="row[j]"><span v-html="o"></span></label>
                </td>
                <td v-else-if="c.type == 'select'" :key="j">
                  <select v-model="row[j]" @change="c.onchange && c.onchange(row)"><option v-for="(o, k) in typeof c.items == 'function' ? c.items(row, j, c.bind) : c.items" :value="k" v-html="o" :key="k"></option></select>
                </td>
                <td v-else-if="c.type == 'checkbox'" :key="j">
                  <label v-for="(o, k) in c.items" :key="k"><input type="checkbox" :value="k" v-model="row[j]"><span v-html="o"></span></label>
                </td>
              </template>
            </template>
          </template>
          <td v-if="tbl.action">
            <template v-for="(b, j) in tbl.action">
              <button v-if="typeof b.condition == 'undefined' || btnCondition(data, i, b)" @click="btnClick(data, i, b)" v-html="b.caption" :key="j"></button>
            </template>
          </td>
        </tr>
      </template>
      <td v-else :colspan="colspan">无数据</td>
    </tbody>
  </template>
  <tbody v-else>
    <tr v-for="(row, i) in data" :key="i"><td v-for="(c, j) in row" :key="j" v-html="c"></td></tr>
  </tbody>
</table>
</template>

<script>
export default {
  name: 'datable',
  props: ['tbl', 'data'],
  data: function() {
    return {
      def_ascimg: './img/asc.png',
      def_descimg: './img/desc.png',
      hide: []
    };
  },
  computed: {
    colspan() {
      let c = 0
      for(let k in this.tbl.columns) c++
      return c + (this.tbl.action ? 1 : 0)
    }
  },
  methods: {
    colCondition(k, c) {
      let h = c.condition.call(this.$parent, k)
      if(!h) this.hide[k] = !h
      return h
    },
    colClick: function(k) {
      if(this.tbl.orderby == k) this.tbl.desc = !this.tbl.desc;
      else {
        this.tbl.orderby = k;
        this.tbl.desc = false;
      }
      this.sort();
    },
    btnCondition(data, i, b) {
      return b.condition.call(this.$parent, data, i)
    },
    btnClick(data, i, b) {
      b.onclick.call(this.$parent, data, i)
    },
    sort: function(){
      var k = this.tbl.orderby, desc = this.tbl.desc;
      if(this.data)
        this.data.sort(function(a, b){
          var r = a[k] > b[k] ? 1 : a[k] < b[k] ? -1 : 0;
          return desc ? -r : r;
        });
    },
    getItems: function(v, items){
      var i, r = [];
      if(v instanceof Array){
        for(i in v) r.push(items[v[i]]);
        return r.join(', ');
      }
      else return items[v];
    }
  },
  mounted: function(){
    this.sort();
  }
}
</script>
