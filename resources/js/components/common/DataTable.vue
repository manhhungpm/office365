<template>
    <table class="table table-bordered table-hover table-striped display responsive nowrap"></table>
</template>

<style>
    @import "~datatables.net-bs4/css/dataTables.bootstrap4.min.css";
    @import "~datatables.net-fixedcolumns-bs4/css/fixedColumns.bootstrap4.min.css";


    .DTFC_ScrollWrapper {
        height: auto !important;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        width: 300px;
    }

    .dataTables_scrollHead {

    }

    .DTFC_LeftBodyLiner {
        top: -14px !important;
        width: unset !important;
        overflow-y: unset !important;
    }

    .DTFC_LeftWrapper {
        top: -14px !important;
    }

    .DTFC_LeftBodyWrapper {
        top: -14px !important;
        border-right: 1px solid #e7ecf1;
    }

    .DTFC_RightHeadWrapper {
        top: -14px !important;
    }

    .DTFC_RightBodyWrapper {
        top: -28px !important;
        border-left: 1px solid #e7ecf1;
    }

    .DTFC_RightBodyLiner {
        top: -14px !important;
        width: unset !important;
        overflow-y: unset !important;
    }

    div.dataTables_wrapper div.dataTables_processing {
        z-index: 1000;
    }


</style>

