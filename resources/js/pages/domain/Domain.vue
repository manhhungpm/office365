<template>
    <div>
        <the-portlet title="Danh sách Domain">
            <data-table ref="table" :columns="columns" url="/api/domain/listing" :actions="actions"
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

        <domain-modal ref="modal" :on-action-success="updateItemSuccess"/>
    </div>
</template>

<script>
  import bootbox from 'bootbox'
  import axios from 'axios'

  import { generateTableAction, htmlEscapeEntities, reloadIntelligently } from '~/helpers/tableHelper'
  import { notify, notifyTryAgain, notifyDeleteSuccess, notifyUpdateSuccess } from '~/helpers/bootstrap-notify'
  import DomainModal from './partials/DomainModal'

  const vm = {
    components: { DomainModal },
    layout: 'default',
    middleware: 'auth',
    metaInfo() {
      return { title: 'Quản lý Domain' }
    },
    data: () => ({
      columns: [
        {
          data: 'id',
          title: 'Domain'
        },
        {
          data: 'account',
          title: 'Account',
          render(data) {
            if (data != null) {
              return htmlEscapeEntities(data.app_name)
            }
            return ''
          }
        },
        {
          data: 'resellers_count',
          title: 'Số Reseller'
        },
        {
          data: 'isVerified',
          title: 'Trạng thái',
          render(data) {
            if (parseInt(data) == 1) {
              return '<span class="text-success">Đã xác nhận</span>'
            }
            return '<span class="text-danger">Chưa xác nhận</span>'
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
          message: `Bạn chắc chắn muốn xóa domain <span class="text-danger">"${htmlEscapeEntities(rowData.id)}"</span>?`,
          buttons: {
            cancel: {
              label: this.$t('button.cancel')
            },
            confirm: {
              label: this.$t('button.ok')
            }
          },
          callback: async function(result) {
            if (result) {

              let res = await axios.post('/api/domain/delete', { domain_id: rowData.domain_id })
              const { data } = res

              if (data.code == 0) {
                notifyDeleteSuccess('domain')
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
        $(this.$el).on('change', '.cb-status', async function() {
          let rowData = table.row($(this).parents('tr')).data()
          let status = rowData.status
          if (parseInt(status) === 0) {
            status = 1
          } else {
            status = 0
          }

          let res = await axios.post('/api/domain/change-status', { id: rowData.id, status: status })
          const { data } = res

          if (parseInt(data.code) === 0) {
            notifyUpdateSuccess('domain')
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