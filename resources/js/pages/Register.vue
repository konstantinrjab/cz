<template>
    <div class="container">
        <div class="card card-default">
            <div class="card-header">Inscription</div>
            <div class="card-body">
                <form autocomplete="off" @submit.prevent="register" v-if="!success" method="post">
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" class="form-control"
                               v-bind:class="{ 'is-invalid': has_error && errors.email }"
                               placeholder="user@example.com" v-model="email">
                        <span class="help-block text-danger" v-if="has_error && errors.email">{{ errors.email[0] }}</span>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" class="form-control" v-model="password"
                               v-bind:class="{ 'is-invalid': has_error && errors.password }">
                        <span class="help-block text-danger" v-if="has_error && errors.password">{{ errors.password[0] }}</span>
                    </div>
                    <div class="form-group" v-bind:class="{ 'has-error': has_error && errors.password }">
                        <label for="password_confirmation">Password confirmation</label>
                        <input type="password" id="password_confirmation" class="form-control"
                               v-bind:class="{ 'is-invalid': has_error && errors.password }"
                               v-model="password_confirmation">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                has_error: false,
                error: '',
                errors: {},
                success: false
            }
        },
        methods: {
            register() {
                let app = this;
                this.$auth.register({
                    data: {
                        email: app.email,
                        password: app.password,
                        password_confirmation: app.password_confirmation
                    },
                    success: function () {
                        app.success = true;
                        this.$router.push({name: 'login', params: {successRegistrationRedirect: true}})
                    },
                    error: function (res) {
                        app.has_error = true;
                        app.error = res.response.data.error;
                        app.errors = res.response.data.errors || {}
                    }
                })
            }
        }
    }
</script>