<script>
  import 'datatables.net-bs4'
  import 'datatables.net-fixedcolumns-bs4'

  export default {
    name: 'DataTable',
    props: {
      columns: {
        type: Array,
        default: () => []
      },
      url: String,
      actions: {
        type: Array,
        default: () => []
      },
      serverSide: {
        type: Boolean,
        default: true
      },
      processing: {
        type: Boolean,
        default: true
      },
      searching: {
        type: Boolean,
        default: true
      },
      lengthChange: {
        type: Boolean,
        default: true
      },
      ordering: {
        type: Boolean,
        default: true
      },
      orderColumnIndex: {
        type: Number,
        default: 1
      },
      orderType: {
        type: String,
        default: 'asc'
      },
      refresh: {
        type: Boolean,
        default: false
      },
      postData: {
        type: Object,
        default: null
      },
      dom: {
        type: String,
        default: '<\'row\'<\'col-md-6 col-sm-12\'l><\'col-md-6 col-sm-12\'f>r><\'table-scrollable\'t><\'row\'<\'col-md-5 col-sm-12\'i><\'col-md-7 col-sm-12\'p>>'
      },
      searchPlaceholder: {
        type: String,
        default: 'Nhập từ khóa tìm kiếm...'
      },
      fixedColumnsLeft: {
        type: Number,
        default: 1
      },
      fixedColumnsRight: {
        type: Number,
        default: 1
      },
      responsive: {
        type: Boolean,
        default: true,
      },
      pageLength: {
        type: Number,
        default: 10
      }
    },
    data() {
      return {
        table: null,
        firstRow: null,
      }
    },
    mounted() {
      this.initTable()
      this.table.columns.adjust().fixedColumns().relayout()
      let $this = this
    },
    methods: {
      initTable() {
        this.firstRow = null
        $.fn.dataTable.ext.errMode = 'none'
        $.extend($.fn.dataTableExt.oStdClasses, {
          'sWrapper': 'dataTables_wrapper',
          'sFilterInput': 'form-control',
          'sLengthSelect': 'custom-select form-control'
        })

        let defaultConfigs = {
          dom: this.dom,
          processing: this.processing,
          ordering: this.ordering,
          searching: this.searching,

          lengthChange: this.lengthChange,
          serverSide: this.serverSide,
          responsive: this.responsive,
          scrollX: true,
          scrollCollapse: true,
          // fixedHeader: {
          //     header: this.responsive,
          //     headerOffset: 70
          // },
          pageLength: this.pageLength,
          lengthMenu: [5, 10, 25, 50, 75, 100],
          fixedColumns: {
            leftColumns: this.fixedColumnsLeft,
            rightColumns: this.fixedColumnsRight,
          },

          language: {
            aria: {
              sortAscending: 'Sắp xếp tăng dần',
              sortDescending: 'Sắp xếp giảm dần'
            },
            processing: '<div class="m-loader m-loader--light m-loader--left"></div><span>&nbsp;&nbsp;&nbsp; ' + 'Đang tải' + '...</span>',
            emptyTable: 'Không có bản ghi',
            info: '_START_ - _END_ của _TOTAL_ bản ghi',
            infoEmpty: '',
            infoFiltered: '',
            lengthMenu: '_MENU_ bản ghi',
            search: '',
            zeroRecords: 'Không có bản ghi',
            paginate: {
              previous: '<i class="la la-angle-left" aria-hidden="true"></i>',
              next: '<i class="la la-angle-right" aria-hidden="true"></i>',
              last: 'Cuối',
              first: 'Đầu'
            },
            searchPlaceholder: this.searchPlaceholder
          },
          columns: this.tableColumns,
          order: [this.orderColumnIndex, this.orderType],
          drawCallback: function (settings) {
            var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate')
            pagination.toggle(this.api().page.info().pages > 0)
          }
        }


        if (this.serverSide) {
          $.extend(defaultConfigs, {
            ajax: {
              url: this.url,
              type: 'POST',
              dataType: 'json',
              data: function (d) {
                return $.extend({}, d, this.postData)
              }.bind(this)
            }
          })
        }

        this.table = $(this.$el).DataTable(defaultConfigs)

        this.$emit('initial', this.table)

        this.initIndexColumn()
        this.registerActions()

        this.table.on('draw.dt', () => {
          this.table.columns.adjust().fixedColumns().relayout()

          setTimeout(() => {
            this.table.columns.adjust().fixedColumns().relayout()
          }, 200)
        })
      },
      initIndexColumn() {
        if (this.serverSide) {
          this.table.on('order.dt search.dt draw.dt', function () {
            var info = this.table.page.info()
            var start = info.start
            // $('.table-action').tooltip();

            setTimeout(() => {
              $('.DTFC_Cloned .table-action').tooltip();
            }, 100)

            this.table.column(0, {
              search: 'applied',
              order: 'applied'
            }).nodes().each(function (cell, i) {
              cell.innerHTML = i + 1 + start
            })

          }.bind(this))

        } else {
          this.table.on('order.dt search.dt draw.dt', function () {
            this.table.column(0, {
              search: 'applied',
              order: 'applied'
            }).nodes().each(function (cell, i) {
              cell.innerHTML = i + 1
            })
          }.bind(this))

          this.table.draw()
        }
      },
      registerActions() {
        let vm = this

        if (this.actions.length > 0) {
          this.actions.forEach(action => {
            $(this.$el).on(action.type, '[data-action="' + action.name + '"]', function () {

              $('.table-action').tooltip('hide')
              var td = $(this).closest('td')
              var tr = $(this).closest('tr')
              var row = $(vm.$el).DataTable().row(tr)

              var data = $(vm.$el).DataTable().row(tr).data()
              var cell = $(vm.$el).DataTable().cell(td)

              action.action(vm.table, data, row, td, cell)
            })
          })
        }
      },


      async reinit() {
        $(this.$el).DataTable().destroy()

        $(this.$el).empty()
        await this.$nextTick()
        this.initTable()
      },
      reload(keepCurrentPage = true) {
        if (this.table != null) {
          this.table.ajax.reload(null, !keepCurrentPage)
        }
      },
      async nextRow(rowIndex) {
        if (this.table != null) {
          if (this.table.row(rowIndex + 1).data()) {

            return this.table.row(rowIndex + 1)
          } else {
            let info = this.table.page.info()
            let currentPage = info.page
            if (currentPage === info.pages - 1) {
              return this.table.row(rowIndex)
            }
            this.table.page('next').draw('page')

            let interval = setInterval(() => {
              let page = this.table.page.info().page
              if (page != currentPage) {
                clearInterval(interval)
                this.$emit('firstRow', this.table.row(0))
              }
            }, 1000)
          }
        }
      },
      async prevRow(rowIndex) {
        if (this.table != null) {
          if (this.table.row(rowIndex - 1).data()) {

            return this.table.row(rowIndex - 1)
          } else {

            let info = this.table.page.info()
            let end = info.length - 1
            let currentPage = info.page
            if (currentPage == 0) {
              return this.table.row(0)
            }
            this.table.page('previous').draw('page')

            let interval = setInterval(() => {
              let page = this.table.page.info().page

              if (page != currentPage) {
                clearInterval(interval)
                this.$emit('lastRow', this.table.row(end))
              }

            }, 1000)


          }
        }
      },
      getAllData() {
        return this.table.rows().data()
      },
      nextPrePage(){
        this.table.page( 'previous' ).draw( 'page' )
      }


    },
    computed: {
      tableColumns: function () {
        var columns = this.columns
        columns.unshift({
          data: null,
          title: 'STT',
          orderable: false,
          responsivePriority: 1,
          className: 'tb-number'
        })

        columns.forEach(column => {
          if (column.render === undefined) {
            column.render = $.fn.dataTable.render.text()
          }
        })

        return columns
      }
    },
    watch: {},
    destroyed() {
      $(this.$el).DataTable().destroy()
      $(this.$el).empty()
    }
  }
</script>