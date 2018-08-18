import Vue from 'vue'
import Vuex from 'vuex'
import * as actions from './actions'
import * as mutations from './mutations'

Vue.use(Vuex)

const state = {
    open_name:[],
    active_name: '',
    page_title: '欢迎您！',
    bread_1: {name:'',path:''},
    bread_2: {name:'',path:''},
    bread_3: {name:''},
}

const store = new Vuex.Store({
    state,
    actions,
    mutations
})

if (module.hot) {
    module.hot.accept([
        './actions',
        './mutations'
    ], () => {
        store.hotUpdate({
            actions: require('./actions'),
            mutations: require('./mutations')
        })
    })
}

export default store