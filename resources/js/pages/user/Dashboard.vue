<template>
    <div class="container">
        <div class="card card-default">
            <div class="card-header">Dashboard</div>
            <div class="card-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Join</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="game in games">
                        <td>
                            {{game.name}}
                        </td>
                        <td>
                            <button class="btn btn-success">Join</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                games: {}
            }
        },
        components: {
            //
        },
        methods: {
            getGames() {
                //5c56bfd9c89a35
                axios.get(`/games`, {
                    // headers: {
                    //     "Authorization": "Bearer " + this.user.api_token
                    // }
                }).then((response) => {
                        this.games = response.data;
                    }
                )
            },
        },
        mounted() {
            this.getGames()
        }
    }
</script>