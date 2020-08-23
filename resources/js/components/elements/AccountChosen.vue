<template>
    <form-control label="Tài khoản"
                  data-vv-as="Tài khoản"
                  name="account" :required="true"
                  v-model="account"
                  type="select"
                  :select-options="accountOptions"
                  :error="error"/>
</template>

<script>

  export default {
    name: "AccountChosen",
    props: {
      error: {
        type: String,
        default: ''
      },
      value: {},
      multiple: { default: false,
      }

    },
    data() {
      return {
        account: null,
        accountOptions: {
          placeholder: 'Chọn một người dùng...',
          multiple: this.multiple,
          idField: 'id',
          textField: 'app_name',
          ajax: '/api/account/list-all'
        }
      }
    },
    watch: {
      account(val) {
        this.$emit('input', val)
      },
      value(newVal, oldVal) {
        if (newVal) {
          if (!oldVal || (oldVal && newVal.id != oldVal.id)) {
            this.account = newVal
          }
        } else {
          this.account = newVal
        }
      }
    },
    $_veeValidate: {
      value() {
        return account
      },
      name() {
        return 'account'
      }
    },
  }
</script>
