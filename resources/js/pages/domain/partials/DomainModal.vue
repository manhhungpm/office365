<template>
    <the-modal ref="modal" :title="isEdit ? 'Cập nhật domain' : 'Thêm domain'"
               :onHidden="onModalHidden"
    >
        <form class="m-form m-form--state m-form--label-align-right"
              @submit.prevent="validateForm">

            <form-control label="Domain"
                          data-vv-as="Domain"
                          name="id" :required="true"
                          v-model="form.id"
                          v-validate="'required|max:100'"
                          :error="errors.first('id') || form.errors.get('id')"/>

            <account-chosen v-model="form.account"
                            v-validate="'required'"
                            :error="errors.first('account')"/>
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

  const defaultDomain = {
    id: '',
    account: null
  }

  export default {
    name: 'DomainModal',
    props: {
      onActionSuccess: {
        type: Function,
        default: () => {
        }
      }
    },
    data() {
      return {
        isEdit: false,
        form: new Form(defaultDomain)
      }
    },
    methods: {
      validateForm() {
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
      show(item = null) {
        if (item != null) {
          this.form = new Form(item)
          this.isEdit = true
        }

        this.$refs.modal.show()
      },
      hide() {
        $(this.$el).modal('hide')
      },
      onModalHidden() {
        this.form = new Form(defaultDomain)
        this.isEdit = false
        this.$validator.reset()
      },
      async addItem() {
        try {
          this.form.account_id = this.form.account.id
          const { data } = await this.form.post('/api/domain/store')

          if (data.code == SUCCESS) {
            notifyAddSuccess('domain')
            this.$refs.modal.hide()
            this.onActionSuccess()
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
      async updateItem() {
        try {
          const { data } = await this.form.post('/api/domain/edit')

          if (data.code == SUCCESS) {
            notifyUpdateSuccess('domain')
            this.$refs.modal.hide()
            this.onActionSuccess()
          } else {
            notifyTryAgain()
          }
        } catch (e) {
          const { status } = e.response

          if (status != 422) {
            notifyTryAgain()
          }
        }

      }
    }
  }
</script>