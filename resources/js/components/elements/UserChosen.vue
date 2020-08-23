<template>
    <form-control label="Tài khoản"
                  data-vv-as="Tài khoản"
                  name="user" :required="required"
                  v-model="user"
                  type="select"
                  :select-options="userOptions"
                  :error="error"/>
</template>

<script>

  export default {
    name: "UserChosen",
    props: {
      error: {
        type: String,
        default: ''
      },
      value: {},
      multiple: {
        default: false,
      },
      hasAllOption: {
        type: Boolean,
        default: false
      },
      required: {
        default: false
      }

    },
    data() {
      return {
        user: null,
        userOptions: {
          placeholder: 'Chọn một người dùng...',
          multiple: this.multiple,
          idField: 'id',
          textField: 'name',
          ajax: '/api/admin/user/listing-all',
          hasAllOption: this.hasAllOption
        }
      }
    },
    watch: {
      user(val) {
        this.$emit('input', val)
      },
      value(newVal, oldVal) {
        if (newVal) {
          if (!oldVal || (oldVal && newVal.id != oldVal.id)) {
            this.user = newVal
          }
        } else {
          this.user = newVal
        }
      }
    },
    $_veeValidate: {
      value() {
        return user
      },
      name() {
        return 'user'
      }
    },
  }
</script>
