@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row justify-content-center" v-for="n in 3">
            <div class="col-4 cell" v-for="m in 3" @click="makeMove($event)" v-bind:id="''+n+m"></div>
        </div>
    </div>

@endsection

@section('styles')
    <style>
        .cell {
            height: 150px;
            border: 1px solid black;
            background: #1d68a7;
        }
    </style>
@endsection

@section('scripts')
    <script>
        const app = new Vue({
            el: '#app',
            data: {
                user: {!! json_encode(Auth::user()) !!}
            },
            methods: {
                makeMove(event) {
                    console.log(event.target.id);
                    axios.post(`/api/game/move`, {
                        cell: event.target.id,
                        api_token: this.user.api_token,
                    }).then((response) => {
                            console.log(1);
                        }
                    )
                },
            },
            mounted() {
                console.log('Component mounted.')
            }
        })
    </script>
@endsection
