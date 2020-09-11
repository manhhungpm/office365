<template>
    <div class="m-grid__item m-grid__item--fluid  m-grid__item--order-tablet-and-mobile-1 m-login__wrapper d-flex">
        <div class="offset-3 col-6 align-self-center">
            <the-portlet title="Tạo tài khoản Microsoft">
                <form class="m-form m-form--fit m-form--state m-form--label-align-right" ref="form"
                      @submit.prevent="validateForm">
                    <form-control label="Mã bảo mật"
                                  data-vv-as="Mã bảo mật"
                                  name="code"
                                  :required="true"
                                  v-model="form.code"
                                  v-validate="{required:true}"
                                  :is-group="true"
                                  :prepend="false"
                                  :error="errors.first('code') || form.errors.get('code')"
                                  :input-class="{
                                    'is-valid' : isCorrectCode && checked,
                                    'is-invalid' : !isCorrectCode && checked
                                  }"
                    >
                        <button class="btn btn-primary"
                                slot="input-group-append"
                                title="Xác nhận"
                                @click.prevent="checkCode"

                        >
                            <i class="la la-angle-right"></i>
                        </button>
                    </form-control>
                    <template v-if="checked">
                        <template v-if="isCorrectCode">
                            <div class="form-group text-success m-form__group">
                               Domain: {{this.form.domain.id}}
                            </div>
                            <div class="form-row form-group">
                                <div class="col-md-12">
                                    <form-control label="Tên người dùng"
                                                  data-vv-as="Tên người dùng"
                                                  name="username" :required="true"
                                                  v-model="form.username"
                                                  v-validate="'required|max:100|isUsername'"
                                                  :error="errors.first('username') || form.errors.get('userPrincipalName')"/>
                                    <div class="m-form__group form-text text-muted" v-if="form.username && form.domain">
                                        {{form.username}}@{{form.domain ? form.domain.id : ''}}
                                    </div>
                                </div>


                            </div>

                            <form-control :label="$t('form.user.password')"
                                          :data-vv-as="$t('form.user.password')"
                                          name="password"
                                          type="password"
                                          :required="true"
                                          v-model="form.password"
                                          v-validate="{ required:true,max:128,isPassword:true }"
                                          ref="password"
                                          :error="errors.first('password')|| form.errors.get('password')"/>

                            <form-control :label="$t('form.user.repassword')"
                                          :data-vv-as="$t('form.user.repassword')"
                                          name="password_confirmation"
                                          type="password"
                                          :required="true"
                                          v-model="form.password_confirmation"
                                          v-validate="'required|confirmed:password'"
                                          :error="errors.first('password_confirmation')|| form.errors.get('password_confirmation')"/>

                            <form-control label="Tên hiển thị"
                                          data-vv-as="Tên hiển thị"
                                          name="displayName" :required="true"
                                          v-model="form.displayName"
                                          v-validate="'required|max:100'"
                                          :error="errors.first('displayName') || form.errors.get('displayName')"/>

                            <div class="form-group m-form__group">
                                <label class="m-checkbox m-checkbox--state-success">
                                    <input type="checkbox" v-model="form.accountEnabled"> Active
                                    <span></span>
                                </label>
                            </div>



                        </template>
                        <template v-else>
                            <div class="form-group m-form__group">
                                <div class="text-danger">Mã bảo mật hết hạn hoặc quá số người dùng cho phép tạo</div>
                            </div>
                        </template>
                    </template>
                    <div class="d-flex justify-content-between form-group m-form__group align-items-center">
                        <router-link class="m-link" :to="'/login'">Trở về đăng nhập
                        </router-link>
                        <button type="button" class="btn btn-primary" @click="validateForm"
                                v-if="isCorrectCode && checked">
                            Tạo
                        </button>
                    </div>
                </form>
            </the-portlet>
        </div>
    </div>
</template>
<script>
  import Form from 'vform'
  import { API_USER_UPDATE_PROFILE } from '~/constants/url'
  import { SUCCESS } from '~/constants/code'
  import { notify, notifyTryAgain, notifyUpdateSuccess, notifyAddSuccess } from '~/helpers/bootstrap-notify'
  import axios from 'axios'
  import cloneDeep from 'lodash/cloneDeep'

  const defaultMSUser = {
    code: '',
    displayName: '',
    username: '',
    domain: null,
    password: '',
    accountEnabled: true
  }

  export default {
    name: 'CreateMsUser',
    layout: 'auth',

    metaInfo() {
      return { title: 'Tạo người dùng MS' }
    },
    data() {
      return {
        checked: false,
        isCorrectCode: false,
        form: new Form(defaultMSUser),
        isLoading: true,
      }
    },
    mounted() {
      // this.fetchUser()
    },
    watch: {
      'form.code': {
        handler(value) {
          this.isCorrectCode = false
          this.checked = false
          this.form = new Form(defaultMSUser)
          this.form.code = value
          this.$validator.reset()

        }
      }
    },
    methods: {
      // fetchUser() {
      //   const user = this.$store.getters['auth/user']
      //   this.form = new Form(cloneDeep(user))
      // },
      validateForm() {
        this.$validator.validateAll().then((result) => {
          if (result) {
            this.addItem()
          }
        })
      },
      async checkCode() {
        const { data } = await this.form.post('/api/student-code/check')
        this.checked = true
        if (data.code == SUCCESS) {
          this.isCorrectCode = true
          this.form.domain = data.data
        } else {
          this.isCorrectCode = false
        }
      },
      async addItem() {
        try {
          this.form.userPrincipalName = this.form.username + '@' + this.form.domain.id
          this.form.domain_id = this.form.domain.domain_id
          const code = this.form.code.slice()
          const { data } = await this.form.post('/api/ms-user/guest-store')

          if (data.code == SUCCESS) {
            notifyAddSuccess('người dùng Microsoft')
            this.form.code = null
            await this.$nextTick()
            this.form.code = code
          } else {
            notifyTryAgain()
          }
        } catch (e) {
          const { status } = e.response

          if (status != 422) {
            notifyTryAgain()
          }
        }
      },
      // showModal() {
      //   this.$refs.modal.show()
      // }
    },
    computed: {}
  }
</script>
