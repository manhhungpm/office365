<template>
    <div>
        <the-portlet title="Danh sách mã bảo mật">
            <data-table ref="table" :columns="columns" url="/api/student-code/listing" :actions="actions"
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

        <student-code-modal ref="modal" :on-action-success="updateItemSuccess"/>
        <student-code-show-user-created-modal ref="showUserCreatedModal"></student-code-show-user-created-modal>
    </div>
</template>

<script>
    import bootbox from 'bootbox'
    import axios from 'axios'
    import {mapGetters} from 'vuex'
    import {generateTableAction, htmlEscapeEntities, reloadIntelligently} from '~/helpers/tableHelper'
    import {notify, notifyTryAgain, notifyDeleteSuccess, notifyUpdateSuccess} from '~/helpers/bootstrap-notify'
    import {toNormalDate} from '~/helpers/datetimeFormat'
    import StudentCodeModal from './partials/StudentCodeModal'
    import {ROLE_ADMIN} from '~/constants/roles'
    import * as studentCodeStatus from '~/constants/studentCodeStatus'
    import StudentCodeShowUserCreatedModal from "./partials/StudentCodeShowUserCreatedModal";

    const vm = {
        components: {StudentCodeShowUserCreatedModal, StudentCodeModal},
        layout: 'default',
        middleware: 'auth',
        metaInfo() {
            return {title: 'Quản lý mã bảo mật'}
        },
        data: () => ({

            table: null
        }),
        mounted() {
            this.handleEvents()
        },
        methods: {
            setTable(table) {
                this.table = table
            },
            deleteItem(table, rowData) {
                let $this = this

                if (parseInt(rowData.status) == studentCodeStatus.UNUSED) {
                    bootbox.confirm({
                        title: this.$t('alert.notice'),
                        message: `Bạn chắc chắn muốn xóa MSSV <span class="text-danger">"${htmlEscapeEntities(rowData.code)}"</span>?`,
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

                                let res = await axios.post('/api/student-code/delete', {id: rowData.id})
                                const {data} = res

                                if (data.code == 0) {
                                    notifyDeleteSuccess('MSSV')
                                    reloadIntelligently($this.$refs.table)
                                } else if (data.code == 4) {
                                    notify("Thông báo", "Không thể xóa mã vì có tồn tại người dùng sử dụng mã này")
                                } else {
                                    notifyTryAgain()
                                }

                            }
                        }
                    })
                } else {
                    bootbox.alert({
                        title: 'Thông báo',
                        message: 'Mã bảo mật đang được sử dụng, không thể thực hiện thao tác này!'
                    })
                }
            },
            updateItemSuccess() {
                this.$refs.table.reload()
            },
            showModal() {
                this.$refs.modal.show()
            },
            editItem(table, rowData) {
                this.$refs.modal.show(rowData)
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

                    let res = await axios.post('/api/domain/change-status', {id: rowData.id, status: status})
                    const {data} = res

                    if (parseInt(data.code) === 0) {
                        notifyUpdateSuccess('domain')
                        reloadIntelligently($this.$refs.table)
                    } else {
                        notifyTryAgain()
                    }
                })
            },
            showUserCreated(table, rowData){
                this.$refs.showUserCreatedModal.show(rowData);
            }
        },
        computed: {
            ...mapGetters({
                role: 'auth/role'
            }),
            columns() {
                return [
                    {
                        data: 'code',
                        title: 'Mã bảo mật'
                    },
                    {
                        data: 'domain',
                        title: 'Domain',
                        render(data) {
                            if (data != null) {
                                return htmlEscapeEntities(data.id)
                            }
                            return ''
                        }
                    },
                    {
                        data: 'reseller',
                        title: 'Reseller',
                        visible: this.role == ROLE_ADMIN,
                        render(data) {
                            if (data != null) {
                                return htmlEscapeEntities(data.display_name)
                            }
                            return ''
                        }
                    },
                    {
                        data: 'status',
                        title: 'Trạng thái',
                        render(data, type, row) {
                            switch (parseInt(data)) {
                                case studentCodeStatus.UNUSED:
                                    return `<span class="text-warning">Chưa sử dụng ${row.used_number}/${row.max_user}</span>`
                                    break
                                case studentCodeStatus.ACTIVE:
                                    if (row.used_number < row.max_user) {
                                        return `<span class="text-success">Đang sử dụng ${row.used_number}/${row.max_user}</span>`
                                    } else {
                                        return `<span class="text-danger">Đang sử dụng ${row.used_number}/${row.max_user}</span>`
                                    }
                                    break
                                case studentCodeStatus.BLOCK:
                                    return '<span class="text-danger">Đã khóa</span>'
                                    break
                            }

                            return ''
                        }
                    },
                    {
                        data: 'expired_date',
                        title: 'Ngày hết hạn',
                        render(data) {
                            return toNormalDate(data)
                        }
                    },
                    {
                        data: 'created_at',
                        title: 'Ngày tạo',
                        render(data) {
                            return toNormalDate(data)
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
                            return generateTableAction('edit', 'editItem') + generateTableAction('delete', 'deleteItem') +
                                generateTableAction('showUserCreated', 'showUserCreated', 'info', 'la-list', 'Xem User đã tạo')
                        }
                    }
                ]
            },
            actions() {
                return [
                    {
                        type: 'click',
                        name: 'editItem',
                        action: this.editItem
                    },
                    {
                        type: 'click',
                        name: 'deleteItem',
                        action: this.deleteItem
                    },
                    {
                        type: 'click',
                        name: 'showUserCreated',
                        action: this.showUserCreated
                    }
                ]
            }
        }
    }

    export default vm
</script>
