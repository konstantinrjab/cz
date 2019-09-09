import VueRouter from 'vue-router'

import Home from '../pages/Welcome'
import Register from '../pages/Register'
import Login from '../pages/Login'
import Dashboard from '../pages/Dashboard'
import CreateGame from '../pages/CreateGame'
import Game from '../pages/Game'
import PageNotFound from '../pages/404'

const routes = [
    {
        path: '/',
        name: 'home',
        component: Home,
        meta: {
            auth: undefined
        }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: {
            auth: false
        }
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: {
            auth: false
        }
    },
    {
        path: '/games/create',
        name: 'games.create',
        component: CreateGame,
        meta: {
            auth: true
        }
    },
    {
        path: '/games/:id',
        name: 'games',
        component: Game,
        meta: {
            auth: true
        }
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: {
            auth: true
        }
    },
    {
        path: "*",
        component: PageNotFound
    }
];

const router = new VueRouter({
    history: true,
    mode: 'history',
    routes,
});

export default router
