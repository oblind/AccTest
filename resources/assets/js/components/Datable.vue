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
        <tr v-for="(row, i) in data" :key="i" :class="row == selection ? ['selection'] : null" @click="rowClick(row, i)">
          <template v-for="(c, j) in tbl.columns">
            <td v-if="typeof c != 'object'" v-html="row[j]" :key="j"></td>
            <template v-else>
              <template v-if="!hide[j]">
                <template v-if="!c.type && typeof c.items == 'function'">
                  <td v-if="c.href" :key="j"><a :href="row[c.href]">{{getItems(row[j], c.items(row, j))}}</a></td>
                  <td v-else v-html="getItems(row[j], c.items(row, j))" :key="j"></td>
                </template>
                <template v-else-if="!c.type && c.items">
                  <td v-if="c.href" :key="j"><a :href="row[c.href]">{{getItems(row[j], c.items)}}</a></td>
                  <td v-else v-html="getItems(row[j], c.items)" :key="j"></td>
                </template>
                <template v-else-if="!c.type && c.filter">
                  <td v-if="c.href" :key="j"><a :href="row[c.href]">{{c.filter(row[j])}}</a></td>
                  <td v-else v-html="c.filter(row[j])" :key="j"></td>
                </template>
                <template v-else-if="!c.type">
                  <td v-if="c.href" :key="j"><a :href="row[c.href]">{{row[j]}}</a></td>
                  <td v-else v-html="row[j]" :key="j"></td>
                </template>
                <td v-else-if="c.type == 'radio'" :key="j">
                  <label v-for="(o, k) in c.items" :key="k"><input type="radio" :value="k" v-model="row[j]"><span v-html="o"></span></label>
                </td>
                <td v-else-if="c.type == 'select'" :key="j">
                  <select v-model="row[j]" @change="c.onchange && c.onchange(row)"><option v-for="(o, k) in typeof c.items == 'function' ? c.items(row, j, c.bind) : c.items" :value="k" v-html="o" :key="k"></option></select>
                </td>
                <td v-else-if="c.type == 'checkbox'" :key="j">
                  <label v-for="(o, k) in c.items" :key="k"><input type="checkbox" :value="k" v-model="row[j]"><span v-html="o"></span></label>
                </td>
                <td v-else-if="c.type == 'email'" :key="j">
                  <input type="email" v-model="row[j]">
                </td>
                <td v-else-if="c.type == 'number'" :key="j">
                  <input type="number" v-model="row[j]">
                </td>
                <td v-else :key="j">
                  <input type="text" v-model="row[j]">
                </td>
              </template>
            </template>
          </template>
          <td v-if="tbl.action">
            <template v-for="(b, j) in tbl.action">
              <template v-if="typeof b.condition == 'undefined' || btnCondition(data, i, b)">
                <a v-if="b.href" :href="row[b.href]" :key="j">{{b.caption}}</a>
                <button v-else @click.stop.prevent="btnClick(data, i, b)" v-html="b.caption" :key="j"></button>
              </template>
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
  props: ['tbl', 'data', 'selection'],
  data: function() {
    return {
      def_ascimg: './img/asc.png',
      def_descimg: './img/desc.png',
      hide: [],
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
      let s = c.condition.call(this.$parent, k)
      this.hide[k] = !s
      return s
    },
    colClick(k) {
      if(this.tbl.orderby == k) this.tbl.desc = !this.tbl.desc;
      else {
        this.tbl.orderby = k;
        this.tbl.desc = false;
      }
      this.sort();
    },
    rowClick(row, i) {
      if(row != this.selection)
        this.$emit('rowSelect', row, i)
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
