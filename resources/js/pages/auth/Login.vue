<template>
    <div class="m-grid__item m-grid__item--fluid  m-grid__item--order-tablet-and-mobile-1 m-login__wrapper">
        <div class="m-login__body">
            <div class="m-login__signin">
                <div class="m-login__title">
                    <h3>{{ $t('auth.login')}}</h3>
                </div>

                <form class="m-login__form m-form" @submit.prevent="validateForm" @keydown="form.onKeydown($event)">
                    <div style="height: 48px;" class="mb-4">
                        <alert :outline="true" color="danger"
                               v-if="form.errors.has('error')">
                           {{form.errors.get('error')}}
                        </alert>
                    </div>

                    <div class="form-group m-form__group"
                         :class="{'has-danger' : errors.has('username')}">
                        <input class="form-control m-input" type="text"
                               v-model="form.name"
                               :placeholder="$t('auth.username')"
                               name="username"
                               v-validate="'required'"
                               :data-vv-as="$t('auth.username')"
                               autocomplete="off">

                        <div class="form-control-feedback" v-if="errors.has('username')">{{ errors.first('username')
                            }}
                        </div>
                    </div>
                    <div class="form-group m-form__group"
                         :class="{'has-danger' : errors.has('password')}">
                        <input class="form-control m-input m-login__form-input--last"
                               type="password"
                               v-model="form.password"
                               :placeholder="$t('auth.password')"
                               v-validate="'required'"
                               :data-vv-as="$t('auth.password')"
                               name="password">

                        <div class="form-control-feedback" v-if="errors.has('password')">{{ errors.first('password')
                            }}
                        </div>
                    </div>

                    <div class="m-login__action">
<!--                        <router-link class="m-link" :to="'/create-ms-user'">Tạo tài khoản MS</router-link>-->
                        <a href="javascript:;" style="margin: 0 auto">
                            <v-button extra-class="m-btn--custom m-btn--air m-login__btn m-login__btn--primary"
                                      style-type="air" :loading="form.busy" type="submit">
                                {{ $t('auth.login')}}
                            </v-button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
  import { API_LOGIN } from '~/constants/url'
  import Form from 'vform'

  export default {
    name: 'Login',
    middleware: 'guest',
    layout: 'auth',
    transitionName: 'page',
    metaInfo() {
      return { title: this.$t('auth.login') }
    },
    data() {
      return {
        form: new Form({
          name: '',
          password: ''
        }),
        remember: true
      }
    },
    methods: {
      validateForm() {
        this.$validator.validateAll().then((result) => {
          if (result) {
            this.login()
          }
        })
      },
      async login() {
        const { data } = await this.form.post(API_LOGIN)

        this.$store.dispatch('auth/saveToken', {
          token: data.access_token,
          remember: this.remember
        })

        // Fetch the user.
        await this.$store.dispatch('auth/fetchUser')

        // Redirect home.
        this.$router.push('/')
      },

    }
  }
</script>
