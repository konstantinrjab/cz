<?php

namespace App\Http\Controllers;

use App\Events\GameChangeStateEvent;
use App\Entity\Game;
use App\Http\Requests\CreateGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Services\GameRepresentationService;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    /** @var GameService $gameService */
    private $gameService;

    /** @var GameRepresentationService $representationService */
    private $representationService;

    public function __construct(GameService $gameService, GameRepresentationService $representationService)
    {
        $this->gameService = $gameService;
        $this->representationService = $representationService;
    }

    public function index(): JsonResponse
    {
        $gameCollection = $this->gameService->getAbleToConnect();

        return response()->json($this->representationService->getGameCollection($gameCollection));
    }

    public function store(CreateGameRequest $request): JsonResponse
    {
        $game = $this->gameService->create($request->get('name'), $request->get('password'));

        return response()->json($this->representationService->getGame($game));
    }

    public function show(string $gameId): JsonResponse
    {
        $playerId = Auth::user()->getAuthIdentifier();
        $game = $this->gameService->getByIdAndPlayerId($gameId, $playerId);

        return response()->json($this->representationService->getGame($game));
    }

    public function update(UpdateGameRequest $request, Game $game): JsonResponse
    {
        $playerId = Auth::user()->getAuthIdentifier();

        $this->gameService->update($game, $request->get('row'), $request->get('column'), $playerId);

        broadcast(new GameChangeStateEvent($game, $this->representationService))->toOthers();

        return response()->json($this->representationService->getGame($game));
    }

    public function join(Game $game): JsonResponse
    {
        $playerId = Auth::user()->getAuthIdentifier();

        $this->gameService->joinGame($game, $playerId);

        broadcast(new GameChangeStateEvent($game, $this->representationService))->toOthers();

        return response()->json($this->representationService->getGame($game));
    }
}
