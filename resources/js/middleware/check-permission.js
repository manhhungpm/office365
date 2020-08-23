import store from '~/store'

export default async (to, from, next) => {

    let hasPermission = true

    to.matched.forEach(router => {
        if (router.meta.permission) {
            let index = store.getters['auth/perms'].findIndex(item => item.name === router.meta.permission)

            if (index === -1) {
                hasPermission = false
                return
            }
        }
    })

    if (!hasPermission) {
        next({ name: 'not_found' })
    }else{
        next()
    }
}
