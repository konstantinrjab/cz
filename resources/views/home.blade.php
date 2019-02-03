@extends('layouts.app')

@section('content')

    <button class="btn btn-primary">Create New Game</button>

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
                @{{game.name}}
            </td>
            <td>
                <button class="btn btn-success">Join</button>
            </td>
        </tr>
        </tbody>
    </table>

@endsection

@section('scripts')
    <script>
        const app = new Vue({
            el: '#app',
            data: {
                user: {!! json_encode(Auth::user()) !!},
                games: {}
            },
            methods: {
                getGames() {
                    //5c56bfd9c89a35
                    axios.get(`/api/games`, {
                        headers: {
                            "Authorization": "Bearer " + this.user.api_token
                        }
                    }).then((response) => {
                            this.games = response.data;
                        }
                    )
                },
            },
            mounted() {
                console.log('Component mounted.');
                this.getGames()
            }
        })
    </script>
@endsection
