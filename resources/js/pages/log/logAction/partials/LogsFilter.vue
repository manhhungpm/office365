<template>
    <div>
        <div class="row">

            <user-chosen class="col-3"
                         v-model="form.username"
                         :has-all-option="true"
            >

            </user-chosen>

            <form-control label="Chọn loại đối tượng"
                          data-vv-as="Chọn loại đối tượng"
                          :required="false"
                          name="className"
                          class="col-3"
                          :type="'select'"
                          :select-options="classNameOption"
                          v-model="form.className"
                          :error="errors.first('className') || form.errors.get('className')"/>

            <form-control label="Chọn loại hành động"
                          data-vv-as="Chọn loại hành động"
                          :required="false"
                          name="actionType"
                          class="col-3"
                          :type="'select'"
                          :select-options="actionTypeOption"
                          v-model="form.actionType"
                          :error="errors.first('actionType') || form.errors.get('actionType')"/>

            <div class="form-group m-form__group col-3"
                 :class="{'has-danger': errors.first('timeFrom')|| form.errors.get('timeFrom') || errors.first('timeTo')|| form.errors.get('timeTo')}">
                <label>Khoảng ngày</label>
                <div class="input-group">
                    <date-picker
                            name="timeFrom"
                            ref="timeFrom"
                            :language="'vi'"
                            v-model="form.timeFrom"
                            :placeholder="'Từ ngày'"
                            :data-vv-as="'Ngày bắt đầu'"
                            v-validate="'date_format:dd/MM/yyyy|'"
                    />
                    <div class="input-group-prepend">
                        <span class="input-group-text"> - </span>
                    </div>
                    <date-picker
                            name="timeTo"
                            ref="timeTo"
                            :language="'vi'"
                            v-model="form.timeTo"
                            :placeholder="'Đến ngày'"
                            :data-vv-as="'Ngày kết thúc'"
                            v-validate="'date_format:dd/MM/yyyy|afterDate:timeFrom'"
                    />
                </div>
                <div class="form-control-feedback"
                     v-if=" errors.first('timeFrom') || form.errors.get('timeFrom') ">
                    {{errors.first('timeFrom')}}
                </div>

                <div class="form-control-feedback"
                     v-if=" errors.first('timeTo') || form.errors.get('timeTo') ">
                    {{errors.first('timeTo')}}
                </div>

            </div>

        </div>
        <div class="text-right ">
            <button class="btn btn-primary " style="margin-right: 10px" @click="validateForm">Tìm kiếm</button>
            <button class="btn btn-secondary " @click="reset">Reset</button>
        </div>
    </div>
</template>

