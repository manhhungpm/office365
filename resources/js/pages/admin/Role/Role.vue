<template>
    <div>
        <the-portlet :title="$t('form.role.list')">
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

        <role-modal ref="modal" :on-action-success="updateItemSuccess"/>
    </div>
</template>

<script>
    import Vue from 'vue'
    import bootbox from 'bootbox'
    import axios from 'axios'

    import {API_ROLE_LISTING, API_ROLE_DELETE, API_ROLE_GET_PERMISSION_LIST} from '~/constants/url'
    import {generateTableAction, htmlEscapeEntities} from '~/helpers/tableHelper'
    import {notify, notifyTryAgain, notifyDeleteSuccess} from '~/helpers/bootstrap-notify'

    import RoleModal from './RoleModal';

    Vue.component('role-modal', RoleModal);

    const vm = {
        layout: 'default',
        middleware: 'auth',
        metaInfo() {
            return {title: this.$t('menu.admin.role')}
        },
        data: () => ({
            listingUrl: API_ROLE_LISTING,
            columns: [
                {
                    data: 'name',
                    title: 'Mã nhóm người dùng'
                },
                {
                    data: 'display_name',
                    title: 'Tên hiển thị'
                },
                {
                    data: 'description',
                    title: 'Mô tả'
                },
                {
                    data: null,
                    title: 'Hành động',
                    responsivePriority: 1,
                    orderable: false,
                    className: 'text-center',
                    render() {
                        return generateTableAction('edit', 'showDetail') +
                            generateTableAction('delete', 'deleteItem')
                    }
                }
            ],
        }),
        mounted() {
        },
        methods: {
            showDetail(table, rowData) {
                this.$refs.modal.show(rowData)
            },
            deleteItem(table, rowData) {
                let $this = this

                bootbox.confirm({
                    title: this.$t('alert.notice'),
                    message: this.$t('form.role.delete_confirm', [htmlEscapeEntities(rowData.name)]),
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

                            let res = await axios.post(API_ROLE_DELETE, {id: rowData.id})
                            const {data} = res

                            if (data.code == 0) {
                                notifyDeleteSuccess($this.$t('form.role.role'))

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
                    }
                ]
            }
        }
    }

    export default vm
</script>
