<?php

namespace Wexample\SymfonyHelpers\Tests\Traits\TestCase\Application;

use App\Controller\SecurityController;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wexample\SymfonyHelpers\Helper\RequestHelper;
use Wexample\SymfonyHelpers\Helper\TextHelper;

trait IntegrationTestCaseTrait
{
    protected ?KernelBrowser $client = null;

    protected ?string $pathPrevious = null;

    protected bool $hasRequested = false;

    public function getCurrentPath(): string
    {
        return $this->getCurrentRequest()->getPathInfo();
    }

    public function getCurrentRequest(): ?Request
    {
        if (!$this->hasRequested) {
            return null;
        }

        return $this->client->getRequest();
    }

    public function assertStatusCodeInternalServerError(): void
    {
        $this->assertStatusCodeEquals(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->logSecondary('Status code is a server error : '.Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function assertStatusCodeTemporaryRedirect(): void
    {
        $this->assertStatusCodeEquals(Response::HTTP_FOUND);
        $this->logSecondary('Status code is a temporary redirection : '.Response::HTTP_FOUND);
    }

    public function assertStatusCodePermanentRedirect(): void
    {
        $this->assertStatusCodeEquals(Response::HTTP_MOVED_PERMANENTLY);
        $this->logSecondary('Status code is a permanent redirection : '.Response::HTTP_MOVED_PERMANENTLY);
    }

    public function assertStatusCodeNotFound(): void
    {
        $this->assertStatusCodeEquals(Response::HTTP_NOT_FOUND);
        $this->logSecondary('Status code is not found : '.Response::HTTP_NOT_FOUND);
    }

    public function assertStatusCodeForbidden(): void
    {
        $this->assertStatusCodeEquals(Response::HTTP_FORBIDDEN);
        $this->logSecondary('Status code is forbidden : '.Response::HTTP_FORBIDDEN);
    }

    public function assertStatusCodeIsNotError(string $message = null): void
    {
        $this->assertNotContains(
            $this->client->getResponse()->getStatusCode(),
            [
                Response::HTTP_INTERNAL_SERVER_ERROR,
                Response::HTTP_NOT_IMPLEMENTED,
                Response::HTTP_BAD_GATEWAY,
                Response::HTTP_SERVICE_UNAVAILABLE,
                Response::HTTP_GATEWAY_TIMEOUT,
                Response::HTTP_VERSION_NOT_SUPPORTED,
                Response::HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL,
                Response::HTTP_INSUFFICIENT_STORAGE,
                Response::HTTP_LOOP_DETECTED,
                Response::HTTP_NOT_EXTENDED,
                Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED,
            ],
            $message ?: 'Status code is not an error code.'
        );
    }

    public function assertResponseIsForbiddenOrRedirectsToLoginPage(): void
    {
        $this->logIndentUp();

        // Anonymous are redirected to home.
        if (!isset($this->user)) {
            $this->assertRedirectionIsToLoginPage();
        } // Users are forbidden.
        else {
            $this->assertStatusCodeEquals(
                Response::HTTP_FORBIDDEN
            );
        }

        $this->logIndentDown();
    }

    public function assertRedirectionIsToLoginPage(): void
    {
        $this->assertRedirectionIsToTargetUrl(
            $this->url(
                SecurityController::buildRouteName(
                    SecurityController::ROUTE_LOGIN
                ),
            )
        );
    }

    public function assertRedirectionIsToTargetUrl(string $url): void
    {
        $this->assertIsRedirection();

        /** @var RedirectResponse $response */
        $response = $this->client->getResponse();

        $targetUrl = $response->getTargetUrl();

        $this->assertEquals(
            $targetUrl,
            $url,
            'Target redirected url matches expected.'
        );

        $this->logSecondary(
            'As expected, url has been redirected to '.$url
        );
    }

    public function assertIsRedirection(): void
    {
        /** @var RedirectResponse $response */
        $response = $this->client->getResponse();

        $this->assertTrue(
            RedirectResponse::class === $response::class,
            'Response is a redirection.'
        );
    }

    public function postToRouteAndCheckNotAllowed(
        string $route,
        array $routeParameters = [],
        array $content = []
    ): void {
        $this->postToRoute(
            $route,
            $routeParameters,
            $content
        );

        $this->assertStatusCodeMethodNotAllowed();
    }

    public function postToRoute(
        string $route,
        array $routeParameters = [],
        array $content = []
    ): void {
        $this->post(
            $this->url($route, $routeParameters),
            $content
        );
    }

    protected function logNavigation(string $message): void
    {
        $this->log(
            $message,
            TextHelper::ASCII_COLOR_BLUE
        );
    }

    public function post(
        string $url,
        array $content = []
    ): void {
        $this->logNavigation(
            'POST '.$url,
        );

        $this->client->request(
            Request::METHOD_POST,
            $url,
            content: json_encode($content)
        );
    }

    public function assertStatusCodeMethodNotAllowed(): void
    {
        $this->assertStatusCodeEquals(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->logSecondary('Status code is method not found : '.Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function content(): string
    {
        return $this->client->getResponse()->getContent();
    }

    public function getResponseCode(): int
    {
        return $this->client->getResponse()->getStatusCode();
    }

    public function followRedirectAndCheckTargetPage(): void
    {
        while ($this->client->getResponse()->getStatusCode() === Response::HTTP_FOUND) {
            $this->logNavigation(
                'Redirecting to : '
                .$this->client->getResponse()->headers->get('location')
            );
            $this->client->followRedirect();
        }

        $this->assertStatusCodeOk();
        $this->assertPageBodyHasNotOrphanTranslationKey();
    }

    public function assertStatusCodeOk(): void
    {
        $this->assertStatusCodeEquals(Response::HTTP_OK);
        $this->logSecondary('Status code is OK : '.Response::HTTP_OK);
    }

    protected function createGlobalClient(bool $forceRecreate = true): void
    {
        if ($this->client && !$forceRecreate) {
            return;
        }

        $this->hasRequested = false;
        static::ensureKernelShutdown();
        $this->client = static::createClient();
    }

    protected function goToRouteAndCheckHtml(
        string $routeName,
        array $routeParameters = [],
        int $expectedResponseCode = Response::HTTP_OK
    ): void {
        $this->goToRouteAndCheckStatusCode(
            $routeName,
            $routeParameters,
            $expectedResponseCode
        );

        if (Response::HTTP_OK === $expectedResponseCode) {
            $this->assertPageBodyHasNotOrphanTranslationKey();
        }
    }

    protected function goToRouteAndCheckStatusCode(
        string $routeName,
        array $routeParameters = [],
        int $expectedResponseCode = Response::HTTP_OK,
        array $requestParameters = []
    ): Crawler {
        $crawler = $this->goToRoute(
            $routeName,
            $routeParameters,
            $requestParameters
        );

        $this->assertStatusCodeEquals($expectedResponseCode);

        return $crawler;
    }

    public function goToRoute(
        string $route,
        array $routeParameters = [],
        array $parameters = [],
    ): Crawler {
        $path = $this->url($route, $routeParameters);

        if (isset($parameters['query'])) {
            $path .= '?'.RequestHelper::buildQueryString(
                    $parameters['query']
                );
        }

        $this->logSecondary(
            'Convert route `'.$route.'` with '.
            json_encode($routeParameters)
        );

        return $this->go(
            $path,
            $parameters
        );
    }

    public function go(
        string $path,
        array $parameters = [],
    ): Crawler {
        $this->logNavigation(
            'GET '.$path,
        );

        // Store previous request for further usage.
        if ($request = $this->getCurrentRequest()) {
            $this->pathPrevious = $request->getUri();
        }

        $this->requestGet(
            $path,
            $parameters
        );

        return $this->getCurrentCrawler();
    }

    public function requestGet(
        string $path,
        array $parameters = []
    ): ?Crawler {
        $this->hasRequested = true;

        return $this->client->request(
            Request::METHOD_GET,
            $path,
            $parameters['parameters'] ?? [],
            $parameters['files'] ?? [],
            $parameters['server'] ?? [],
        );
    }

    public function getCurrentCrawler(): ?Crawler
    {
        return $this->client?->getCrawler();
    }

    public function assertStatusCodeEquals(int $expectedResponseCode): void
    {
        $actual = $this->client->getResponse()->getStatusCode();

        if ($expectedResponseCode !== $actual) {
            $this->logBodyExtract();
        }

        // We don't use assertResponseStatusCodeSame(),
        // as logged trace is far too long.
        $this->assertEquals(
            $expectedResponseCode,
            $actual,
        );

        $this->logSecondary(
            'Status code is '.$expectedResponseCode
        );
    }

    public function logBodyExtract(
        int $indent = null
    ): void {
        $this->logSecondary(
            substr($this->getBody(), 0, 100),
            $indent
        );
    }

    public function debugContent(Crawler $crawler = null): void
    {
        if (!$crawler) {
            $crawler = $this->getCurrentCrawler();
        }

        if (!$crawler) {
            $this->error('No crawler found in debug method !');
        }

        $body = $crawler->filter('body');

        $output = $body ? $body->html() : $this->content();

        echo PHP_EOL, '++++++++++++++++++++++++++',
        PHP_EOL, ' PATH :'.$this->client->getRequest()->getPathInfo(),
        PHP_EOL, ' CODE :'.$this->client->getResponse()->getStatusCode(),
        PHP_EOL;

        $exceptionMessagePosition = strpos($output, 'exception_message');
        $outputSuite = substr($output, $exceptionMessagePosition);
        if (false !== $exceptionMessagePosition) {
            preg_match(
                '/(?:exception_message">)([^<]*)(?:<\/span>)/',
                $outputSuite,
                $matches
            );
            echo ' Exception message : ', $matches[1];
            preg_match(
                '/<div class="block">.*?<\/div>/s',
                $outputSuite,
                $matches
            );

            echo PHP_EOL, ' Stack trace : ', PHP_EOL, $matches[0];
        } else {
            echo $output;
        }

        echo PHP_EOL, '++++++++++++++++++++++++++';
    }
}
