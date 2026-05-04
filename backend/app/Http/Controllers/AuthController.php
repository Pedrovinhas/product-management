<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->string('email')->value(),
            $request->string('password')->value(),
        );

        return response()->json([
            'data' => [
                'token' => $result['token'],
                'user' => new UserResource($result['user']),
            ],
            'message' => 'Login realizado com sucesso.',
            'errors' => [],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->authService->logout($user);

        return response()->json([
            'data' => null,
            'message' => 'Sessão encerrada com sucesso.',
            'errors' => [],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'data' => new UserResource($user),
            'message' => 'Usuário recuperado com sucesso.',
            'errors' => [],
        ]);
    }
}
