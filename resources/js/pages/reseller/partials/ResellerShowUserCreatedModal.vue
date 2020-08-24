<template>
    <the-modal :title="'Danh sách User đã tạo'"
               class="modal-xxl"
               ref="showUserCreatedModal"
               :on-hidden="onModalHidden"
               :on-shown="onModalShown"
    >
        <data-table
            v-if="modalShown"
            ref="table"
            :columns="columns"
            :url="'/api/admin/user/listing-user-created'"
            :post-data="{reseller_id: resellerId}"
            :searching="false"
        />
    </the-modal>
</template>

<script>
    import TheModal from "../../../components/common/TheModal";

    export default {
        name: "ResellerShowUserCreatedModal",
        components: { TheModal},
        data() {
            return {
                resellerId: null,
                modalShown: false,
            }
        },
        computed: {
          columns(){
            return [
                {
                    data: 'userPrincipalName',
                    title: 'Principal Name',
                },
                {
                    data: 'displayName',
                    title: 'Tên hiển thị',
                },
                {
                    data: 'id',
                    title: 'License',
                }
            ]
          }
        },
        methods: {
            show(item = null) {
                if (item != null) {
                    console.log(item);
                    this.resellerId = item.id;
                }
                this.$refs.showUserCreatedModal.show();
            },
            onModalHidden() {
                this.resellerId = null;
            },
            async onModalShown() {
                this.modalShown = true;
                await this.$nextTick();
                this.$refs.table.reload();
            },
        }
    }
</script>
