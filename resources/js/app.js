import 'es6-promise/auto'
import axios from 'axios'
import './bootstrap'
import Vue from 'vue'
import VueAuth from '@websanova/vue-auth'
import VueAxios from 'vue-axios'
import VueRouter from 'vue-router'
import Index from './Index'
import auth from './entity/auth'
import router from './entity/router'
import './entity/unauthtorized_interseptor'

window.Vue = Vue;

Vue.router = router;
Vue.use(VueRouter);
Vue.use(VueAxios, axios);
Vue.use(VueAuth, auth);

Vue.component('index', Index);
axios.defaults.baseURL = '/api';

const app = new Vue({
    el: '#app',
    router
});
