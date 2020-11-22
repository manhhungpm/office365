<template>
    <the-modal ref="modal" :title="'Cấu hình license'"
               :onHidden="onModalHidden"
               :center="center"
    >
        <form class="m-form m-form--state m-form--label-align-right"
              @submit.prevent="validateForm">

            <div class="row">
                <div class="col-12"><label>License <span class="text-danger">(TICK CHỌN NHỮNG LICENSE KHÔNG DÙNG)</span></label>
                </div>
                <div class="col-md-6" v-for="item in arrLicense" data-height="150">
                    <div>
                        <div class="m-checkbox m-checkbox--primary">
                            <label class="m-checkbox m-checkbox--check-bold m-checkbox--state-brand">
                                <input type="checkbox" class="form-check-input itemChild"
                                       :value="item.id+ '//' + item.id"
                                       v-model="idLicenseParent"
                                >
                                {{ item.name }}
                                <span></span></label>

                            <div v-for="it in item.child" data-height="150">
                                <div class="m-checkbox m-checkbox--primary">
                                    <label class="m-checkbox m-checkbox--check-bold m-checkbox--state-brand">
                                        <input type="checkbox" class="form-check-input itemChild"
                                               :value="it.servicePlanId + '//' + item.id"
                                               v-model="idLicenseChild"
                                        >
                                        {{ it.servicePlanName }}
                                        <span></span></label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>

        <template slot="footer">
            <button type="button" class="btn btn-secondary" @click="hide">{{ $t('button.cancel')}}</button>
            <button type="button" class="btn btn-primary" @click="validateForm">
                {{('button.update')}}
            </button>
        </template>
    </the-modal>
</template>

<script>
    import Form from 'vform'

    import {SUCCESS} from '~/constants/code'
    import {notify, notifyTryAgain, notifyUpdateSuccess, notifyAddSuccess} from '~/helpers/bootstrap-notify'
    import axios from 'axios'

    const defaultAccount = {
        name: '',
        description: '',
        client_id: '',
        client_secret: '',
        tenant_id: ''
    }

    export default {
        name: 'AccountEditLicenseModal',
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
                form: new Form(defaultAccount),
                idLicenseParent: [],
                idLicenseChild: [],
                arrLicense: [],
            }
        },
        watch: {
            idLicenseParent(data) {
                console.log(data); //Có đc id của licensse đã chọn
                // if (data.length){
                //     $('.itemChild').prop('checked', true);
                // } else {
                //     $('.itemChild').prop('checked', false);
                // }
            },
            idLicenseChild(data) {
                console.log(data)
            }
        },
        mounted() {
            this.getLicense();
        },
        methods: {
            validateForm() {
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.updateLicense()
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
                this.form = new Form(defaultAccount)
                this.isEdit = false
                this.$validator.reset()
            },
            async updateLicense() {
                try {
                    const {data} = await this.form.post('/api/account/store')

                    if (data.code == SUCCESS) {
                        notifyAddSuccess('tài khoản')
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
            async getLicense() {
                try {
                    const {data} = await this.form.post('/api/account/get-license')
                    let $this = this
                    let arrData = data.value;

                    arrData.forEach(function (e) {
                        $this.arrLicense.push({
                            id: e.skuId,
                            name: e.skuPartNumber,
                            child: e.servicePlans
                        })
                    })

                    console.log(this.arrLicense)
                } catch (e) {
                    console.log(e);
                }

            }
        }
    }
</script>
