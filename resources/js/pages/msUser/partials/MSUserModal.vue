<template>
    <the-modal ref="modal" :title="isEdit ? 'Cập nhật người dùng' : 'Thêm người dùng'"
               :onHidden="onModalHidden"
               :center="center"
    >
        <form class="m-form m-form--state m-form--label-align-right"
              @submit.prevent="validateForm">
            <div class="form-row form-group">
                <div class="col-md-6">
                    <form-control label="Tên người dùng"
                                  data-vv-as="Tên người dùng"
                                  name="username" :required="true"
                                  v-model="form.username"
                                  v-validate="'required|max:100|isUsername'"
                                  :error="errors.first('username') || form.errors.get('userPrincipalName')"/>
                </div>
                <div class="col-md-6">
                    <domain-chosen :required="true"
                                   :multiple="false"
                                   :post-data="{isVerified: 1,reseller:this.$store.getters['auth/user'].id}"
                                   v-validate="'required'"
                                   v-model="form.domain"
                                   :error="errors.first('domain') || form.errors.get('domain')"/>
                </div>
                <div class="col-md-12 form-text text-muted" v-if="form.username && form.domain">
                    {{form.username}}@{{form.domain ? form.domain.id : ''}}
                </div>
            </div>

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
                          v-if="!isEdit"
                          v-validate="'required|confirmed:password'"
                          :error="errors.first('password_confirmation')|| form.errors.get('password_confirmation')"/>

            <form-control label="Tên hiển thị"
                          data-vv-as="Tên hiển thị"
                          name="displayName" :required="true"
                          v-model="form.displayName"
                          v-validate="'required|max:100'"
                          :error="errors.first('displayName') || form.errors.get('displayName')"/>

            <div class="form-group m-form__group" v-if="!isEdit">
                <label class="m-checkbox m-checkbox--state-success">
                    <input type="checkbox" v-model="form.accountEnabled"> Active
                    <span></span>
                </label>
            </div>

            <reseller-chosen v-model="form.reseller"
                             :required="true"
                             v-if="isAdmin"
                             name="reseller"
                             key="reseller"
                             :is-disabled="isEdit"
                             v-validate="'required'"
                             :error="errors.first('reseller') || form.errors.get('reseller_id')"
            />
            <template v-else>
                <div class="form-control-feedback text-danger" v-if="form.errors.get('reseller_id')">
                    {{form.errors.get('reseller_id')}}
                </div>
            </template>


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

    import {SUCCESS} from '~/constants/code'
    import {notify, notifyTryAgain, notifyUpdateSuccess, notifyAddSuccess} from '~/helpers/bootstrap-notify'
    import DomainChosen from '../../../components/elements/DomainChosen'
    import ResellerChosen from '../../../components/elements/ResellerChosen'

    const defaultMSUser = {
        displayName: '',
        username: '',
        domain: null,
        password: '',
        accountEnabled: true,
        reseller: null
    }

    export default {
        components: {ResellerChosen},
        name: 'MSUserModal',
        props: {
            center: {
                type: Boolean,
                default: false
            },
            onActionSuccess: {
                type: Function,
                default: () => {
                }
            }
        },
        data() {
            return {
                isEdit: false,
                form: new Form(defaultMSUser)
            }
        },
        computed: {
            isAdmin() {
                return this.$store.getters['auth/role'] === 'Admin'
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
                    this.$validator.detach('password')

                    this.$nextTick(() => {
                        let clone = Object.assign({}, item)
                        let arr = clone.userPrincipalName.split('@')
                        clone.username = arr[0]

                        this.form = new Form(clone)
                        this.isEdit = true
                    })
                }

                this.$refs.modal.show()
            },
            hide() {
                $(this.$el).modal('hide')
            },

            onModalHidden() {
                this.form = new Form(defaultMSUser)
                this.isEdit = false
                this.$validator.reset()
            },
            async addItem() {
                try {
                    this.form.userPrincipalName = this.form.username + '@' + this.form.domain.id
                    this.form.domain_id = this.form.domain.domain_id
                    if (this.form.reseller) {
                        this.form.reseller_id = this.form.reseller.id
                    } else {
                        this.form.reseller_id = this.$store.getters['auth/user'].id
                    }
                    const {data} = await this.form.post('/api/ms-user/store')

                    if (data.code == SUCCESS) {
                        notifyAddSuccess('người dùng')
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
                    this.form.userPrincipalName = this.form.username + '@' + this.form.domain.id
                    this.form.domain_id = this.form.domain.domain_id
                    const {data} = await this.form.post('/api/ms-user/edit')

                    if (data.code == SUCCESS) {
                        notifyUpdateSuccess('người dùng')
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
        }
    }
</script>
