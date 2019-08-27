<?php

namespace App\Http\Controllers;

use App\Events\GameTurn;
use App\Entity\Game;
use App\Http\Collections\GameCollection;
use App\Http\Requests\CreateGameRequest;
use App\Http\Services\GameService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        $playerId = Auth::user()->getAuthIdentifier();

        $this->gameService->update($request, $game, $playerId);

        broadcast(new GameTurn($game))->toOthers();

        return $game;
    }

    public function join(Game $game): Game
    {
        $playerId = Auth::user()->getAuthIdentifier();

        if ($this->gameService->isAbleToJoinGame($game)) {
            return response()->json('Unable to join game', Response::HTTP_FORBIDDEN);
        }

        if ($this->gameService->alreadyJoined($game, $playerId)) {
            return $game;
        }

        $sign = $this->gameService->createSign($game);

        $game->update(["players.".$sign => $playerId]);
        $game->update(["status" => Game::STARTED]);
        broadcast(new GameTurn($game))->toOthers();

        return $game;
    }
}
