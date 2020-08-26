<template>
    <the-modal ref="modal" :title="isEdit ? 'Cập nhật mã bảo mật' : 'Thêm mã bảo mật'"
               :onHidden="onModalHidden"
    >
        <form class="m-form m-form--state m-form--label-align-right"
              @submit.prevent="validateForm">
            <form-control
                v-if="isEdit"
                label="Mã bảo mật"
                data-vv-as="Mã bảo mật"
                name="code"
                v-model="form.code"
                :is-disabled="isEdit"
            />

            <reseller-chosen v-model="form.reseller"
                             :required="true"
                             name="reseller"
                             key="reseller"
                             v-validate="'required'"
                             :error="errors.first('reseller') || form.errors.get('reseller_id')"
                             :is-disabled="isEdit || isReseller"
            />

            <domain-chosen :required="true"
                           :multiple="false"
                           :is-disabled="isEdit"
                           :postData="{reseller: form.reseller ? form.reseller.id : null}"
                           v-validate="'required'"
                           v-model="form.domain"
                           :error="errors.first('domain') || form.errors.get('domain_id')"/>

            <form-control label="Số MSuser tối đa"
                          type="number"
                          data-vv-as="Số MSuser tối đa"
                          name="max_number"
                          placeholder="Nhập số MSuser tối đa"
                          :required="true"
                          v-model="form.max_user"
                          v-validate="'required|min:1|max:1000|numeric'"
                          :error="errors.first('max_user') || form.errors.get('max_user')"/>

            <form-control label="Ngày hết hạn"
                          type="datepicker"
                          data-vv-as="Ngày hết hạn"
                          name="expired_date"
                          placeholder="Nhập ngày hết hạn"
                          v-model="form.expired_date"
                          :error="errors.first('expired_date') || form.errors.get('expired_date')"/>


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
    import moment from 'moment'
    import {mapGetters} from 'vuex'
    import {ROLE_RESELLER} from '~/constants/roles'
    import cloneDeep from 'lodash/cloneDeep'
    import {SUCCESS} from '~/constants/code'
    import {notify, notifyTryAgain, notifyUpdateSuccess, notifyAddSuccess} from '~/helpers/bootstrap-notify'

    const defaultStudentCode = {
        max_user: 1,
        reseller: null,
        domain: null,
        expired_date: null
    }

    export default {
        name: 'StudentCodeModal',
        props: {
            onActionSuccess: {
                type: Function,
                default: () => {
                }
            }
        },
        data() {
            return {
                form: new Form(defaultStudentCode),
                isEdit: false
            }
        },
        computed: {
            ...mapGetters({
                role: 'auth/role',
                user: 'auth/user'
            }),
            isReseller() {
                return this.role == ROLE_RESELLER
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
            async show(item = null) {
                if (item != null) {
                    this.form = new Form(item)
                    await this.$nextTick()
                    this.form.domain = cloneDeep(item.domain)
                    this.form.expired_date = item.expired_date ? moment(item.expired_date, 'YYYY-MM-DD').format('DD/MM/YYYY') : null
                    this.isEdit = true
                } else {
                    if (this.isReseller) {
                        this.form.reseller = this.user
                    }
                }
                this.$refs.modal.show()
            },
            hide() {
                $(this.$el).modal('hide')
            },
            onModalHidden() {
                this.form = new Form(defaultStudentCode)
                this.isEdit = false
                this.$validator.reset()
            },
            async addItem() {
                try {
                    this.form.domain_id = this.form.domain.domain_id
                    this.form.reseller_id = this.form.reseller ? this.form.reseller.id : null

                    const {data} = await this.form.post('/api/student-code/store')

                    if (data.code == SUCCESS) {
                        notifyAddSuccess('mã bảo mật')
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
                    this.form.domain_id = this.form.domain.domain_id
                    this.form.reseller_id = this.form.reseller ? this.form.reseller.id : null

                    const {data} = await this.form.post('/api/student-code/edit')

                    if (data.code == SUCCESS) {
                        notifyUpdateSuccess('mã bảo mật')
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
        watch: {
            'form.reseller'(val) {
                this.form.domain = null
            }
        }
    }
</script>
