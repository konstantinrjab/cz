<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <form action="" @submit.prevent="create">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name"
                               v-model="name" placeholder="MyGame">
                        <span class="help-block text-danger" v-if="errors.name">{{ errors.name[0] }}</span>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password"
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
                            this.$router.push(`/games/${response.data._id}`)
                        }
                    )
                    .catch((error) => {
                            this.errors = error.response.data.errors;
                        }
                    );
            }
        },
        mounted() {
        }
    }
</script>
