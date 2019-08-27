<?php

namespace App\Http\Controllers;

use App\Events\GameTurn;
use App\Entity\Game;
use App\Http\Collections\GameCollection;
use App\Http\Requests\CreateGame;
use App\Http\Services\GameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GameController extends Controller
{
    /** @var GameService $gameService */
    private $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function index(): GameCollection
    {
        $result = $this->gameService->getAbleToConnectList();

        return $result;
    }

    public function store(CreateGame $request): Game
    {
        $game = $this->gameService->createGame($request->get('name'), $request->get('password'));

        return $game;
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

        foreach ($game['players'] as $player) {
            if ($player == Auth::user()->id) {
                return $game;
            }
        }

        return response()->json(['error' => 'Not authorized.'], 403);
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

        broadcast(new GameTurn($game))->toOthers();

        return $game;
    }

    public function join(Request $request, Game $game)
    {
        if ($game['players'][Game::CROSS] == Auth::user()->id
            or $game['players'][Game::ZERO] == Auth::user()->id) {
            return $game;
        }

        if ($game->status !== Game::NEED_PLAYERS) {
            return response('Game already started', 403);
        }

        $sign = null;

        if (empty($game['players'][Game::CROSS])) {
            $sign = Game::CROSS;
        }
        if (empty($game['players'][Game::ZERO])) {
            $sign = Game::ZERO;
        }

        if (!is_null($sign)) {
            $game->update(["players." . $sign => Auth::user()->id]);
            $game->update(["status" => Game::STARTED]);
            broadcast(new GameTurn($game))->toOthers();

            return $game;
        }

        return false;
    }
}
