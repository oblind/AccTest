<template>
  <div style="margin: 5px">
    <datable v-if="$route.name == 'groups'" :tbl="tbl" :data="groups"></datable>
    <router-view v-else :group="groups && groups.find(g => g.id == $route.params.id)"></router-view>
  </div>
</template>
<script>
import Datable from './components/Datable'

export default {
  components: {Datable},
  data() {
    return {
      tbl: {
        caption: '用户组',
        columns: {
          id: 'id',
          name: {
            caption: '组名',
            href: 'href'
          },
          capacity: '上限'
        }
      }
    }
  },
  computed: {
    groups() {
      return this.$store.state.groups
    }
  }
}
</script>
