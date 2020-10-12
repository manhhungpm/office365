<template>
    <the-modal ref="modal" :title="isEdit ? 'Cập nhật Reseller' : 'Thêm Reseller'"
               :onHidden="onModalHidden">
        <form class="m-form m-form--fit m-form--state m-form--label-align-right" ref="form"
              autocomplete="off"
              @submit.prevent="validateForm">

            <form-control :label="$t('form.user.name')"
                          :data-vv-as="$t('form.user.name')"
                          name="name" :required="true"
                          v-model="form.name"
                          v-validate="{required:true,max:64,min:6,isUsername:true}"
                          :error="errors.first('name') || form.errors.get('name')"/>

            <form-control label="Mã Reseller"
                          data-vv-as="Mã Reseller"
                          name="code" :required="true"
                          v-model="form.code"
                          v-validate="{required:true,max:20,min:2}"
                          :error="errors.first('code') || form.errors.get('code')"/>

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

            <domain-chosen :required="true"
                           v-validate="'required'"
                           v-model="form.domains"
                           :error="errors.first('domains') || form.errors.get('domains')"/>

            <form-control :label="$t('form.user.password')"
                          :data-vv-as="$t('form.user.password')"
                          name="password"
                          type="password"
                          :required="true"
                          v-model="form.password"
                          v-validate="{ required:true,max:128,isPassword:true }"
                          v-if="!isEdit"
                          ref="password"
                          :error="errors.first('password')|| form.errors.get('password')"/>

            <form-control :label="$t('form.user.repassword')"
                          :data-vv-as="$t('form.user.repassword')"
                          name="password_confirmation"
                          type="password"
                          :required="true"
                          v-model="form.password_confirmation"
                          v-validate="'required|confirmed:password'"
                          v-if="!isEdit"
                          :error="errors.first('password_confirmation')|| form.errors.get('password_confirmation')"/>

            <form-control label="Số điện thoại"
                          data-vv-as="Số điện thoại"
                          name="phone"
                          v-model="form.phone"
                          v-validate="'max:25'"
                          :error="errors.first('phone') || form.errors.get('phone')"/>

            <form-control label="Credits"
                          data-vv-as="Credits"
                          name="num_user_max"
                          :required="true"
                          type="number"
                          v-model="form.num_user_max"
                          v-validate="'required|max_value:25'"
                          :error="errors.first('num_user_max') || form.errors.get('num_user_max')"/>

            <div class="form-group m-form__group" v-if="!isEdit">
                <label class="m-checkbox m-checkbox--state-success">
                    <input type="checkbox" v-model="form.status"> Active
                    <span></span>
                </label>
            </div>

            <form-control :label="$t('form.user.note')"
                          :data-vv-as="$t('form.user.note')"
                          type="textarea"
                          name="note" :required="false"
                          v-model="form.note"
                          v-validate="{max:1024}"
                          :error="errors.first('note') || form.errors.get('note')"/>

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
    import {API_USER_STORE, API_USER_EDIT, API_USER_GET_ROLE_LIST} from '~/constants/url'
    import {SUCCESS} from '~/constants/code'
    import {notify, notifyTryAgain, notifyUpdateSuccess, notifyAddSuccess} from '~/helpers/bootstrap-notify'

    const defaultUser = {
        name: '',
        display_name: '',
        email: '',
        phone: '',
        num_user_max: 0,
        password: '',
        password_confirmation: '',
        role: 'Reseller',
        domains: null,
        status: true,
        code: ''
    }

    export default {
        name: 'ResellerModal',
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
                form: new Form(defaultUser)
            }
        },
        mounted() {
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
                    this.$validator.detach('password')
                    this.$validator.detach('password_confirmation')

                    this.$nextTick(() => {
                        this.form = new Form(item)
                        this.isEdit = true
                    })
                }

                this.$refs.modal.show()
            },
            onModalHidden() {
                this.form = new Form(defaultUser)
                this.isEdit = false
                this.$validator.reset()
            },
            async addItem() {
                try {

                    let arrDomain = this.form.domains.map(function (e) {
                        return e.domain_id;
                    })

                    this.form.domain = arrDomain;

                    const {data} = await this.form.post(API_USER_STORE)

                    if (data.code == SUCCESS) {
                        notifyAddSuccess(this.$t('form.user.user'))
                        this.$refs.modal.hide()
                        this.onActionSuccess()
                    } else {
                        notifyTryAgain()
                    }
                } catch (e) {
                    const {status} = e.response

                    if (status != 422) {
                        notifyTryAgain()
                    }
                }
            },
            async updateItem() {
                try {
                    let arrDomain = this.form.domains.map(function (e) {
                        return e.domain_id;
                    })

                    this.form.domain = arrDomain;

                    const {data} = await this.form.post(API_USER_EDIT)

                    if (data.code == SUCCESS) {
                        notifyUpdateSuccess(this.$t('form.user.user'))
                        this.$refs.modal.hide()
                        this.onActionSuccess()
                    } else {
                        notifyTryAgain()
                    }
                } catch (e) {
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
