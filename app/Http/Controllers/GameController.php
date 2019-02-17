<?php

namespace App\Http\Controllers;

use App\Game;
use App\Http\Requests\CreateGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @param CreateGame $request
     * @return mixed
     */
    public function store(CreateGame $request)
    {
        $state = new \StdClass();
        for ($i = 1; $i <= 3; $i++) {
            for ($j = 1; $j <= 3; $j++) {
                $state->{$i . $j} = null;
            }
        }
        $players = new \StdClass();
        $players->turn = Auth::user()->id;
        $players->{1} = Auth::user()->id;
        $players->{0} = null;

        return Game::create([
            'status'   => 2,
            'name'     => $request->name,
            'password' => $request->password,
            'winner'   => null,
            'players'  => $players,
            'state'    => $state,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $game = Game::find($id);
        if (!$game) {
            throw new NotFoundHttpException();
        }
        $p = $game['players'];

        foreach ($game['players'] as $player) {
            if ($player == Auth::user()->id) {
                return $game;
            }
        }

        return response()->json(['error' => 'Not authorized.'],403);
    }

    /**
     * @param Request $request
     * @param Game $game
     * @return Game
     */
    public function update(Request $request, Game $game)
    {
        if ($game['status'] !== Game::STARTED) {
            return $game;
        }

        if ($game['winner']) {
            return $game;
        }

        if ($game['players']['turn'] != Auth::user()->id) {
            return $game;
        }

        $cell = $request->cell;

        if (!is_null($game['state'][$cell])) {
            return $game;
        }

        $sign = $game->getSign(Auth::user()->id);

        if ($cell == $sign) {
            return $game;
        }

        $game->update(["state.$request->cell" => $game->getSign(Auth::user()->id)]);

        $game->changeTurn(Auth::user()->id);
        $game->checkWinner();
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

    public function join(Request $request, Game $game)
    {
        if (empty($game['players'][Game::ZERO])) {
            $game->update(["players." . Game::ZERO => Auth::user()->id]);
        }

        if (!empty($game['players'][Game::CROSS]) && !empty($game['players'][Game::ZERO])) {
            $game->update(["status" => Game::STARTED]);
        }

        if ($game['players'][Game::CROSS] == Auth::user()->id or $game['players'][Game::ZERO] == Auth::user()->id) {
            return $game;
        }

        if (!empty($game['players'][Game::CROSS]) && !empty($game['players'][Game::ZERO])) {
            return response('Game already started', 403);
        }

        return $game;
    }
}
