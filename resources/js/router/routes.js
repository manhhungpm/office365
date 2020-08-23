import { ROLE_ADMIN } from '~/constants/roles'

const NotFound = () => import('~/pages/errors/404.vue').then(m => m.default || m)

const Login = () => import('~/pages/auth/Login.vue').then(m => m.default || m)

const Dashboard = () => import('~/pages/dashboard').then(m => m.default || m)
const Domain = () => import('~/pages/domain/Domain.vue').then(m => m.default || m)
const Reseller = () => import('~/pages/reseller/Reseller.vue').then(m => m.default || m)
const StudentCode = () => import('~/pages/studentCode/StudentCode.vue').then(m => m.default || m)
const Account = () => import('~/pages/account/Account.vue').then(m => m.default || m)
const MSUser = () => import('~/pages/msUser/MSUser.vue').then(m => m.default || m)
const CreateMsUser = () => import('~/pages/guest/CreateMsUser.vue').then(m => m.default || m)
const LogAction = () => import('~/pages/log/logAction/LogAction.vue').then(m => m.default || m)
const Profile = () => import('~/pages/profile/Profile.vue').then(m => m.default || m)

/*
 |--------------------------------------------------------------------------
 | Admin
 |--------------------------------------------------------------------------
 */
const Admin = () => import('~/pages/admin/Admin.vue').then(m => m.default || m)
const User = () => import('~/pages/admin/User/User.vue').then(m => m.default || m)
const Role = () => import('~/pages/admin/Role/Role.vue').then(m => m.default || m)
const Permission = () => import('~/pages/admin/Permission/Permission.vue').then(m => m.default || m)

export const adminRoutes = {
  index: {path: '/', redirect: {name: 'admin.user'}},
  user: {path: 'user', name: 'admin.user', component: User, meta: {title: 'Người dùng'}},
  role: {path: 'role', name: 'admin.role', component: Role, meta: {title: 'Nhóm người dùng'}},
  permission: {path: 'permission', name: 'admin.permission', component: Permission, meta: {title: 'Quyền người dùng'}},
}

export default {
  dashboard: {path: '/', name: 'dashboard', component: Dashboard},
  domain: {path: '/domain', name: 'Quản lý Domain', component: Domain, meta: {role: ROLE_ADMIN}},
  reseller: {path: '/reseller', name: 'Quản lý Reseller', component: Reseller, meta: {role: ROLE_ADMIN}},
  studentCode: {path: '/student-code', name: 'Quản lý mã bảo mật', component: StudentCode},
  account: {path: '/account', name: 'Tài khoản Office 365', component: Account, meta: {role: ROLE_ADMIN}},
  msUser: {path: '/ms-user', name: 'Quản lý người dùng', component: MSUser},
  logAction: {path: '/log-action', name: 'Log Action', component: LogAction},
  profile: {path: '/profile', name:"Profile", component: Profile},
  login: {path: '/login', name: 'login', component: Login},
  guestMsUser: {path: '/create-ms-user', name: 'Tạo người dùng MS', component: CreateMsUser},

  admin: {
    path: '/admin',
    component: Admin,
    children: Object.values(adminRoutes)
  },
  not_found: {path: '*', name: 'not_found', component: NotFound}
}