<script>
    import moment from 'moment'
    import Form from 'vform'
    import {toNormalDate} from '~/helpers/datetimeFormat';
    import {SUCCESS} from '~/constants/code'
    import {notify, notifyAddSuccess, notifyTryAgain, notifyUpdateSuccess} from '~/helpers/bootstrap-notify'
    import {downloadFile} from '~/helpers/downloadFile'


    const defaultLogs = {
        actionType: null,
        timeFrom: null,
        timeTo: null,
        username: null,
        className: null,
    }

    export default {
        name: 'LogsFilter',
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
                service: null,
                form: new Form(defaultLogs),
                loadingExport: false,
                actionTypeOption: {
                    placeholder: 'Chọn loại hành động',
                    multiple: false,
                    searchable: false,
                    options: [
                        {
                            id: -1,
                            text: 'Tất cả',
                            value: 'All'
                        },
                        {
                            id: 1,
                            text: 'Cập nhật',
                            value: 'Update'
                        },
                        {
                            id: 2,
                            text: 'Đăng nhập',
                            value: 'Login'
                        },
                        {
                            id: 3,
                            text: 'Đăng xuất',
                            value: 'Logout'
                        },
                        {
                            id: 4,
                            text: 'Thêm',
                            value: 'Add'
                        },
                        {
                            id: 5,
                            text: 'Xóa',
                            value: 'Delete'
                        },
                    ]
                },
                classNameOption: {
                    placeholder: 'Chọn loại đối tượng',
                    multiple: false,
                    searchable: false,
                    options: [
                        {
                            id: -1,
                            text: 'Tất cả',
                            value: 'All'
                        },
                        {
                            id: 1,
                            text: 'Bộ phận',
                            value: 'departments'
                        },
                        {
                            id: 2,
                            text: 'Dịch vụ',
                            value: 'services'
                        },
                        {
                            id: 3,
                            text: 'Chi tiết đơn hàng',
                            value: 'order_details'
                        },
                        {
                            id: 4,
                            text: 'Dải MSISDN',
                            value: 'msisdns'
                        },
                        {
                            id: 5,
                            text: 'Dải GT NODE',
                            value: 'gt_nodes'
                        },
                        {
                            id: 6,
                            text: 'Đối tác',
                            value: 'partners'
                        },
                        {
                            id: 7,
                            text: 'Đơn hàng',
                            value: 'orders'
                        },
                        {
                            id: 8,
                            text: 'Hợp đồng',
                            value: 'contracts'
                        },
                        {
                            id: 9,
                            text: 'Kho số và Alias',
                            value: 'sender_ids'
                        },
                        {
                            id: 10,
                            text: 'Loại hợp đồng',
                            value: 'contract_types'
                        },
                        {
                            id: 11,
                            text: 'Luồng triển khai dịch vụ',
                            value: 'service_deploys'
                        },
                        {
                            id: 12,
                            text: 'Mẫu hợp đồng',
                            value: 'contract_templates'
                        },
                        {
                            id: 13,
                            text: 'Nhóm người dùng',
                            value: 'roles'
                        },
                        {
                            id: 14,
                            text: 'Nhóm sản phẩm',
                            value: 'product_groups'
                        },
                        {
                            id: 15,
                            text: 'Nhà mạng',
                            value: 'providers'
                        },
                        {
                            id: 16,
                            text: 'Người dùng',
                            value: 'users'
                        },
                        {
                            id: 17,
                            text: 'Phụ lục chiết khấu',
                            value: 'discounts'
                        },
                        {
                            id: 18,
                            text: 'Phụ lục giá bán',
                            value: 'service_prices'
                        },
                        {
                            id: 19,
                            text: 'Quyền người dùng',
                            value: 'permissions'
                        },
                        {
                            id: 20,
                            text: 'Sản phẩm',
                            value: 'products'
                        },
                    ]
                },
            }
        },
        mounted() {
            this.setupData()

        },
        methods: {
            validateForm() {
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.search()
                    }
                })
            },
            async setupData() {
                let setup = () => {
                    this.form.timeFrom = moment().subtract(1, 'months').format("dd/MM/yyyy")
                    this.form.timeTo = moment().format("dd/MM/yyyy")
                    this.form.actionType = {
                        id: -1,
                        text: 'Tất cả',
                        value: 'All'
                    }
                    this.form.username = {
                        id: -1,
                        text: 'Tất cả',
                        name: 'Tất cả'
                    }
                    this.form.className = {
                        id: -1,
                        text: 'Tất cả',
                        value: 'All'
                    }
                }
                await setup()
                this.validateForm()
            },
            search() {
                let searchParams = this.filter()
                this.$emit('search', searchParams)
            },
            filter() {
                let searchParams = {}
                if (this.form.actionType) {
                    searchParams.action_name = this.form.actionType.value
                }
                if (this.form.timeFrom && this.form.timeFrom != "") {
                    searchParams.timeFrom = moment(this.form.timeFrom, "dd/MM/yyyy").format("YYYY-MM-DD 00:00:00")
                }
                if (this.form.timeTo && this.form.timeTo != "") {
                    searchParams.timeTo = moment(this.form.timeTo, "dd/MM/yyyy").format("YYYY-MM-DD 23:59:59")
                }
                if (this.form.username) {
                    searchParams.username = this.form.username.name
                }
                if (this.form.className) {
                    searchParams.class_name = this.form.className.value
                }
                return searchParams
            },
            reset() {
                this.form = new Form(defaultLogs)
                this.setupData()
            },
        }
    }
</script>




















