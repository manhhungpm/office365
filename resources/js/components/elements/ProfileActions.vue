<template>
    <dropdown v-if="user"
              extra-class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img m-dropdown--medium m-dropdown--header-bg-fill m-dropdown--mobile-full-width"
              header-class="m-nav__link"
              align="right">
        <template slot="header">
            <span class="m-topbar__userpic">
                <img :src="require('~assets/img/users/user4.jpg')"
                     class="m--img-rounded m--marginless" alt=""/>
            </span>
            <span class="m-topbar__username m--hide">{{ user.name }}</span>
        </template>

        <template slot="content">
            <div class="m-dropdown__header m--align-center">
                <div class="m-card-user m-card-user--skin-dark">
                    <div class="m-card-user__pic">
                        <img :src="require('~assets/img/users/user4.jpg')" class="m--img-rounded m--marginless"
                             alt=""/>
                    </div>
                    <div class="m-card-user__details">
                        <span class="m-card-user__name m--font-weight-500">{{ user.name }}</span>
                        <a href="" class="m-card-user__email m--font-weight-300 m-link">{{ user.email }}</a>
                    </div>
                </div>
            </div>
            <div class="m-dropdown__body">
                <div class="m-dropdown__content">
                    <ul class="m-nav m-nav--skin-light">
                        <li class="m-nav__section m--hide">
                            <span class="m-nav__section-text">Section</span>
                        </li>
                        <li class="m-nav__item">
                            <a href="javascript:;" class="m-nav__link">
                                <i class="m-nav__link-icon flaticon-profile-1"></i>
                                <span class="m-nav__link-title">
                                    <span class="m-nav__link-wrap">
                                        <router-link :to="'/profile'" class="m-nav__link-text">{{ $t('profile.my_profile')}}</router-link>
                                    </span>
                                </span>
                            </a>
                        </li>
                        <li class="m-nav__item">
                            <a href="javascript:;" @click="logout"
                               class="btn m-btn--pill btn-secondary m-btn m-btn--custom m-btn--label-brand m-btn--bolder">
                                {{$t('auth.logout')}}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </template>
    </dropdown>
</template>

<script>
  import { mapState } from 'vuex'

  export default {
    name: 'ProfileActions',
    methods: {
      async logout() {
        await this.$store.dispatch('auth/logout')

        this.$router.push({ name: 'login' })
      }
    },
    computed: {
      ...mapState({
        user: state => state.auth.user
      })
    }
  }
</script>
