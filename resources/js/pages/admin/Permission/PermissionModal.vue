<template>
    <the-modal ref="modal" :title="isEdit ? $t('form.permission.update_title') : $t('form.permission.insert_title')"
           :onHidden="onModalHidden">
        <form class="m-form m-form--state m-form--label-align-right"
              @submit.prevent="validateForm">

            <form-control :label="$t('form.permission.name')"
                          :data-vv-as="$t('form.permission.name')"
                          name="name" :required="true"
                          v-model="form.name"
                          v-validate="'required|max:100'"
                          :error="errors.first('name') || form.errors.get('name')"/>

            <form-control :label="$t('form.permission.display_name')"
                          :data-vv-as="$t('form.permission.display_name')"
                          name="display_name"
                          :required="true"
                          v-model="form.display_name"
                          v-validate="'required|max:255'"
                          :error="errors.first('display_name') || form.errors.get('display_name')"/>

            <form-control :label="$t('form.permission.description')"
                          :data-vv-as="$t('form.permission.description')"
                          name="description"
                          type="textarea"
                          v-model="form.description"
                          v-validate="'max:255'"
                          :error="errors.first('description')|| form.errors.get('description')"/>
        </form>

        <template slot="footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $t('button.cancel')}}</button>
            <button type="button" class="btn btn-primary" @click="validateForm">
                {{ isEdit ? $t('button.update'): $t('button.add')}}
            </button>
        </template>
    </the-modal>
</template>

<script>
    import Form from 'vform'
    import { API_PERMISSION_STORE, API_PERMISSION_EDIT } from '~/constants/url'
    import { SUCCESS } from '~/constants/code'
    import { notify, notifyTryAgain, notifyUpdateSuccess, notifyAddSuccess } from '~/helpers/bootstrap-notify'

    const defaultPermission = {
        name: '',
        display_name: '',
        description: ''
    }

    export default {
        name: 'PermissionModal',
        props: {
            onActionSuccess: {
                type: Function,
                default: () => {
                }
            }
        },
        data () {
            return {
                isEdit: false,
                form: new Form(defaultPermission)
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
            onModalHidden () {
                this.form = new Form(defaultPermission)
                this.isEdit = false
                this.$validator.reset()
            },
            async addItem () {
                try {
                    const { data } = await this.form.post(API_PERMISSION_STORE)

                    if (data.code == SUCCESS) {
                        notifyAddSuccess(this.$t('form.permission.permission'))
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
                    const { data } = await this.form.post(API_PERMISSION_EDIT)

                    if (data.code == SUCCESS) {
                        notifyUpdateSuccess(this.$t('form.permission.permission'))
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
