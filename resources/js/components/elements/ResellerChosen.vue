<template>
    <form-control label="Reseller"
                  data-vv-as="Reseller"
                  name="reseller" :required="required"
                  v-model="reseller"
                  type="select"
                  :is-disabled="isDisabled"
                  :select-options="resellerOptions"
                  :error="error"/>
</template>

<script>

  export default {
    name: 'ResellerChosen',
    props: {
      error: {
        type: String,
        default: ''
      },
      value: {},
      multiple: {
        default: false
      },
      hasAllOption: {
        type: Boolean,
        default: false
      },
      required: {
        default: false
      },
      isDisabled: {
        default: false
      }
    },
    data() {
      return {
        reseller: null,
        resellerOptions: {
          placeholder: 'Chọn một reseller...',
          multiple: this.multiple,
          idField: 'id',
          textField: 'name',
          ajax: '/api/admin/user/listing-reseller',
          hasAllOption: this.hasAllOption,
          allowClear: true
        }
      }
    },
    watch: {
      reseller(val) {
        this.$emit('input', val)
      },
      value(newVal, oldVal) {
        if (newVal) {
          if (!oldVal || (oldVal && newVal.id != oldVal.id)) {
            this.reseller = newVal
          }
        } else {
          this.reseller = newVal
        }
      }
    },
    $_veeValidate: {
      value() {
        return this.reseller
      },
      name() {
        return 'reseller'
      }
    }
  }
</script>
