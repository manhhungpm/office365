import routes, {adminRoutes} from '~/router/routes'
import { ROLE_ADMIN } from '../../../constants/roles'

export default [
    {
        title: 'Dashboard',
        icon: 'flaticon-line-graph',
        route: routes.dashboard
    },
    {
        title: 'Quản lý Domain',
        icon: 'flaticon-globe',
        route: routes.domain,
      role: ROLE_ADMIN
    },
    {
        title: 'Quản lý Reseller',
        icon: 'flaticon-businesswoman',
        route: routes.reseller,
        role: ROLE_ADMIN
    },
    {
        title: 'Quản lý mã bảo mật',
        icon: 'flaticon-layers',
        route: routes.studentCode
    },
    {
        title: 'Quản lý người dùng',
        icon: 'flaticon-users',
        route: routes.msUser
    },
    {
        title: 'Tài khoản Office 365',
        icon: 'flaticon-cogwheel-2',
        route: routes.account,
        role: ROLE_ADMIN
    },
]
