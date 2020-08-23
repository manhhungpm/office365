<template>
    <the-modal ref="renewPasswordModal" :title="$t('form.user.renew_password')"
           :onHidden="onModalHidden">
        <form class="m-form m-form--fit m-form--state m-form--label-align-right" ref="form"
              @submit.prevent="validateForm">

            <form-control :label="$t('form.user.display_name')"
                          :data-vv-as="$t('form.user.display_name')"
                          :isDisabled="true"
                          name="display_name"
                          :required="true"
                          v-model="form.display_name"
                          v-validate="'required|max:255'"
                          :error="errors.first('display_name') || form.errors.get('display_name')"/>

            <form-control :label="$t('form.user.password')"
                          :data-vv-as="$t('form.user.password')"
                          name="password"
                          type="password"
                          :required="true"
                          v-model="form.password"
                          v-validate="{required:true,max:128,isPassword:true }"
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

        </form>

        <template slot="footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $t('button.cancel')}}</button>
            <button type="button" class="btn btn-primary" @click="validateForm">
                {{ $t('button.update') }}
            </button>
        </template>
    </the-modal>
</template>

<script>
    import Form from 'vform'
    import {API_USER_UPDATE_PASSWORD} from '~/constants/url'
    import {SUCCESS} from '~/constants/code'
    import {notify, notifyTryAgain, notifyUpdateSuccess, notifyAddSuccess} from '~/helpers/bootstrap-notify';

    const defaultUser = {
        display_name: '',
        password: '',
        password_confirmation: '',
    }

    export default {
        name: 'RenewPasswordModal',
        props: {
            onActionSuccess: {
                type: Function,
                default: () => {
                }
            },
        },
        data() {
            return {
                form: new Form(defaultUser),
            }
        },
        mounted() {
        },
        methods: {
            validateForm() {
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.saveItem()
                    }
                })
            },
            show(item = null) {
                if (item != null) {
                    this.form = new Form(item)
                }
                this.$validator.reset()
                this.$refs.renewPasswordModal.show()
            },
            onModalHidden() {
                this.form = new Form(defaultUser)
                this.$validator.reset()
            },
            async saveItem() {
                try {
                    const {data} = await this.form.post(API_USER_UPDATE_PASSWORD)

                    if (data.code == SUCCESS) {
                        notifyUpdateSuccess(this.$t('form.user.update_password'))
                        this.$refs.renewPasswordModal.hide()
                        this.onActionSuccess()
                    } else {
                        notifyTryAgain()
                    }
                }
                catch (e) {
                    const {status} = e.response

                    if (status != 422) {
                        notifyTryAgain()
                    }
                }

            }
        },
        computed: {}
    }
</script>
