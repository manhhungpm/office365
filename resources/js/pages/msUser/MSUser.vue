<template>
    <div>
        <the-portlet title="Danh sách người dùng">
            <data-table ref="table" :columns="columns" url="/api/ms-user/listing" :actions="actions"
                        :fixed-columns-left="2"
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
    </div>
</template>

<script>
    import Vue from 'vue'
    import bootbox from 'bootbox'
    import axios from 'axios'

    import {generateTableAction, htmlEscapeEntities, reloadIntelligently} from '~/helpers/tableHelper'
    import {notify, notifyTryAgain, notifyDeleteSuccess, notifyUpdateSuccess} from '~/helpers/bootstrap-notify'

    import MSUserModal from './partials/MSUserModal'

    Vue.component('account-modal', MSUserModal)

    const vm = {
        layout: 'default',
        middleware: 'auth',
        metaInfo() {
            return {title: 'Quản lý người dùng'}
        },
        data: () => ({
            columns: [
                {
                    data: 'userPrincipalName',
                    title: 'Principal Name'
                },
                {
                    data: 'displayName',
                    title: 'Tên hiển thị'
                },
                {
                    data: 'id',
                    title: 'License'
                },
                {
                    data: 'account',
                    title: 'Tenant Name',
                    render(data) {
                        return htmlEscapeEntities(data.app_name)
                    }
                },
                {
                    data: 'reseller',
                    title: 'Người tạo',
                    render(data, type, row) {
                        if (data != null) {
                            return htmlEscapeEntities(data.name)
                        }

                        return 'Code' + (row.code ? `: ${row.code}` : '')
                    }
                },
                {
                    data: 'accountEnabled',
                    title: 'Trạng thái',
                    render(data) {
                        if (data) {
                            return '<span class="text-success">Đang hoạt động</span>'
                        }
                        return '<span class="text-danger">Khóa</span>'
                    }
                },
                {
                    data: 'createdDateTime',
                    title: 'Thời gian tạo'
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
                            generateTableAction('delete', 'deleteItem')
                    }
                }
            ],
            table: null
        }),
        mounted() {
            this.handleEvents()
        },
        methods: {
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
                    message: `Bạn chắc chắn muốn xóa người dùng <span class="text-danger">"${htmlEscapeEntities(rowData.displayName)}"</span>?`,
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

                            let res = await axios.post('/api/ms-user/delete', {ms_user_id: rowData.ms_user_id})
                            const {data} = res

                            if (data.code == 0) {
                                notifyDeleteSuccess('người dùng')
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

                    let res = await axios.post('/api/account/change-status', {id: rowData.ms_user_id, status: status})
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
                    }
                ]
            }
        }
    }

    export default vm
</script>
