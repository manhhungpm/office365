<template>
    <the-modal
        ref="renewPasswordModal"
        :title="'Đổi mật khẩu'"
        :on-hidden="onModalHidden"
    >
        <form
            ref="form"
            class="m-form m-form--fit m-form--state m-form--label-align-right"
            @submit.prevent="validateForm"
        >
            <form-control
                v-model="form.domain"
                :label="'Principal Name'"
                :data-vv-as="'Principal Name'"
                :is-disabled="true"
                name="principalName"
            />

            <form-control
                ref="password"
                v-model="form.password"
                v-validate="'required|max:128|isPassword:true'"
                :label="'Mật khẩu'"
                :data-vv-as="'Mật khẩu'"
                name="password"
                type="password"
                :required="true"
                :error="errors.first('password') || form.errors.get('password')"
            />

            <form-control
                v-model="form.password_confirmation"
                v-validate="'required|confirmed:password'"
                :label="'Xác nhận mật khẩu'"
                :data-vv-as="'Xác nhận mật khẩu'"
                name="password_confirmation"
                type="password"
                :required="true"
                :error="
                    errors.first('password_confirmation') ||
                        form.errors.get('password_confirmation')
                "
            />
        </form>

        <template slot="footer">
            <button
                type="button"
                class="btn btn-secondary"
                data-dismiss="modal"
            >
                {{ $t("button.cancel") }}
            </button>
            <button type="button" class="btn btn-primary" @click="validateForm">
                {{ $t("button.update") }}
            </button>
        </template>
    </the-modal>
</template>

<script>
    import Form from "vform";
    import {SUCCESS} from "~/constants/code";
    import {
        notifyTryAgain,
        notifyUpdateSuccess
    } from "~/helpers/bootstrap-notify";
    import FormControl from "../../../components/common/FormControl";
    import TheModal from "../../../components/common/TheModal";
    import {API_USER_UPDATE_PASSWORD} from '~/constants/url'

    const defaultUser = {
        domain: null,
        password: null,
        password_confirmation: null
    };
    export default {
        name: "MSUserRenewPasswordModal",
        components: {TheModal, FormControl},
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
            };
        },
        computed: {},
        mounted() {
        },
        methods: {
            validateForm() {
                this.$validator.validateAll().then(result => {
                    if (result) {
                        this.saveItem();
                    }
                });
            },
            show(item = null) {
                if (item != null) {
                    this.form = new Form(item);
                    this.form.domain = item.domain.id;
                }
                this.$validator.reset();
                this.$refs.renewPasswordModal.show();
            },
            onModalHidden() {
                this.form = new Form(defaultUser);
                this.$validator.reset();
            },
            async saveItem() {
                try {
                    const {data} = await this.form.post("/api/ms-user/update-password");

                    if (data.code == SUCCESS) {
                        notifyUpdateSuccess(this.$t("form.user.update_password"));
                        this.$refs.renewPasswordModal.hide();
                        this.onActionSuccess();
                    } else {
                        notifyTryAgain();
                    }
                } catch (e) {
                    console.log(e)
                }
            }
        }
    }
</script>
