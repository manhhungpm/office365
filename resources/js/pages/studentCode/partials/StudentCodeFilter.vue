<template>
    <div>
        <div class="m-portlet__body">
            <div
                id="m_accordion_5"
                class="m-accordion m-accordion--default m-accordion--toggle-arrow"
                role="tablist"
            >
                <div class="m-accordion__item m-accordion__item--brand">
                    <div
                        id="m_accordion_5_item_3_head"
                        class="m-accordion__item-head collapsed"
                        role="tab"
                        data-toggle="collapse"
                        href="#m_accordion_5_item_3_body"
                        aria-expanded="true"
                    >
                        <span class="m-accordion__item-title">
                            Tìm kiếm</span
                        >
                        <span class="m-accordion__item-mode"></span>
                    </div>
                    <div
                        id="m_accordion_5_item_3_body"
                        class="m-accordion__item-body collapse show"
                        role="tabpanel"
                        aria-labelledby="m_accordion_5_item_3_head"
                        data-parent="#m_accordion_5"
                    >
                        <div class="m-accordion__item-content">
                            <div class="row">
                                <div class="col-6">
                                    <domain-chosen v-model="form.domain" :multiple="false"></domain-chosen>
                                </div>

                                <div class="col-6">
                                    <reseller-chosen v-model="form.reseller" :multiple="false"></reseller-chosen>
                                </div>

                                <div
                                    class="col-md-12 d-flex justify-content-center"
                                >
                                    <v-button
                                        color="primary"
                                        style-type="air"
                                        class="m-btn m-btn--icon"
                                        style="margin-right: 5px"
                                        @click.native="validateForm"
                                    >
                                        <span>
                                            <i class="la la-search"></i>
                                            <span>Tìm kiếm</span>
                                        </span>
                                    </v-button>
                                    <v-button
                                        color="accent"
                                        style-type="air"
                                        class="m-btn m-btn--icon"
                                        style="margin-right: 5px"
                                        @click.native="reset"
                                    >
                                        <span>
                                            <i class="la la-refresh"></i>
                                            <span>Reset</span>
                                        </span>
                                    </v-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Form from "vform";
    import axios from "axios";
    import DomainChosen from "../../../components/elements/DomainChosen";
    import ResellerChosen from "../../../components/elements/ResellerChosen";

    const defaultForm = {
        domain: null,
        reseller: null,
    };
    export default {
        name: "StudentCodeFilter",
        components: {ResellerChosen, DomainChosen},
        props: {
            onActionSuccess: {
                type: Function,
                default: () => {
                }
            }
        },
        data() {
            return {
                form: new Form(defaultForm),
            };
        },
        mounted() {},
        methods: {
            validateForm() {
                this.$validator.validateAll().then(result => {
                    if (result) {
                        this.search();
                    }
                });
            },
            search() {
                let searchParams = this.filter();
                this.$emit("search", searchParams);
            },
            reset() {
                this.form = new Form(defaultForm);
            },
            filter() {
                let searchParams = {};

                if (this.form.domain) {
                    searchParams.domain = this.form.domain.domain_id;
                }

                if (this.form.reseller) {
                    searchParams.reseller = this.form.reseller.id;
                }

                return searchParams;
            },
        }
    }
</script>
