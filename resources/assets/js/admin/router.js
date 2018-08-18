import Vue from 'vue'
import Router from 'vue-router'
import Default from './components/Default.vue'
import NotFoundView from './components/NotFoundView.vue'
import ForbiddenView from './components/ForbiddenView.vue'

// user
import UsersList from './components/users/List.vue'
import UserEdit from './components/users/Edit.vue'
import UserInfo from './components/users/Info.vue'
import ChangePassword from './components/users/Password.vue'
// role
import RolesList from './components/roles/List.vue'
// role
import PermissionsList from './components/permissions/List.vue'

Vue.use(Router)

export default new Router({
    mode: 'history',
    routes: [
        {path: '/admin',component: Default},

        {path: '/admin/users',component: UsersList},
        {path: '/admin/users/info/:id',component: UserInfo},
        {path: '/admin/users/edit/:id',component: UserEdit},
        {path: '/admin/roles',component: RolesList},
        {path: '/admin/change-password',component: ChangePassword},
        {path: '/admin/permissions',component: PermissionsList},
        {path: '/admin/forbidden',component: ForbiddenView},

        {path: '/admin/*',component: NotFoundView},

    ]
})