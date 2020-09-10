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
            :url="'/api/student-code/listing-user-created'"
            :post-data="{code: code}"
            :searching="false"
        />
    </the-modal>
</template>

<script>
    import TheModal from "../../../components/common/TheModal";

    export default {
        name: "StudentCodeShowUserCreatedModal",
        components: { TheModal},
        data() {
            return {
                code: null,
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
                    this.code = item.code;
                }
                this.$refs.showUserCreatedModal.show();
            },
            onModalHidden() {
                this.code = null;
            },
            async onModalShown() {
                this.modalShown = true;
                await this.$nextTick();
                this.$refs.table.reload();
            },
        }
    }
</script>
