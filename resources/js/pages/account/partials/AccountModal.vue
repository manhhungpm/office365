<template>
    <the-modal ref="modal" :title="isEdit ? 'Cập nhật tài khoản Office 265' : 'Thêm tài khoản Office 365'"
           :onHidden="onModalHidden"
           :center="center"
    >
        <form class="m-form m-form--state m-form--label-align-right"
              @submit.prevent="validateForm">

            <form-control label="Tên ứng dụng"
                          data-vv-as="Tên ứng dụng"
                          name="app_name" :required="true"
                          v-model="form.app_name"
                          v-validate="'required|max:100'"
                          :error="errors.first('app_name') || form.errors.get('app_name')"/>

            <form-control label="Mô tả"
                          data-vv-as="Mô tả"
                          name="description"
                          type="textarea"
                          v-model="form.description"
                          v-validate="'max:255'"
                          :error="errors.first('description')|| form.errors.get('description')"/>

            <form-control label="Client ID"
                          data-vv-as="Client ID"
                          name="client_id" :required="true"
                          v-model="form.client_id"
                          v-validate="'required|max:100'"
                          :error="errors.first('client_id') || form.errors.get('client_id')"/>

            <form-control label="Client Secret"
                          data-vv-as="Client Secret"
                          name="client_secret" :required="true"
                          v-model="form.client_secret"
                          v-validate="'required|max:100'"
                          :error="errors.first('client_secret') || form.errors.get('client_secret')"/>

            <form-control label="Tenant ID"
                          data-vv-as="Tenant ID"
                          name="tenant_id" :required="true"
                          v-model="form.tenant_id"
                          v-validate="'required|max:100'"
                          :error="errors.first('tenant_id') || form.errors.get('tenant_id')"/>
        </form>

        <template slot="footer">
            <button type="button" class="btn btn-secondary" @click="hide">{{ $t('button.cancel')}}</button>
            <button type="button" class="btn btn-primary" @click="validateForm">
                {{ isEdit ? $t('button.update'): $t('button.add')}}
            </button>
        </template>
    </the-modal>
</template>

<script>
  import Form from 'vform'

  import { SUCCESS } from '~/constants/code'
  import { notify, notifyTryAgain, notifyUpdateSuccess, notifyAddSuccess } from '~/helpers/bootstrap-notify'

  const defaultAccount = {
    name: '',
    description: '',
    client_id: '',
    client_secret: '',
    tenant_id: ''
  }

  export default {
    name: 'AccountModal',
    props: {
      center:{
        type: Boolean,
        default: false
      },
      onActionSuccess: {
        type: Function,
        default: () => {
        }
      }
    },
    data () {
      return {
        isEdit: false,
        form: new Form(defaultAccount)
      }
    },
    methods: {
      validateForm () {
        this.$validator.validateAll().then((result) => {
          if (result) {
            if (this.isEdit) {
              this.updateItem()
            } else {
              this.addItem()
            }
          }
        })
      },
      show (item = null) {
        if (item != null) {
          this.form = new Form(item)
          this.isEdit = true
        }

        this.$refs.modal.show()
      },
      hide () {
        $(this.$el).modal('hide')
      },

      onModalHidden () {
        this.form = new Form(defaultAccount)
        this.isEdit = false
        this.$validator.reset()
      },
      async addItem () {
        try {
          const { data } = await this.form.post('/api/account/store')

          if (data.code == SUCCESS) {
            notifyAddSuccess('tài khoản')
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
      async updateItem () {
        try {
          const { data } = await this.form.post('/api/account/edit')

          if (data.code == SUCCESS) {
            notifyUpdateSuccess('tài khoản')
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

      }
    }
  }
</script>