<?php

namespace Mytdt\Auth0\Lumen\Providers;

use Auth0\SDK\Auth0JWT;
use Auth0\SDK\Exception\CoreException;
use Dingo\Api\Auth\Provider\Authorization;
use Dingo\Api\Routing\Route;
use Illuminate\Http\Request;
use Mytdt\Auth0\Lumen\Contracts\UserRepositoryContract;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Auth0DingoProvider extends Authorization
{
    protected $userRepository;

    public function __construct(UserRepositoryContract $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function authenticate(Request $request, Route $route)
    {
        $this->validateAuthorizationHeader($request);

        $authorizationHeader = $request->header('Authorization');
        $encToken = str_replace('Bearer ', '', $authorizationHeader);

        if (trim($encToken) == '') {
            throw new BadRequestHttpException('Auth0', 'Unable to authenticate with no token.');
        }

        $clientId = config('auth0.clientId');
        $clientSecret = config('auth0.clientSecret');
        $token = null;

        try {
            $token = Auth0JWT::decode($encToken, $clientId, $clientSecret);
        } catch (CoreException $e) {
            throw new UnauthorizedHttpException('Auth0', 'Unable to authenticate with invalid token.');
        } catch (Exception $e) {
            throw new UnauthorizedHttpException('Auth0', $e->getMessage(), $e);
        }

        // if it does not represent a valid user, return a HTTP 401
        try {
            $user = $this->userRepository->getUserByDecodedJWT($token);
        } catch (Exception $e) {
            throw new UnauthorizedHttpException('Auth0', 'User credentials not found.');
        }

        if (!$user) {
            throw new UnauthorizedHttpException('Auth0', 'Unauthorized user.');
        }

        //$auth0_id = str_replace('auth0|', '', $user->sub);
        //$user = \App\User::where(['auth0_id' => $auth0_id])->firstOrFail();

        return $user;
    }

    public function getAuthorizationMethod()
    {
        return 'bearer';
    }
}
