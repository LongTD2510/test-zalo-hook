<?php

namespace App\Http\Middleware;

use App\Enums\UserStatusEnum;
use App\Services\SenPrintsAuth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
class CheckAuthAccess
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();
            return $next($request);
        } catch (TokenExpiredException $e) {
            return \response()->json([
                'success' => false,
                'error' => 'token_expired'
            ], 401);
        } catch (TokenInvalidException $e) {
            return \response()->json([
                'success' => false,
                'error' => 'token_invalid'
            ], 401);
        } catch (JWTException $e) {
            return \response()->json([
                'success' => false,
                'error' => 'token_absent'
            ], 406);
        }
    }
}
