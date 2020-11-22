<template>
    <div>
        <the-portlet title="Danh sách tài khoản Office 365">
            <data-table ref="table" :columns="columns" url="/api/account/listing" :actions="actions"
                        v-on:initial="setTable"/>

            <v-button color="primary" style-type="air"
                      class="m-btn--custom m-btn--icon"
                      slot="tool"
                      @click.native="showModal">
                    <span>
                        <i class="la la-plus"></i>
                        <span>{{ $t('button.add')}}</span>
                    </span>
            </v-button>
        </the-portlet>

        <account-modal ref="modal" :on-action-success="updateItemSuccess"/>
        <account-edit-license-modal ref="licenseModal" :on-action-success="updateItemSuccess"></account-edit-license-modal>
    </div>
</template>

<script>
    import Vue from 'vue'
    import bootbox from 'bootbox'
    import axios from 'axios'

    import {generateTableAction, htmlEscapeEntities, reloadIntelligently} from '~/helpers/tableHelper'
    import {notify, notifyTryAgain, notifyDeleteSuccess, notifyUpdateSuccess} from '~/helpers/bootstrap-notify'

    import AccountModal from './partials/AccountModal'
    import AccountEditLicenseModal from "./partials/AccountEditLicenseModal";

    Vue.component('account-modal', AccountModal)

    const vm = {
        components: {AccountEditLicenseModal},
        layout: 'default',
        middleware: 'auth',
        metaInfo() {
            return {title: 'Tài khoản Office 365'}
        },
        data: () => ({
            columns: [
                {
                    data: 'app_name',
                    title: 'Tên ứng dụng'
                },
                // {
                //   data: 'description',
                //   title: 'Mô tả'
                // },
                {
                    data: 'client_id',
                    title: 'Client ID'
                },
                {
                    data: 'client_secret',
                    title: 'Client Secret'
                },
                {
                    data: 'tenant_id',
                    title: 'Tenant ID'
                },
                {
                    data: 'status',
                    title: 'Trạng thái',
                    render(data) {
                        if (parseInt(data) === 1) {
                            return '<label class="m-checkbox m-checkbox--air m-checkbox--state-success">' +
                                '<input type="checkbox" class="cb-status" checked> Active' +
                                '<span></span>' +
                                '</label>'
                        } else {
                            return '<label class="m-checkbox m-checkbox--air m-checkbox--state-success">' +
                                '<input type="checkbox" class="cb-status"> Active' +
                                '<span></span>' +
                                '</label>'
                        }
                    }
                },
                {
                    data: null,
                    title: 'Hành động',
                    responsivePriority: 1,
                    orderable: false,
                    className: 'text-center',
                    width: '15%',
                    render() {
                        return generateTableAction('edit', 'showDetail') +
                            generateTableAction('delete', 'deleteItem') +
                            generateTableAction('editLicense', 'editLicense', 'warning', 'la-check', 'Cấu hình license')
                    }
                }
            ],
            table: null
        }),
        mounted() {
            this.handleEvents();
        },
        methods: {
            editLicense(table, rowData){
                this.$refs.licenseModal.show(rowData)
            },
            setTable(table) {
                this.table = table
            },
            showDetail(table, rowData) {
                this.$refs.modal.show(rowData)
            },
            deleteItem(table, rowData) {
                let $this = this

                bootbox.confirm({
                    title: this.$t('alert.notice'),
                    message: `Bạn chắc chắn muốn xóa tài khoản <span class="text-danger">"${htmlEscapeEntities(rowData.name)}"</span>?`,
                    buttons: {
                        cancel: {
                            label: this.$t('button.cancel')
                        },
                        confirm: {
                            label: this.$t('button.ok')
                        }
                    },
                    callback: async function (result) {
                        if (result) {

                            let res = await axios.post('/api/account/delete', {id: rowData.id})
                            const {data} = res

                            if (data.code == 0) {
                                notifyDeleteSuccess('tài khoản')
                                reloadIntelligently($this.$refs.table)
                            } else {
                                notifyTryAgain()
                            }

                        }
                    }
                })
            },
            updateItemSuccess() {
                this.$refs.table.reload()
            },
            showModal() {
                this.$refs.modal.show()
            },
            handleEvents() {
                let table = this.table
                let $this = this
                $(this.$el).on('change', '.cb-status', async function () {
                    let rowData = table.row($(this).parents('tr')).data()
                    let status = rowData.status
                    if (parseInt(status) === 0) {
                        status = 1
                    } else {
                        status = 0
                    }

                    let res = await axios.post('/api/account/change-status', {id: rowData.id, status: status})
                    const {data} = res

                    if (parseInt(data.code) === 0) {
                        notifyUpdateSuccess('tài khoản')
                        reloadIntelligently($this.$refs.table)
                    } else {
                        notifyTryAgain()
                    }
                })
            }
        },
        computed: {
            actions() {
                return [
                    {
                        type: 'click',
                        name: 'showDetail',
                        action: this.showDetail
                    },
                    {
                        type: 'click',
                        name: 'deleteItem',
                        action: this.deleteItem
                    },
                    {
                        type: 'click',
                        name: 'editLicense',
                        action: this.editLicense
                    }
                ]
            }
        }
    }

    export default vm
</script>
