<template>
    <the-modal ref="modal" :title="'Cấu hình license'"
               :onHidden="onModalHidden"
               :center="center"
    >
        <form class="m-form m-form--state m-form--label-align-right"
              @submit.prevent="validateForm">

            <div class="row">
                <div class="col-12">
                    <label>Chọn license
                        <span class="text-danger">(TICK CHỌN NHỮNG LICENSE DÙNG)</span>
                    </label>
                    <div class="row">
                        <div class="col-md-6" v-for="item in arrLicense" data-height="150">
                            <div>
                                <div class="m-checkbox m-checkbox--primary">
                                    <label class="m-checkbox m-checkbox--check-bold m-checkbox--state-brand">
                                        <input type="checkbox" class="form-check-input itemChild"
                                               :value="item.id"
                                               v-model="idLicenseParent"
                                        >
                                        {{ item.name }}
                                        <span></span></label>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-12">
                    <label>License con
                        <span class="text-danger">(TICK CHỌN NHỮNG LICENSE KHÔNG DÙNG)</span>
                    </label>
                    <div class="row">
                        <div class="col-md-6" v-for="item in arrLicense" data-height="150">
                            <div>
                                <div class="m-checkbox m-checkbox--primary">
                                    <label>
                                        <b>{{ item.name }}</b>
                                    </label>

                                    <div v-for="it in item.child" data-height="150">
                                        <div class="m-checkbox m-checkbox--primary">
                                            <label class="m-checkbox m-checkbox--check-bold m-checkbox--state-brand">
                                                <input type="checkbox" class="form-check-input itemChild"
                                                       :value="it.servicePlanId + '//' + item.id"
                                                       v-model="idLicenseChild"
                                                >
                                                {{ it.servicePlanName }}
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
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
                Cập nhật
            </button>
        </template>
    </the-modal>
</template>

<script>
    import Form from 'vform'

    import {SUCCESS} from '~/constants/code'
    import {notify, notifyTryAgain, notifyUpdateSuccess, notifyAddSuccess} from '~/helpers/bootstrap-notify'
    import axios from 'axios'
    import FormControl from "../../../components/common/FormControl";

    const defaultAccount = {
        assigned_licenses: null
    }

    export default {
        name: 'AccountEditLicenseModal',
        components: {FormControl},
        props: {
            appName: {
                type: String,
                default: null
            },
            onActionSuccess: {
                type: Function,
                default: () => {
                }
            }
        },
        data() {
            return {
                form: new Form(defaultAccount),
                idLicenseParent: [],
                idLicenseChild: [],
                arrLicense: [],
            }
        },
        watch: {
            idLicenseParent(data) {
                // console.log(data); //Có đc id của licensse cha đã chọn
            },
            idLicenseChild(data) {
                // console.log(data) //Có đc id của licensse con đã chọn
            }
        },
        mounted() {
            this.getLicense();
        },
        methods: {
            async getLicense() {
                try {
                    const {data} = await axios.post('/api/account/get-license')
                    let $this = this
                    let arrData = data.value;

                    arrData.forEach(function (e) {
                        $this.arrLicense.push({
                            id: e.skuId,
                            name: e.skuPartNumber,
                            child: e.servicePlans
                        })
                    })

                    // console.log(this.arrLicense)
                } catch (e) {
                    console.log(e);
                }
            },
            async addLicense() {
                try {

                    this.form.assigned_licenses = this.prepareData(this.idLicenseParent, this.idLicenseChild)

                    const {data} = await this.form.post('/api/license-config/add')

                    if (data.code == SUCCESS) {
                        notifyUpdateSuccess('cấu hình')
                        this.$refs.modal.hide()
                        this.onActionSuccess()
                    } else {
                        notifyTryAgain()
                    }
                } catch (e) {
                    console.log(e)
                }
            },
            prepareData(parent, child) {
                // console.log(parent)
                // console.log(child[0].split("//"))
                let assignedLicenses = []

                //Tạo skuId
                parent.forEach(function (e) {
                    assignedLicenses.push({
                        disabledPlans: [],
                        skuId: e
                    })
                })

                //Tạo disabledPlans
                assignedLicenses.forEach(function (a) {
                    child.forEach(function (c) {
                        let eSplit = c.split("//")
                        if(a.skuId == eSplit[1]){
                            a.disabledPlans.push(eSplit[0])
                        }
                    })
                })

                console.log(assignedLicenses);

                return assignedLicenses;

            },
            validateForm() {
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.addLicense()
                    }
                })
            },
            show(item = null) {
                console.log(item)
                if (item != null) {
                    this.form = new Form(item)
                }

                this.$refs.modal.show()
            },
            hide() {
                $(this.$el).modal('hide')
            },
            onModalHidden() {
                this.form = new Form(defaultAccount)
                this.$validator.reset()
            },
        }
    }
</script>
