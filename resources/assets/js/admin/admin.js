/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('../bootstrap');

import Vue from 'vue'
import store from './store/';
import router from './router.js';
import iView from 'iview';
import App from './components/App.vue';
import 'iview/dist/styles/iview.css';

Vue.use(iView);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

let rbacConfig = $("div#app").data('rbac');

router.beforeEach((to, from, next) => {
    let toPath = to.path;

    if (toPath === '/admin') {
        store.dispatch('SET_NAV',{oName:'1',aName:''})
        store.dispatch('CHANGE_TITLE',{title:'欢迎您！'})
        store.dispatch('CHANGE_BREAD',{
            bread_1:{name:'首页',path:''},
        })
    }
    else if (toPath === '/admin/users') {
        store.dispatch('SET_NAV',{oName:'3',aName:'3-1'})
        store.dispatch('CHANGE_TITLE',{title:'用户管理'})
        store.dispatch('CHANGE_BREAD',{
            bread_1:{name:'首页',path:'/admin'},
            bread_2:{name:'用户管理',path:'/admin/users'},
            bread_3:{name:'用户列表'},
        })
    }
    else if (toPath === '/admin/roles') {
        store.dispatch('SET_NAV',{oName:'3',aName:'3-2'})
        store.dispatch('CHANGE_TITLE',{title:'角色管理'})
        store.dispatch('CHANGE_BREAD',{
            bread_1:{name:'首页',path:'/admin'},
            bread_2:{name:'角色管理',path:'/admin/roles'},
            bread_3:{name:'角色列表'},
        })
    }
    else if (toPath === '/admin/permissions') {
        store.dispatch('SET_NAV',{oName:'3',aName:'3-3'})
        store.dispatch('CHANGE_TITLE',{title:'功能模块'})
        store.dispatch('CHANGE_BREAD',{
            bread_1:{name:'首页',path:'/admin'},
            bread_2:{name:'功能模块',path:'/admin/permissions'},
            bread_3:{name:'模块列表'},
        })
    }

    if ((toPath === '/admin') && !rbacConfig['admin']) {
        router.push({path:'/admin/forbidden'})
    }
    else if ((toPath === '/admin/users') && !rbacConfig['rbac_manage']) {
        router.push({path:'/admin/forbidden'})
    }
    else if ((toPath === '/admin/roles') && !rbacConfig['rbac_manage']) {
        router.push({path:'/admin/forbidden'})
    }
    else if ((toPath === '/admin/permissions') && !rbacConfig['rbac_manage']) {
        router.push({path:'/admin/forbidden'})
    }
    else {
        next()
    }
})

const app = new Vue({
    router,
    store,
    el: '#app',
    render: h => h(App)
});


