<template>
    <the-modal ref="modal" :title="'Tăng số lượng Max User'"
               :onHidden="onModalHidden">
        <form class="m-form m-form--fit m-form--state m-form--label-align-right" ref="form"
              autocomplete="off"
              @submit.prevent="validateForm">

            <form-control label="Số người dùng muốn thêm"
                          data-vv-as="Số người dùng muốn thêm"
                          name="num_user_max"
                          type="number"
                          v-model="form.num_user_max"
                          v-validate="'max_value:25'"
                          :error="errors.first('num_user_max') || form.errors.get('num_user_max')"/>
        </form>

        <template slot="footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $t('button.cancel')}}</button>
            <button type="button" class="btn btn-primary" @click="validateForm">
                {{ $t('button.add')}}
            </button>
        </template>
    </the-modal>
</template>

<script>
    import Form from 'vform'
    import {API_USER_INCREASE_MAX_USER} from '~/constants/url'
    import {SUCCESS} from '~/constants/code'
    import {notify, notifyTryAgain} from '~/helpers/bootstrap-notify'

    const defaultUser = {
        num_user_max: 0,
    }

    export default {
        name: "ResellerIncreaseMaxUserModal",
        props: {
            onActionSuccess: {
                type: Function,
                default: () => {
                }
            }
        },
        data() {
            return {
                form: new Form(defaultUser)
            }
        },
        mounted() {
        },
        methods: {
            validateForm() {
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.addItem()
                    }
                })
            },
            show(item = null) {
                this.form.id_item = item.id;
                this.$refs.modal.show()
            },
            onModalHidden() {
                this.form = new Form(defaultUser)
                this.$validator.reset()
            },
            async addItem() {
                try {
                    const {data} = await this.form.post(API_USER_INCREASE_MAX_USER)

                    if (data.code == SUCCESS) {
                        notify("Thông báo","Tăng số lượng User thành công","success")
                        this.$refs.modal.hide()
                        this.onActionSuccess()
                    } else {
                        notifyTryAgain()
                    }
                } catch (e) {
                    console.log(e)
                }
            }
        },
        computed: {}
    }
</script>

