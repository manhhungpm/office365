<template>
    <div class="m-grid m-grid--hor m-grid--root m-page">
        <div class="m-grid__item m-grid__item--fluid m-grid m-grid--desktop m-grid--ver-desktop m-grid--hor-tablet-and-mobile m-login m-login--6">
            <div class="m-grid__item   m-grid__item--order-tablet-and-mobile-2  m-grid m-grid--hor m-login__aside">
                <div class="m-grid__item">
                    <div class="m-login__logo">
                        <a href="javascript:;">
                            <img :src="require('~assets/img/logos/logo-4.png')">
                        </a>
                    </div>
                </div>
                <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver">
                    <div class="m-grid__item m-grid__item--middle">
                        <span class="m-login__title">Chào mừng bạn đến với Office 365 Reseller</span>
                        <span class="m-login__subtitle">Hệ thống cung cấp các tính năng quản trị phục vụ kinh doanh.</span>
                    </div>
                </div>
                <div class="m-grid__item">
                    <div class="m-login__info">
                        <div class="m-login__section">
                            <router-link to="/" class="m-link">© 2019 Office 365 Reseller</router-link>
                        </div>
                        <div class="m-login__section">
                            <a href="javascript:;" class="m-link">Privacy</a>
                            <a href="javascript:;" class="m-link">Legal</a>
                            <a href="javascript:;" class="m-link">Contact</a>
                        </div>
                    </div>
                </div>
            </div>

            <child/>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'auth'
    }
</script>
