<template>
    <div class="m-grid m-grid--hor m-grid--root m-page">
        <Header/>

        <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">
            <button class="m-aside-left-close  m-aside-left-close--skin-dark " id="m_aside_left_close_btn"><i
                class="la la-close"></i></button>
            <left-aside/>

            <div class="m-grid__item m-grid__item--fluid m-wrapper">
                <div class="m-content">
                    <child/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import Header from '~/components/layouts/Header'
    import LeftAside from '~/components/layouts/LeftAside'

    export default {
        name: 'MainLayout',
        components: {
            Header,
            LeftAside
        },
        computed: {
            ...mapState({
                routeName: state => state.route.name
            })
        }
    }
</script>
