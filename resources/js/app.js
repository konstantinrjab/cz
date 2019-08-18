import 'es6-promise/auto'
import axios from 'axios'
import './bootstrap'
import Vue from 'vue'
import VueAuth from '@websanova/vue-auth'
import VueAxios from 'vue-axios'
import VueRouter from 'vue-router'
import Index from './Index'
import auth from './auth'
import router from './router'

window.Vue = Vue;

Vue.router = router;
Vue.use(VueRouter);

Vue.use(VueAxios, axios);

axios.defaults.baseURL = '/api';

Vue.use(VueAuth, auth);

Vue.component('index', Index);
const app = new Vue({
    el: '#app',
    router
});
