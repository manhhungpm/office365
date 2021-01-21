<template>
    <the-modal ref="modal" :title="'Cấu hình license'"
               :onHidden="onModalHidden"
    >
        <form class="m-form m-form--state m-form--label-align-right"
              @submit.prevent="validateForm">

            <div class="row">
                <div class="col-md-12"><span class="text-success">CẤU HÌNH HIỆN TẠI:</span>
                    <ul v-for="s in selectedLicense"><b>{{s.parent}}</b>:
                        <div class="text-danger">Không dùng:
                            <span v-for="c in s.child">{{c}},</span>
                        </div>

                    </ul>
                </div>

                <div class="col-12">
                    <span class="text-success">CẤU HÌNH MỚI:</span> <br>
                    <label>Chọn license
                        <span class="text-danger">(TICK CHỌN NHỮNG LICENSE DÙNG)</span>
                    </label>
                    <div class="row">
                        <div class="col-md-6" v-for="item in arrLicense" data-height="150">
                            <div>
                                <div class="m-checkbox m-checkbox--primary">
                                    <label class="m-checkbox m-checkbox--check-bold m-checkbox--state-brand">
                                        <input type="checkbox" class="form-check-input itemChild"
                                               :value="item.id+'>>'+item.name"
                                               v-model="form.idLicenseParent"
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
                                                       :value="it.servicePlanId + '>>' +it.servicePlanName + '//' + item.id "
                                                       v-model="form.idLicenseChild"
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
        assigned_licenses: null,
        idLicenseParent: [],
        idLicenseChild: [],
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
                arrLicense: [],
                selectedLicense: []
            }
        },
        watch: {},
        mounted() {
            // this.getLicense();
        },
        methods: {
            async show(item) {
                //Truyền id để load license của nó
                this.getLicense(item.id)
                //Id của account
                this.form.account_id = item.id
                //Hiển thị những thằng đã chọn
                await this.historyData(item.id)

                this.$refs.modal.show()
            },
            async getLicense(id) {
                try {
                    const {data} = await axios.post('/api/account/get-license',{
                        id: id
                    })
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
                    this.form.assigned_licenses = this.prepareData(this.form.idLicenseParent, this.form.idLicenseChild)

                    const {data} = await this.form.post('/api/license-config/add-license')

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
                let assignedLicenses = []

                //Tạo skuId
                parent.forEach(function (e) {
                    let pSplit = e.split(">>")
                    assignedLicenses.push({
                        disabledPlans: [],
                        skuId: pSplit[0],
                        name: pSplit[1]
                    })
                })

                //Tạo disabledPlans
                assignedLicenses.forEach(function (a) {
                    child.forEach(function (c) {
                        let eSplit = c.split("//") //con + id của cha
                        if (a.skuId == eSplit[1]) {
                            let childSplit = eSplit[0].split(">>") //id con + tên con
                            a.disabledPlans.push({
                                id: childSplit[0],
                                name: childSplit[1]
                            })
                        }
                    })
                })

                // console.log(assignedLicenses);

                return assignedLicenses;
            },
            validateForm() {
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.addLicense()
                    }
                })
            },
            async historyData(id) {
                const {data} = await axios.post('/api/license-config/listing-license',{
                    id: id
                })

                let arrData = data.data;
                let parentName = []
                let selected = []

                arrData.forEach(function (e) {
                    parentName.push(e.parent_name)
                })

                let uniqueParent =  [...new Set(parentName)];

                selected = uniqueParent.map(function (e) {
                    return {
                        parent: e,
                        child: []
                    }
                })

                selected.forEach(function (e) {
                    arrData.forEach(function (a) {
                        if (e.parent == a.parent_name){
                            e.child.push(a.child_name)
                        }
                    })
                })

                this.selectedLicense = selected

                console.log(selected)
            },
            hide() {
                $(this.$el).modal('hide')
            },
            onModalHidden() {
                this.form = new Form(defaultAccount)
                this.$validator.reset()
                this.selectedLicense = []
                this.arrLicense = []
            },
        }
    }
</script>
