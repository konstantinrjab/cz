<template>
    <div class="container">
        <div class="row justify-content-center" v-for="n in 3">
            <div class="col-4 cell" v-for="m in 3" @click="makeMove($event)" v-bind:id="''+n+m"></div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['apiToken'],
        data() {
            return {
                game: {
                    id: 1
                }
            }
        },
        methods: {
            makeMove(event) {
                axios.post(`/api/game/move/`, {
                    cell: event.target.id,
                    api_token: this.apiToken,
                }).then((response) => {
                        console.log(1);
                    }
                )
            },
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>

<style>
    .cell {
        height: 150px;
        border: 1px solid black;
        background: #1d68a7;
    }
</style>