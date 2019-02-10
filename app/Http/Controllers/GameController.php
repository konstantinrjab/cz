<?php

namespace App\Http\Controllers;

use App\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Game::where('status', Game::NEED_PLAYERS)->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Game::findOrFail($id);
    }

    /**
     * @param Request $request
     * @param Game $game
     * @return Game
     */
    public function update(Request $request, Game $game)
    {
        if ($game['players']['turn'] != Auth::user()->id) {
            return $game;
        }

        $cell = $request->cell;
        $sign = $game->getSign(Auth::user()->id);

        if($cell == $sign) {
            return $game;
        }

        $game->update(["state.$request->cell" => $game->getSign(Auth::user()->id)], ['upsert' => true]);
        $game->changeTurn(Auth::user()->id);
        $game->save();

        return $game;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
