<template>
    <div class="">
        <div class="container">
            <div class="row justify-content-center" v-for="n in 3">
                <div class="col-4 cell" v-for="m in 3" @click="makeMove($event)" v-bind:id="''+n+m"></div>
            </div>
        </div>
        <div class="result" v-if="result">
            <h2 class="text-center">{{ result }}</h2>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                result: "",
                game: {}
            }
        },
        methods: {
            renderState(data) {
                $.each(data.state, function (index, value) {
                    if (value === 1) {
                        $('#' + index).addClass('cross')
                    }
                    if (value === 0) {
                        $('#' + index).addClass('zero')
                    }
                });
                this.checkWinner()
            },
            makeMove(event) {
                axios.put(`/games/${this.game._id}`, {
                    cell: event.target.id,
                }).then((response) => {
                        this.renderState(response.data)
                    }
                )
            },
            getGame() {
                console.log(this.$auth.user()._id);
                axios.get(`/games/${this.$route.params.id}`)
                    .then((response) => {
                            this.game = response.data;
                            this.renderState(response.data);
                        }
                    ).catch(error => {
                    this.$router.push(`/404`)
                });
            },
            checkWinner() {
                if (!this.game['winner']) return;

                if (this.game['winner'] === this.$auth.user()._id) {
                    this.result = 'You win';
                } else {
                    this.result = 'You lose';
                }
            }
        },
        mounted() {
            this.getGame()
        }
    }
</script>

<style>
    .cell {
        flex: 0 0 150px;
        padding: 0;
        height: 150px;
        border: 1px solid black;
        background: #1d68a7;
    }

    .cross:hover {
        opacity: 1;
    }

    .cross:before, .cross:after {
        position: absolute;
        left: 50%;
        top: 25px;
        content: ' ';
        height: 100px;
        width: 2px;
        background-color: #333;
    }

    .cross:before {
        transform: rotate(45deg);
    }

    .cross:after {
        transform: rotate(-45deg);
    }

    .zero:before {
        position: absolute;
        left: 25px;
        top: 25px;
        content: ' ';
        height: 100px;
        border: 2px solid black;
        border-radius: 50%;
        width: 100px;
    }

</style>