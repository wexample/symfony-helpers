<?php

namespace Wexample\SymfonyHelpers\Tests\Class\Traits;

use Symfony\Component\HttpFoundation\Response;
use Wexample\SymfonyHelpers\Controller\AbstractController;

trait ControllerTestCaseTrait
{
    use HtmlDocumentTestCaseTrait;

    protected function goToControllerRouteAndCheckHtml(
        string $routeName,
        array $routeParameters = [],
        int $expectedResponseCode = Response::HTTP_OK
    ): void {
        $this->goToRouteAndCheckHtml(
            $this->buildControllerRoute($routeName),
            $routeParameters,
            $expectedResponseCode
        );
    }

    public static function buildControllerRoute(string $routeName): string
    {
        /** @var AbstractController $controllerClass */
        $controllerClass = static::getControllerClass();

        return $controllerClass::buildRouteName($routeName);
    }

    abstract public static function getControllerClass(): string;

    protected function postToControllerRouteAndCheckNotAllowed(
        string $routeName,
        array $routeParameters = [],
        array $payload = []
    ):void {
        $this->postToRouteAndCheckNotAllowed(
            $this->buildControllerRoute(
                $routeName
            ),
            $routeParameters,
            $payload
        );
    }

    protected function goToControllerHtmlRouteAndCheckForbiddenOrNeedsAuth(
        string $routeName,
        array $routeParameters = []
    ): void {
        $this->goToControllerRoute(
            $routeName,
            $routeParameters,
        );

        $this->assertResponseIsForbiddenOrRedirectsToLoginPage();
    }

    protected function goToControllerRoute(
        string $routeName,
        array $routeParameters = [],
        array $requestParameters = []
    ): void {
        $this->goToRoute(
            $this->buildControllerRoute($routeName),
            $routeParameters,
            $requestParameters
        );
    }

    protected function goToControllerRouteAndCheckStatusCode(
        string $routeName,
        array $routeParameters = [],
        int $expectedResponseCode = Response::HTTP_OK,
        array $requestParameters = []
    ): void {
        $this->goToRouteAndCheckStatusCode(
            $this->buildControllerRoute($routeName),
            $routeParameters,
            $expectedResponseCode,
            $requestParameters
        );
    }

    protected function assertControllerRouteSame(
        string $routeName,
        array $parameters = [],
        string $message = null
    ): void {
        $this->assertRouteSame(
            $this->buildControllerRoute($routeName),
            $parameters,
            $message ?? 'Route same'
        );
    }
}
