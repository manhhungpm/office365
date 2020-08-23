<template>
    <div>
        <the-portlet :title="$t('form.permission.list')">
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

        <permission-modal ref="modal" :on-action-success="updateItemSuccess"/>
    </div>
</template>

<script>
    import Vue from 'vue'
    import bootbox from 'bootbox'
    import axios from 'axios'

    import {API_PERMISSION_LISTING, API_PERMISSION_DELETE} from '~/constants/url'
    import {generateTableAction, htmlEscapeEntities} from '~/helpers/tableHelper'
    import {notify, notifyTryAgain, notifyDeleteSuccess} from '~/helpers/bootstrap-notify'

    import PermissionModal from './PermissionModal'

    Vue.component('permission-modal', PermissionModal)

    const vm = {
        layout: 'default',
        middleware: 'auth',
        metaInfo() {
            return {title: this.$t('menu.admin.permission')}
        },
        data: () => ({
            listingUrl: API_PERMISSION_LISTING,
            columns: [
                {
                    data: 'name',
                    title: 'Mã quyền'
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
            ]
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
                    message: this.$t('form.permission.delete_confirm', [htmlEscapeEntities(rowData.name)]),
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

                            let res = await axios.post(API_PERMISSION_DELETE, {id: rowData.id})
                            const {data} = res

                            if (data.code == 0) {
                                notifyDeleteSuccess($this.$t('form.permission.permission'))

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
