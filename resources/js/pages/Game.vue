<template>
    <div class="">
        <div class="container">
            <div class="row justify-content-center" v-for="rowNumber in 3">
                <div class="col-4 cell" v-for="columnNumber in 3" @click="makeMove($event)"
                     v-bind:id="rowNumber.toString()+columnNumber.toString()"></div>
            </div>
        </div>
        <div class="statusIndicator" v-if="statusIndicator">
            <h2 class="text-center">{{ statusIndicator }}</h2>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                statusIndicator: "",
                game: {},
                socketListen: false
            }
        },
        methods: {
            getGame() {
                axios.get(`/games/${this.$route.params.id}`)
                    .then((response) => {
                            this.game = response.data;
                            if (!this.socketListen) {
                                this.listen();
                            }
                            this.renderState();
                        }
                    ).catch(error => {
                    console.error(error);
                    this.$router.push(`/404`)
                });
            },
            renderState() {
                this.renderCells();
                this.updateStatusBar();
            },
            renderCells() {
                $.each(this.game.cell_collection, function (index, gameCell) {
                    const cellId = gameCell.row.toString() + gameCell.column.toString();
                    const value = gameCell.value;

                    if (value === 'cross') {
                        $('#' + cellId).addClass('cross')
                    }
                    if (value === 'zero') {
                        $('#' + cellId).addClass('zero')
                    }
                });
            },
            updateStatusBar() {
                if (this.game.winner_id) {
                    this.updateWinnerInfo();
                } else {
                    this.updateTurnInfo();
                }
            },
            updateTurnInfo() {
                if (this.game.active_player_id === this.$auth.user().id) {
                    this.statusIndicator = 'You turn';
                } else {
                    this.statusIndicator = 'Waiting...';
                }
            },
            updateWinnerInfo() {
                if (this.game.winner_id === this.$auth.user().id) {
                    this.statusIndicator = 'You win!';
                } else {
                    this.statusIndicator = 'You lose';
                }
            },
            makeMove(event) {
                const cell = {
                    row: event.target.id.charAt(0),
                    column: event.target.id.charAt(1),
                };

                axios.put(`/games/${this.game.id}`, cell).then((response) => {
                        this.game.active_player_id = response.data.active_player_id;
                        this.game.cell_collection = response.data.cell_collection;
                        this.renderState()
                    }
                )
            },
            listen() {
                this.socketListen = true;

                Echo.channel('game.' + this.game.id)
                    .listen('.GameChangeStateEvent', (game) => {
                        console.log(game);
                        this.game = game;
                        this.renderState()
                    });
            }
        },
        mounted() {
            this.getGame();
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
