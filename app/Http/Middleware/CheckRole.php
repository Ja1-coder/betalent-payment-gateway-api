<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CheckRole
 *
 * Middleware responsible for Role-Based Access Control (RBAC).
 * It intercepts requests and verifies if the authenticated user's role 
 * matches the required permissions defined in the route.
 */
class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * This method translates string-based roles from the route definition 
     * into their corresponding integer values stored in the database, 
     * ensuring a decoupled and flexible authorization check.
     *
     * @param  Request  $request The incoming HTTP request.
     * @param  Closure  $next The next middleware or controller in the pipeline.
     * @param  string  ...$roles Variadic list of allowed roles (e.g., 'admin', 'finance').
     * @return Response Returns 401 if unauthenticated, 403 if unauthorized, or proceeds to next.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Usuário não identificado.'
            ], 401);
        }

        /** * Map string roles to their respective integer constants defined in the User Model.
         * This allows the route to use readable names while the check remains type-safe.
         */
        $allowedValues = array_map(fn($role) => User::getRoleValue($role), $roles);

        if (!in_array($user->role, $allowedValues)) {
            return response()->json([
                'message' => 'Acesso negado. Você não tem permissão para acessar este recurso.'
            ], 403);
        }

        return $next($request);
    }
}