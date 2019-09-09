<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <form autocomplete="off" @submit.prevent="create">
                    <div class="form-group">
                        <label for="game-name">Name</label>
                        <input type="text" class="form-control" id="game-name"
                               v-model="name" placeholder="My game">
                        <span class="help-block text-danger" v-if="errors.name">
                            {{ errors.name[0] }}
                        </span>
                    </div>
                    <div class="form-group">
                        <label for="game-password">Password</label>
                        <input type="password" class="form-control" id="game-password"
                               v-model="password" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-primary">Create</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                name: "",
                password: "",
                errors: {}
            }
        },
        methods: {
            create() {
                axios.post(`/games`, {
                    name: this.name,
                    password: this.password,
                })
                    .then((response) => {
                            if (!response.data.id) {
                                this.$router.push('/404');
                            }

                            this.$router.push(`/games/${response.data.id}`);
                        }
                    )
                    .catch((error) => {
                            this.errors = error.response.data.errors;
                        }
                    );
            }
        }
    }
</script>
