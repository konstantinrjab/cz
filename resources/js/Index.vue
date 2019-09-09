<template>
    <div id="main">
        <navigationMenu></navigationMenu>
        <div id="content">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
    import navigationMenu from './components/Menu.vue'

    export default {
        components: {
            navigationMenu
        }
    }
</script>
