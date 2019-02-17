import VueRouter from 'vue-router'
// Pages
import Home from './pages/Home'
import Register from './pages/Register'
import Login from './pages/Login'
import Dashboard from './pages/user/Dashboard'
import AdminDashboard from './pages/admin/Dashboard'
import CreateGame from './pages/CreateGame'
import Game from './pages/Game'
import PageNotFound from './pages/404'
// Routes
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
        path: '/game/create',
        name: 'game.create',
        component: CreateGame,
        meta: {
            auth: true
        }
    },
    {
        path: '/game/:id',
        name: 'game',
        component: Game,
        meta: {
            auth: true
        }
    },
    // USER ROUTES
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: {
            auth: true
        }
    },
    // ADMIN ROUTES
    {
        path: '/admin',
        name: 'admin.dashboard',
        component: AdminDashboard,
        meta: {
            auth: {roles: 2, redirect: {name: 'login'}, forbiddenRedirect: '/403'}
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