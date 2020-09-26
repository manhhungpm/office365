<template>
    <div class="row">
        <template v-if="stats">
            <div class="col-4">
                <div class="card card-custom card-stretch card-stretch-half gutter-b">
                    <div class="card-header align-items-center border-0">
                        <h3 class="card-title align-items-start flex-column">
                            {{stats.reseller.name}}
                        </h3>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row">
                            <div class="col-5">
                                Số user đã tạo:
                            </div>
                            <div class="col-7">
                                {{stats.reseller.num_user_created}}
                            </div>
                            <div class="col-5">
                                Số mã bảo mật đã tạo:
                            </div>
                            <div class="col-7">
                                {{stats.codes.length}}
                            </div>
                            <div class="col-5">
                                Tổng số credit đã nạp:
                            </div>
                            <div class="col-7">
                                {{stats.reseller.num_user_max}}
                            </div>
                            <div class="col-5">
                                Tổng số credit đã sử dụng:
                            </div>
                            <div class="col-7">
                                {{totalUsed}}
                            </div>
                            <div class="col-5">
                                Tổng số credit còn lại:
                            </div>
                            <div class="col-7">
                                {{stats.reseller.num_user_max - totalUsed}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-8">
                <div class="card card-custom card-stretch card-stretch-half gutter-b">
                    <div class="card-header align-items-center border-0 d-flex justify-content-between">
                        <h3 class="card-title align-items-start flex-column">
                            Mã bảo mật
                        </h3>
                        <router-link :to="'/student-code'"><i class="la la-angle-right"></i></router-link>
                    </div>
                    <div class="card-body pt-4">
                        <template v-if="stats && stats.codes.length > 0">
                            <div class="row mb-2"
                                 v-for="(code, index) in stats.codes"
                                 :key="index">
                                <div class="col-4 font-weight-bold">{{code.code}}</div>
                                <div class="col-4 text-danger" v-if="code.status == 2">Đã bị khóa</div>
                                <div class="col-4 text-success" v-else-if="code.status== 1">Đang sử dụng</div>
                                <div class="col-4 text-warning" v-else>Chưa sử dụng</div>
                                <div class="col-4 " :class="code.used_number < code.max_user ? 'text-success' : 'text-danger'">
                                    {{code.used_number}}/{{code.max_user}}
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="d-flex justify-content-between">
                                <span> Chưa tạo mã bảo mật.</span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
  import axios from 'axios'

  export default {
    name: 'ResellerStats',
    data() {
      return {
        stats: null
      }
    },
    computed: {
      totalUsed() {
        return parseInt(this.stats.totalCodeMax,10) + parseInt(this.stats.reseller.num_user_created,10)
      }
    },
    mounted() {
      this.loadData()
    },
    methods: {
      async loadData() {
        const { data } = await axios.post('/api/dashboard/reseller-stats')
        this.stats = data.data
      }
    }
  }
</script>
