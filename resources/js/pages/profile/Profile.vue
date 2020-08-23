<template>
    <div>
        <the-portlet title="Thông tin cá nhân">
            <v-button color="accent" style-type="air"
                      class="m-btn--custom m-btn--icon"
                      slot="tool"
                      @click.native="showModal">
                    <span>
                        <i class="la la-lock"></i>
                        <span>Đổi mật khẩu</span>
                    </span>
            </v-button>
            <form class="m-form m-form--fit m-form--state m-form--label-align-right offset-3 col-6" ref="form"
                  @submit.prevent="validateForm">

                <form-control :label="$t('form.user.name')"
                              :data-vv-as="$t('form.user.name')"
                              name="name" :required="true"
                              v-model="form.name"
                              :is-disabled="true"
                />

                <form-control :label="$t('form.user.display_name')"
                              :data-vv-as="$t('form.user.display_name')"
                              name="display_name"
                              :required="true"
                              v-model="form.display_name"
                              v-validate="'required|max:255'"
                              :error="errors.first('display_name') || form.errors.get('display_name')"/>

                <form-control :label="$t('form.user.email')"
                              :data-vv-as="$t('form.user.email')"
                              name="email"
                              :required="true"
                              v-model="form.email"
                              v-validate="'required|max:255|email'"
                              :error="errors.first('email') || form.errors.get('email')"/>

                <form-control label="Số điện thoại"
                              :data-vv-as="'Số điện thoại'"
                              name="phone"
                              :required="true"
                              v-model="form.phone"
                              v-validate="'max:15|numeric'"
                              :error="errors.first('phone') || form.errors.get('phone')"/>
                <div class="d-flex justify-content-end form-group m-form__group">
                    <button type="button" class="btn btn-primary" @click="validateForm">
                        <span v-t="'button.update'"></span>
                    </button>
                </div>
            </form>
        </the-portlet>
        <change-password-modal ref="modal"></change-password-modal>
    </div>
</template>
<script>
  import Form from 'vform'
  import { API_USER_UPDATE_PROFILE } from '~/constants/url'
  import { SUCCESS } from '~/constants/code'
  import { notify, notifyTryAgain, notifyUpdateSuccess, notifyAddSuccess } from '~/helpers/bootstrap-notify'
  import axios from 'axios'
  import cloneDeep from 'lodash/cloneDeep'
  import ChangePasswordModal from './partials/ChangePasswordModal'

  const defaultUser = {
    name: '',
    display_name: '',
    email: '',
    phone: ''
  }

  export default {
    components: {
      ChangePasswordModal
    },
    name: 'Profile',
    layout: 'default',
    middleware: 'auth',
    metaInfo() {
      return { title: 'Thông tin cá nhân' }
    },
    data() {
      return {
        isEdit: false,
        form: new Form(defaultUser),
        isLoading: true
      }
    },
    mounted() {
      this.fetchUser()
    },
    methods: {
      fetchUser() {
        const user = this.$store.getters['auth/user']
        this.form = new Form(cloneDeep(user))
      },
      validateForm() {
        this.$validator.validateAll().then((result) => {
          if (result) {
            this.updateItem()
          }
        })
      },
      async updateItem() {
        try {
          const { data } = await this.form.post(API_USER_UPDATE_PROFILE)

          if (data.code == SUCCESS) {
            notifyUpdateSuccess('thông tin')
            this.$refs.modal.hide()
            this.onActionSuccess()
          } else {
            notifyTryAgain()
          }
        }
        catch (e) {
          const { status } = e.response

          if (status != 422) {
            notifyTryAgain()
          }
        }

      },
      showModal() {
        this.$refs.modal.show()
      }
    },
    computed: {}
  }
</script>