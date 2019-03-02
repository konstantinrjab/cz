<template>
    <div class="container">
        <nav id="nav" class="navbar navbar-expand-sm navbar-light bg-light">
            <a class="navbar-brand" href="#">Cross Zeros</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMenu"
                    aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav mr-auto">
                    <!--UNLOGGED-->
                    <li class="nav-item" v-if="!$auth.check()" v-for="(route, key) in routes.unlogged"
                        v-bind:key="route.path">
                        <router-link class="nav-link" :to="{ name : route.path }" :key="key">
                            {{route.name}}
                        </router-link>
                    </li>
                    <li v-if="$auth.check()" v-for="(route, key) in routes.user" v-bind:key="route.path">
                        <router-link class="nav-link" :to="{ name : route.path }" :key="key">
                            {{route.name}}
                        </router-link>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li v-if="$auth.check()">
                        <a href="#" class="nav-link" @click.prevent="$auth.logout()">Logout ({{this.$auth.user().email}})</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                routes: {
                    unlogged: [
                        {
                            name: 'Register',
                            path: 'register'
                        },
                        {
                            name: 'Login',
                            path: 'login'
                        }
                    ],
                    user: [
                        {
                            name: 'Dashboard',
                            path: 'dashboard'
                        }
                    ],
                }
            }
        },
        mounted() {
            //
        }
    }
</script>