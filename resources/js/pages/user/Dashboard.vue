<template>
    <div class="container">
        <div class="card card-default">
            <div class="card-header">Dashboard</div>
            <div class="card-body">
                <div class="form-group">
                    <router-link to="/games/create" class="btn btn-success form-control">Create New Game</router-link>

                </div>
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
                            <button class="btn btn-outline-primary" @click="joinGame(game._id)">Join</button>
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
        methods: {
            getGames() {
                axios.get(`/games`)
                    .then((response) => {
                            this.games = response.data;
                        }
                    )
            },
            joinGame(gameID) {
                axios.post(`/games/${gameID}/join`)
                    .then((response) => {
                        this.$router.push(`/games/${response.data._id}`)
                        }
                    ).catch((error) => {
                    console.log(error);
                });
            },
        },
        mounted() {
            this.getGames()
        }
    }
</script>