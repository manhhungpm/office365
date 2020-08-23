<template>
    <div class="form-group m-form__group" :class="{'has-danger': error}">
        <label v-if="label && type !=='checkbox'">{{ label }} <span class="text-danger"
                                                                    v-if="required">(*)</span></label>

        <template v-if="type == 'text' || type == 'password' || type == 'number'">
            <div class="input-group m-input-group" v-if="isGroup">
                <div class="input-group-prepend" v-if="prepend">
                    <slot name="input-group-prepend"></slot>
                </div>
                <input
                        ref="input"
                        :value="value"
                        :name="name"
                        autocomplete="off"
                        :type="type"
                        class="form-control m-input"
                        :class="inputClass"
                        :placeholder="placeholder ? placeholder : (label ? label : '')"
                        @input="$emit('input', $event.target.value)" :disabled="isDisabled" :readonly="readonly">
                <div class="input-group-append" v-if="!prepend">
                    <slot name="input-group-append"></slot>
                </div>
            </div>

            <input v-else
                   ref="input"
                   :value="value"
                   :name="name"
                   autocomplete="off"
                   :class="inputClass"
                   :type="type" class="form-control m-input"
                   :placeholder="placeholder ? placeholder : (label ? label : '')"
                   @input="$emit('input', $event.target.value)" :disabled="isDisabled">
        </template>

        <template v-else-if="type == 'checkbox'">
            <label class="m-checkbox m-checkbox--state-primary">
                <input type="checkbox" ref="input"
                       :name="name"
                       :checked="value == trueValue"
                       @change="$emit('input', $event.target.checked ? trueValue : falseValue)"> {{label}}
                <span></span>
            </label>
        </template>

        <template v-else-if="type=='select'">
            <select2
                    class="form-control m-form__control"
                    ref="input"
                    :value="value"
                    :options="selectOptions.options ? selectOptions.options : null"
                    :placeholder="selectOptions.placeholder ? selectOptions.placeholder : ''"
                    :multiple="selectOptions.multiple ? selectOptions.multiple:false"
                    :searchable="selectOptions.searchable ? selectOptions.searchable:false"
                    :allow-clear="selectOptions.allowClear ? selectOptions.allowClear:false"
                    :textField="selectOptions.textField ? selectOptions.textField:'text'"
                    :idField="selectOptions.idField ? selectOptions.idField:'id'"
                    :ajax="selectOptions.ajax ? selectOptions.ajax : null"
                    :post-data="selectOptions.postData ? selectOptions.postData : {}"
                    @input="(e)=>{this.$emit('input', e)}"
                    :has-all-option="selectOptions.hasAllOption ? selectOptions.hasAllOption : false"
                    :text-format="selectOptions.textFormat ? selectOptions.textFormat : null"
                    :disabled="isDisabled"
            ></select2>
        </template>

        <template v-else-if="type=='datepicker'">
            <date-picker
                    ref="input"
                    :placeholder="placeholder"
                    :value="value"
                    @input="(e)=>{this.$emit('input', e)}"
            ></date-picker>
        </template>

        <template v-else>
            <textarea ref="input"
                      :value="value"
                      autocomplete="off"
                      :class="inputClass"
                      :type="type" class="form-control m-input"
                      :placeholder="placeholder ? placeholder : (label ? label : '')"
                      @input="$emit('input', $event.target.value)"
                      rows="5" :disabled="isDisabled"></textarea>
        </template>

        <div class="form-control-feedback" v-if="error">{{error}}</div>
    </div>
</template>

<script>
  import DatePicker from './DatePicker'

  export default {
    components: { DatePicker },
    name: 'FormControl',
    props: {
      value: {},
      label: {
        type: String,
        default: null
      },
      name: {
        type: String,
        default: null
      },
      type: {
        type: String,
        default: 'text'
      },
      placeholder: {
        type: String,
        default: null
      },
      required: {
        type: Boolean,
        default: false
      },
      error: {
        type: String,
        default: null
      },
      isDisabled: {
        type: Boolean,
        default: false
      },
      selectOptions: {
        type: Object,
        default: () => {
        }
      },
      isGroup: {
        type: Boolean,
        default: false
      },
      trueValue: {
        default: true
      },
      falseValue: {
        default: false
      },
      prepend: {
        default: true
      },
      readonly: {
        type: Boolean,
        default: false
      },
      inputClass: {
        default: undefined
      }
    },
    $_veeValidate: {
      value() {
        return $(this.$refs.input).val()
      },
      name() {
        return this.name
      }
    }
  }
</script>
