import store from '~/store'

export default async (to, from, next) => {

  let hasRole = true

  to.matched.forEach(router => {
    if (router.meta.role) {
      hasRole = store.getters['auth/role'] === router.meta.role

    }
  })

  if (!hasRole) {
    next({ name: 'not_found' })
  } else {
    next()
  }
}
