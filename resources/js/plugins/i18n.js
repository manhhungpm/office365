import Vue from 'vue'
import store from '~/store'
import VueI18n from 'vue-i18n'
import VeeValidate, { Validator } from 'vee-validate'
import {customRule} from  '~/helpers/customRule';

Vue.use(VueI18n)

const i18n = new VueI18n({
    locale: 'vi',
    fallbackLocale: 'en',
    messages: {},
    silentTranslationWarn: true
})

const config = {
    i18n: i18n
}

Vue.use(VeeValidate, config)

/**
 * @param {String} locale
 */
export async function loadMessages (locale) {

    const messages = await import('~/lang/' + locale)

    i18n.setLocaleMessage(locale, messages)

    //setup for vee-validate
    const validateMessage = await import(`vee-validate/dist/locale/${locale}`)

    Validator.localize(locale, validateMessage)

    if (i18n.locale !== locale) {
        i18n.locale = locale
    }

    customRule();

}

;(async function () {
    await loadMessages(store.getters['lang/locale'])
})()

export default i18n
