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
    /**
     * The user repository of the provider.
     * 
     * @var Mytdt\Auth0\Lumen\Contracts\UserRepositoryContract
     */
    protected $userRepository;

    /**
     * Create a new Auth0DingoProvider instance.
     * 
     * @param Mytdt\Auth0\Lumen\Contracts\UserRepositoryContract $userRepository
     */
    public function __construct(UserRepositoryContract $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Authenticate the request and return the authenticated user instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Dingo\Api\Routing\Route $route
     *
     * @throws BadRequestHttpException   if no token is provided.
     * @throws UnauthorizedHttpException if invalid token or unauthorized user.
     * 
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
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
        }

        // if it does not represent a valid user, return a HTTP 401
        $user = $this->userRepository->getUserByDecodedJWT($token);
        if (!$user) {
            throw new UnauthorizedHttpException('Auth0', 'Unauthorized user.');
        }

        return $user;
    }

    /**
     * Get the providers authorization method.
     *
     * @return string
     */
    public function getAuthorizationMethod()
    {
        return 'bearer';
    }
}
