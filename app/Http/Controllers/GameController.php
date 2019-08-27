<?php

namespace App\Http\Controllers;

use App\Events\GameTurn;
use App\Entity\Game;
use App\Http\Collections\GameCollection;
use App\Http\Requests\CreateGameRequest;
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

    public function store(CreateGameRequest $request): Game
    {
        $game = $this->gameService->createGame($request->get('name'), $request->get('password'));

        return $game;
    }

    public function show(string $gameId): Game
    {
        $playerId = Auth::user()->getAuthIdentifier();
        $game = $this->gameService->getByPlayerId($gameId, $playerId);
        if (!$game) {
            throw new NotFoundHttpException();
        }

        return $game;
    }

    public function update(Request $request, Game $game): Game
    {
        $userId = Auth::user()->getAuthIdentifier();

        $this->gameService->update($request, $game, $userId);

        broadcast(new GameTurn($game))->toOthers();

        return $game;
    }

    public function join(Game $game): Game
    {
        $userId = Auth::user()->getAuthIdentifier();

        if ($game['players'][Game::CROSS] == $userId || $game['players'][Game::ZERO] == $userId) {
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
            $game->update(["players.".$sign => $userId]);
            $game->update(["status" => Game::STARTED]);
            broadcast(new GameTurn($game))->toOthers();

            return $game;
        }

        throw new \Exception();
    }
}
