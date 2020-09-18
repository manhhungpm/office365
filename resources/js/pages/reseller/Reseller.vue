<template>
    <div>
        <the-portlet title="Danh sách Reseller">
            <data-table ref="table" :columns="columns" :url="listingUrl" :actions="actions" :fixed-columns-left="2"
                        :post-data="{role: 'Reseller'}"/>

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

        <reseller-modal ref="modal" :on-action-success="updateItemSuccess"/>
        <reseller-renew-password-modal ref="renewPasswordModal"
                                       :on-action-success="updateItemSuccess"/>
        <reseller-show-user-created-modal ref="showUserCreatedModal"/>
        <reseller-increase-max-user-modal ref="increaseMaxUserModal" :on-action-success="updateItemSuccess"/>
    </div>
</template>

<script>
    import bootbox from 'bootbox'
    import axios from 'axios'
    import {API_USER_LISTING, API_USER_DELETE, API_USER_INCREASE_MAX_USER} from '~/constants/url'
    import {generateTableAction, htmlEscapeEntities} from '~/helpers/tableHelper'
    import {notify, notifyTryAgain, notifyDeleteSuccess} from '~/helpers/bootstrap-notify'
    import {PASSWORD_REGEX, USER_NAME_REGEX} from '~/constants/regex'
    import {customRule} from '~/helpers/customRule'
    import ResellerModal from './partials/ResellerModal'
    import ResellerRenewPasswordModal from "./partials/ResellerRenewPasswordModal";
    import ResellerShowUserCreatedModal from "./partials/ResellerShowUserCreatedModal";
    import ResellerIncreaseMaxUserModal from "./partials/ResellerIncreaseMaxUserModal";

    const vm = {
        components: {
            ResellerIncreaseMaxUserModal,
            ResellerShowUserCreatedModal, ResellerRenewPasswordModal, ResellerModal
        },
        layout: 'default',
        middleware: 'auth',
        metaInfo() {
            return {title: this.$t('menu.admin.user')}
        },
        data: () => ({
            listingUrl: API_USER_LISTING,
            columns: [
                {
                    data: 'name',
                    title: 'Tên người dùng'
                },
                {
                    data: 'code',
                    title: 'Mã Reseller'
                },
                {
                    data: 'display_name',
                    title: 'Tên hiển thị'
                },
                {
                    data: 'email',
                    title: 'Email'
                },
                {
                    data: 'phone',
                    title: 'Số điện thoại'
                },
                {
                    data: 'domains',
                    title: 'Số domain',
                    render(data) {
                        return data.length
                    }
                },
                {
                    data: 'num_user_max',
                    title: 'Max user'
                },
                {
                    data: 'num_user_created',
                    title: 'User đã tạo'
                },
                {
                    data: 'status',
                    title: 'Trạng thái',
                    render(data) {
                        if (parseInt(data) == 1) {
                            return '<span class="text-success">Active</span>'
                        } else {
                            return '<span class="text-danger">Khóa</span>'
                        }
                    }
                },
                {
                    data: null,
                    title: 'Hành động',
                    responsivePriority: 1,
                    orderable: false,
                    className: 'text-center',
                    render() {
                        return generateTableAction('edit', 'showDetail') +
                            generateTableAction('delete', 'deleteItem') +
                            generateTableAction('renewpassword', 'renewPassword', 'warning', 'la-lock', 'Đổi mật khẩu') +
                            generateTableAction('showUserCreated', 'showUserCreated', 'info', 'la-list', 'Xem User đã tạo') +
                            generateTableAction('increaseMaxUser', 'increaseMaxUser', 'accent', 'la-plus', 'Tăng thêm số User')

                    }
                }
            ]
        }),
        mounted() {

        },
        methods: {
            showDetail(table, rowData) {
                this.$refs.modal.show(rowData)
            },
            showDetailModal(table, rowData) {
                this.$refs.renewPasswordModal.show(rowData)
            },
            deleteItem(table, rowData) {
                let $this = this

                bootbox.confirm({
                    title: this.$t('alert.notice'),
                    message: this.$t('form.user.delete_confirm', [htmlEscapeEntities(rowData.name)]),
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
                            let res = await axios.post(API_USER_DELETE, {id: rowData.id})
                            const {data} = res

                            if (data.code == 0) {
                                notifyDeleteSuccess($this.$t('form.user.user'))

                                $this.$refs.table.reload()
                            } else if (data.code == 3) {
                                notify('Thông báo', 'Không thể xóa vì Reseller đang có User được tạo hoặc mã bảo mật')
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
            renewPassword(table, rowData) {
                this.$refs.renewPasswordModal.show(rowData);
            },
            showUserCreated(table, rowData) {
                this.$refs.showUserCreatedModal.show(rowData);
            },
            async increaseMaxUser(table, rowData) {
                // let $this = this
                //
                // let res = await axios.post(API_USER_INCREASE_MAX_USER, {id: rowData.id})
                // const {data} = res
                //
                // if (data.code == 0) {
                //     notify("Thông báo","Tăng số lượng User thành công","success")
                //
                //     $this.$refs.table.reload()
                // }  else {
                //     notifyTryAgain()
                // }
                this.$refs.increaseMaxUserModal.show(rowData);
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
                        name: 'renewPassword',
                        action: this.renewPassword
                    },
                    {
                        type: 'click',
                        name: 'showUserCreated',
                        action: this.showUserCreated
                    },
                    {
                        type: 'click',
                        name: 'increaseMaxUser',
                        action: this.increaseMaxUser
                    }
                ]
            }
        }
    }

    export default vm
</script>
