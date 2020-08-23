<template>
    <form-control label="Domain"
                  data-vv-as="Domain"
                  name="domain" :required="required"
                  v-model="domain"
                  type="select"
                  :select-options="domainOptions"
                  :is-disabled="isDisabled"
                  :error="error"/>
</template>

<script>

  export default {
    name: 'domainChosen',
    props: {
      error: {
        type: String,
        default: ''
      },
      value: {},
      multiple: {
        default: true
      },
      required: {
        default: false
      },
      isDisabled: {
        default: false
      },
      postData: {
        type: Object,
        default: () => {
        }
      }
    },
    data() {
      return {
        domain: null
      }
    },
    computed: {
      domainOptions(){
        return {
          placeholder: 'Chọn domain...',
          multiple: this.multiple,
          idField: 'id',
          textField: 'id',
          ajax: '/api/domain/list-all',
          postData: this.postData
        }
      }
    },
    watch: {
      domain(val) {
        this.$emit('input', val)
      },
      value(newVal, oldVal) {
        if (newVal) {
          if (!oldVal || (oldVal && newVal.id != oldVal.id)) {
            this.domain = newVal
          }
        } else {
          this.domain = newVal
        }
      }
    },
    $_veeValidate: {
      value() {
        return domain
      },
      name() {
        return 'domain'
      }
    }
  }
</script>
