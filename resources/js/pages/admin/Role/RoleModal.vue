<template>
    <the-modal ref="modal" :title="isEdit ? $t('form.role.update_title') : $t('form.role.insert_title')"
           :onHidden="onModalHidden" :onShow="onShow" >
        <form class="m-form m-form--state m-form--label-align-right"
              @submit.prevent="validateForm">

            <form-control :label="$t('form.role.name')"
                          :data-vv-as="$t('form.role.name')"
                          name="name" :required="true"
                          v-model="form.name"
                          v-validate="'required|max:100'"
                          :error="errors.first('name') || form.errors.get('name')"/>

            <form-control :label="$t('form.role.display_name')"
                          :data-vv-as="$t('form.role.display_name')"
                          name="display_name"
                          :required="true"
                          v-model="form.display_name"
                          v-validate="'required|max:255'"
                          :error="errors.first('display_name') || form.errors.get('display_name')"/>

            <form-control :label="$t('form.role.description')"
                          :data-vv-as="$t('form.role.description')"
                          name="description"
                          type="textarea"
                          v-model="form.description"
                          v-validate="'max:255'"
                          :error="errors.first('description')|| form.errors.get('description')"/>

            <div class="form-group m-form__group">
                <div class="row">
                    <div class="col-md-12">
                        <label>Danh sách quyền người dùng</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Tìm kiếm quyền người dùng"
                                   v-model="search">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-search"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <perfect-scrollbar class="inputSearchPermission m-scrollable" >
                    <div class="container-fluid row">
                        <div class="col-md-6" v-for="item in filteredList" data-height="150">

                            <div class="m-checkbox m-checkbox--primary">
                                <label class="m-checkbox m-checkbox--check-bold m-checkbox--state-brand">
                                    <input type="checkbox" class="form-check-input"
                                           :value="item.id"
                                           v-model="form.permissionIdList"
                                    >
                                    {{ item.display_name }}
                                    <span></span></label>
                            </div>

                        </div>
                    </div>
                </perfect-scrollbar>
            </div>


        </form>

        <template slot="footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $t('button.cancel')}}</button>
            <button type="button" class="btn btn-primary" @click="validateForm">
                {{ isEdit ? $t('button.update'): $t('button.add')}}
            </button>
        </template>
    </the-modal>
</template>

<script>
    import Form from 'vform'
    import axios from 'axios'
    import {API_ROLE_STORE, API_ROLE_EDIT,API_ROLE_GET_PERMISSION_LIST} from '~/constants/url'
    import {SUCCESS} from '~/constants/code'
    import {notify, notifyTryAgain, notifyUpdateSuccess, notifyAddSuccess} from '~/helpers/bootstrap-notify'
    import PerfectScrollbar from "../../../components/common/PerfectScrollbar";

    const defaultRole = {
        name: '',
        display_name: '',
        description: '',
        permissionIdList: [],
    }

    export default {
        name: 'RoleModal',
      components: {PerfectScrollbar},
      props: {
            onActionSuccess: {
                type: Function,
                default: () => {
                }
            },
        },
        data() {
            return {
                isEdit: false,
                form: new Form(defaultRole),
                permissionList:[],
                search: '',
            }
        },
        created: function () {
            this.loadData();
        },
        methods: {
            async loadData() {
                try {
                    let res = await axios.get(API_ROLE_GET_PERMISSION_LIST)
                    let {data} = res
                    this.permissionList = data.data
                    this.isLoading = false
                } catch (e) {

                }
            },
            validateForm() {
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        if (this.isEdit) {
                            this.updateItem()
                        } else {
                            this.addItem()
                        }
                    }
                })
            },
            show(item = null) {
                if (item != null) {
                    this.form = new Form(item)
                    //cập nhật những permission có sẵn
                    this.form.permissionIdList = this.form.perms.map(function (e) {
                        return e.id;
                    })
                    this.isEdit = true
                }

                this.$refs.modal.show()
            },
            onModalHidden() {
                this.form = new Form(defaultRole)
                this.isEdit = false
                this.search=''
                this.$validator.reset()
            },
            onShow(){
                $(".inputSearchPermission").animate({scrollTop: 0}, "slow");
            },
            async addItem() {
                try {
                    const {data} = await this.form.post(API_ROLE_STORE)

                    if (data.code == SUCCESS) {
                        notifyAddSuccess(this.$t('form.role.role'))
                        this.$refs.modal.hide()
                        this.onActionSuccess()
                    } else {
                        notifyTryAgain()
                    }
                }
                catch (e) {
                    const {status} = e.response

                    if (status != 422) {
                        notifyTryAgain()
                    }
                }
            },
            async updateItem() {
                try {
                    const {data} = await this.form.post(API_ROLE_EDIT)

                    if (data.code == SUCCESS) {
                        notifyUpdateSuccess(this.$t('form.role.role'))
                        this.$refs.modal.hide()
                        this.onActionSuccess()
                    } else {
                        notifyTryAgain()
                    }
                }
                catch (e) {
                    const {status} = e.response

                    if (status != 422) {
                        notifyTryAgain()
                    }
                }

            }
        },
        computed: {
            //function search permission
            filteredList() {
                return this.permissionList.filter(item => {
                    return item.display_name.toLowerCase().includes(this.search.toLowerCase())
                })
            }
        }
    }
</script>
