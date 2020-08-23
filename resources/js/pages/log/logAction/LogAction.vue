<template>
    <div>
        <div class="m-portlet__body">
            <div class="m-accordion m-accordion--default m-accordion--toggle-arrow" id="m_accordion_5" role="tablist">
                <div class="m-accordion__item m-accordion__item--brand">
                    <div class="m-accordion__item-head collapsed" role="tab" id="m_accordion_5_item_3_head"
                         data-toggle="collapse" href="#m_accordion_5_item_3_body" aria-expanded="true">
                        <span class="m-accordion__item-title"> Tìm kiếm nâng cao</span>
                        <span class="m-accordion__item-mode"></span>
                    </div>
                    <div class="m-accordion__item-body collapse" id="m_accordion_5_item_3_body" role="tabpanel"
                         aria-labelledby="m_accordion_5_item_3_head" data-parent="#m_accordion_5">
                        <div class="m-accordion__item-content">
                            <logs-filter @search="search"></logs-filter>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <the-portlet :title="$t('form.logs.list')">
                <data-table ref="table" :columns="columns" url="/api/logs/listing" :order-type="orderType"
                            :post-data="tableFilter"
                            :order-column-index="order"
                            :searching="false"
                            :actions="actions"
                            :searchPlaceholder="'Tìm từ khóa'"/>
            </the-portlet>
        </div>

        <logs-detail-change-modal ref="modal"></logs-detail-change-modal>
    </div>
</template>

<script>
    import moment from 'moment'
    import LogsFilter from './partials/LogsFilter'
    import {htmlEscapeEntities} from '~/helpers/tableHelper'
    import LogsDetailChangeModal from './partials/LogsDetailChangeModal'

    function parserObjectName(class_name) {
        if (class_name != null && class_name != undefined) {
            switch (class_name) {
                case 'users':
                    return htmlEscapeEntities('Người dùng')
                    break
                case 'services':
                    return htmlEscapeEntities('Dịch vụ')
                    break
                case 'discounts':
                    return htmlEscapeEntities('Phụ lục chiết khấu')
                    break
                case 'service_deploys':
                    return htmlEscapeEntities('Luồng triển khai dịch vụ')
                    break
                case 'sender_ids':
                    return htmlEscapeEntities('Kho số và Alias')
                    break
                case 'roles':
                    return htmlEscapeEntities('Nhóm người dùng')
                    break
                case 'providers':
                    return htmlEscapeEntities('Nhà mạng')
                    break
                case 'products':
                    return htmlEscapeEntities('Sản phẩm')
                    break
                case 'product_groups':
                    return htmlEscapeEntities('Nhóm sản phẩm')
                    break
                case 'permissions':
                    return htmlEscapeEntities('Quyền người dùng')
                    break
                case 'partners':
                    return htmlEscapeEntities('Đối tác')
                    break
                case 'orders':
                    return htmlEscapeEntities('Đơn hàng')
                    break
                case 'order_details':
                    return htmlEscapeEntities('Chi tiết đơn hàng')
                    break
                case 'msisdns':
                    return htmlEscapeEntities('Dải MSISDN')
                    break
                case 'gt_nodes':
                    return htmlEscapeEntities('Dải GT NODE')
                    break
                case 'service_prices':
                    return htmlEscapeEntities('Phụ lục giá bán')
                    break
                case 'departments':
                    return htmlEscapeEntities('Bộ phận')
                    break
                case 'contracts':
                    return htmlEscapeEntities('Hợp đồng')
                    break
                case 'contract_types':
                    return htmlEscapeEntities('Loại hợp đồng')
                    break
                case 'contract_templates':
                    return htmlEscapeEntities('Mẫu hợp đồng')
                    break
                case 'currencies':
                    return htmlEscapeEntities('Tiền tệ')
                    break
            }
        } else {
            return ''
        }
    }

    const vm = {
        components: {LogsFilter, LogsDetailChangeModal},
        layout: 'default',
        middleware: 'auth',
        metaInfo() {
            return {title: 'Log Action'}
        },
        data: () => ({
            order: 5,
            orderType: "desc",
            tableFilter: null,
            columns: [
                {
                    data: 'username',
                    title: 'Tài khoản',
                },
                {
                    data: 'action_name',
                    title: 'Hành động',
                    render(data) {
                        switch (data) {
                            case 'Add':
                                return htmlEscapeEntities('Thêm')
                                break
                            case 'Update':
                                return htmlEscapeEntities('Cập nhật')
                                break
                            case 'Delete':
                                return htmlEscapeEntities('Xóa')
                                break
                            case 'Login':
                                return htmlEscapeEntities('Đăng nhập')
                                break
                            case 'Logout':
                                return htmlEscapeEntities('Đăng xuất')
                                break
                        }
                    }
                },
                {
                    data: 'class_name',
                    title: 'Loại đối tượng',
                    render(data) {
                        return parserObjectName(data)
                    }
                },
                {
                    data: 'object_name',
                    title: 'Đối tượng',
                    render(data) {
                        return `<div href='javascript:;' data-action='showDetailChange' title="Xem sự thay đổi"><a class="a-link">${htmlEscapeEntities(data)}</a></div>`
                    }
                },
                {
                    data: 'created_at',
                    title: 'Thời gian',
                    render(data) {
                        return moment(data).format('DD/MM/YYYY HH:MM:ss')
                    }
                },
            ]
        }),
        methods: {
            updateItemSuccess() {
                this.$refs.table.reload()
            },
            async search(value) {
                this.tableFilter = value
                await this.$nextTick()
                this.updateItemSuccess()
            },
            showDetailChange(table, rowData) {
                this.$refs.modal.show(rowData)
            },
        },
        computed: {
            actions() {
                return [
                    {
                        type: 'click',
                        name: 'showDetailChange',
                        action: this.showDetailChange
                    },
                ]
            }
        }
    }

    export default vm
</script>
