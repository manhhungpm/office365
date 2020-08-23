<template>
    <the-modal ref="modal" :title="isEdit ? $t('form.user.update_title') : $t('form.user.insert_title')"
           :onHidden="onModalHidden">
        <form class="m-form m-form--fit m-form--state m-form--label-align-right" ref="form"
              @submit.prevent="validateForm">

            <form-control :label="$t('form.user.name')"
                          :data-vv-as="$t('form.user.name')"
                          name="name" :required="true"
                          v-model="form.name"
                          v-validate="{required:true,max:64,min:6,isUsername:true}"
                          :error="errors.first('name') || form.errors.get('name')"/>

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

            <div class="form-group m-form__group" :class="{'has-danger': errors.has('selectRole')}">
                <label>{{ $t('form.user.select_role') }} <span class="text-danger">(*)</span></label>
                <select2
                        name="selectRole"
                        ref="selectRole"
                        v-model="form.role"
                        :options="roleList"
                        v-validate="'required'"
                        :textField="'display_name'"
                        v-if="!isLoading"
                >
                </select2>
                <div class="form-control-feedback error-message" v-cloak
                     v-show="errors.has('userForm.selectRole')">{{errors.first('selectRole') }}
                </div>
            </div>

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
    import axios from 'axios'

    const defaultUser = {
        name: '',
        display_name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role: null
    }

    export default {
        name: 'UserModal',
        props: {
            onActionSuccess: {
                type: Function,
                default: () => {
                }
            },
        },
        data() {
            return {
                isEdit: false,
                form: new Form(defaultUser),
                roleList: [],
                isLoading: true
            }
        },
        mounted() {
        },
        created: function () {
            this.loadData();
        },
        methods: {
            async loadData() {
                try {
                    let res = await axios.get(API_USER_GET_ROLE_LIST)
                    let {data} = res
                    this.roleList = data.data
                    this.isLoading = false
                } catch (e) {

                }
            },
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
                    if (item.roles.length > 0) {
                        this.form.role = {
                            'id': item.roles[0].id,
                            'display_name': item.roles[0].display_name
                        }
                    } else {
                        this.form.role = null
                    }
                } else {
                    if (this.roleList.length > 0) {
                        this.form.role = this.roleList[0]
                    }
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
                    const {data} = await this.form.post(API_USER_STORE)

                    if (data.code == SUCCESS) {
                        notifyAddSuccess(this.$t('form.user.user'))
                        this.$refs.modal.hide()
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
            },
            async updateItem() {
                try {
                    const {data} = await this.form.post(API_USER_EDIT)

                    if (data.code == SUCCESS) {
                        notifyUpdateSuccess(this.$t('form.user.user'))
                        this.$refs.modal.hide()
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
