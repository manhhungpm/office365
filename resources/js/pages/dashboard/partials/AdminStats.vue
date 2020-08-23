<template>
    <div class="offset-3 col-6">
        <template v-if="stats">

                <div class="card card-custom card-stretch card-stretch-half gutter-b">
                    <div class="card-header align-items-center border-0">
                        <h2 class="card-title align-items-center justify-content-center">
                            Tình trạng hệ thống
                        </h2>
                    </div>
                    <div class="card-body pt-4">
                        <div class="d-flex justify-content-between mb-4">
                            <h4>
                                Reseller : {{stats.reseller}}
                            </h4>
                            <router-link :to="'/reseller'"><i class="la la-angle-right"></i></router-link>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <div class="d-flex justify-content-between mb-4">
                            <h4>
                                Mã bảo mật : {{stats.codes}}
                            </h4>
                            <router-link :to="'/student-code'"><i class="la la-angle-right"></i></router-link>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <div class="d-flex justify-content-between mb-4">
                            <h4>
                                Người dùng : {{stats.msUser}}
                            </h4>
                            <router-link :to="'/ms-user'"><i class="la la-angle-right"></i></router-link>
                        </div>
                    </div>
                </div>


        </template>
    </div>
</template>

<script>
  import axios from 'axios'

  export default {
    name: 'AdminStats',
    data() {
      return {
        stats: null
      }
    },
    computed: {
    },
    mounted() {
      this.loadData()
    },
    methods: {
      async loadData() {
        const { data } = await axios.post('/api/dashboard/admin-stats')
        this.stats = data.data
        console.log(data)
      }
    }
  }
</script>