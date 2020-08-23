<template>
    <div>
        <the-portlet :title="$t('form.user.list')">
            <data-table ref="table" :columns="columns" :url="listingUrl" :actions="actions"/>

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

        <user-modal ref="modal" :on-action-success="updateItemSuccess"/>
        <renew-password :on-action-success="updateItemSuccess" ref="renewPasswordModal"></renew-password>
    </div>
</template>

<script>
    import Vue from 'vue'
    import bootbox from 'bootbox'
    import axios from 'axios'
    import {API_USER_LISTING, API_USER_DELETE} from '~/constants/url'
    import {generateTableAction, htmlEscapeEntities} from '~/helpers/tableHelper'
    import {notify, notifyTryAgain, notifyDeleteSuccess} from '~/helpers/bootstrap-notify'
    import {PASSWORD_REGEX, USER_NAME_REGEX} from '~/constants/regex'
    import {customRule} from '~/helpers/customRule';

    import UserModal from './UserModal';
    import RenewPassword from './RenewPassword';

    Vue.component('user-modal', UserModal);
    Vue.component('renew-password', RenewPassword);


    const vm = {
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
                    data: 'full_name',
                    title: 'Tên hiển thị'
                },
                {
                    data: 'email',
                    title: 'Email'
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
                            generateTableAction('renewpassword', 'showDetailModal', 'primary', 'la-lock', 'Đổi mật khẩu')
                    }
                }
            ],
            roleList: [],
            loading: true,
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
                        name: 'showDetailModal',
                        action: this.showDetailModal
                    },
                ]
            }
        }
    }

    export default vm
</script>
