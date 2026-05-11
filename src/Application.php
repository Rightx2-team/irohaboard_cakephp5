<?php
declare(strict_types=1);

namespace App;

use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\Routing\Router;
use Psr\Http\Message\ServerRequestInterface;

class Application extends BaseApplication implements AuthenticationServiceProviderInterface
{
    public function bootstrap(): void
    {
        parent::bootstrap();
        Router::reload();
    }

    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $csrf = new CsrfProtectionMiddleware(['httponly' => true]);
        $csrf->skipCheckCallback(function ($request) {
            $path = $request->getUri()->getPath();
            return str_contains($path, '/install')
                || str_contains($path, '/update')
                || str_contains($path, '/admin/contents/upload')
                || str_contains($path, '/admin/contents/order')
                || str_contains($path, '/admin/courses/order')
                || str_starts_with($path, '/records/add')
                || str_starts_with($path, '/cakephp5_app/records/add');
        });

        $middlewareQueue
            ->add(new ErrorHandlerMiddleware(Configure::read('Error') ?? []))
            ->add(new AssetMiddleware(['cacheTime' => Configure::read('Asset.cacheTime')]))
            ->add(new RoutingMiddleware($this))
            ->add(new BodyParserMiddleware())
            ->add($csrf)
            ->add(new AuthenticationMiddleware($this));
        return $middlewareQueue;
    }

    public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
    {
        $path = $request->getUri()->getPath();
        $isAdmin = str_contains($path, '/admin');

        $service = new AuthenticationService([
            'unauthenticatedRedirect' => $isAdmin ? '/admin/users/login' : '/users/login',
            'queryParam' => 'redirect',
        ]);

        $service->loadAuthenticator('Authentication.Session');
        $service->loadAuthenticator('Authentication.Form', [
            'fields' => [
                'username' => 'username',
                'password' => 'password',
            ],
            'loginUrl' => '%^(/cakephp5_app)?/(admin/)?users/login$%',
            'urlChecker' => [
                'className' => 'Authentication.String',
                'useRegex' => true,
            ],
            // Pass LdapIdentifier directly to FormAuthenticator
            // FormAuthenticatorにLdapIdentifierを直接渡す
            'identifier' => [
                'className' => \App\Identifier\LdapIdentifier::class,
                'fields' => [
                    'username' => 'username',
                    'password' => 'password',
                ],
                'userModel' => 'Users',
            ],
        ]);

        return $service;
    }

    public function services(ContainerInterface $container): void
    {
    }
}