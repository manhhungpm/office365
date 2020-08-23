<template>
    <div>
        <reseller-stats v-if="isReseller"></reseller-stats>
        <admin-stats v-if="isAdmin"></admin-stats>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { ROLE_RESELLER, ROLE_ADMIN } from '~/constants/roles'
  import ResellerStats from './partials/ResellerStats'
  import AdminStats from './partials/AdminStats'

  export default {
    components: {
      AdminStats,
      ResellerStats },
    layout: 'default',
    middleware: 'auth',
    metaInfo() {
      return { title: 'Dashboard' }
    },
    data: () => ({}),
    computed: {
      ...mapGetters({
        role: 'auth/role'
      }),

      isReseller() {
        return this.role == ROLE_RESELLER
      },
      isAdmin() {
        return this.role == ROLE_ADMIN
      }
    },

    mounted() {

    },
    methods: {}
  }
</script>
