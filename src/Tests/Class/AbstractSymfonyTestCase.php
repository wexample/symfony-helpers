<?php

namespace Wexample\SymfonyHelpers\Tests\Class;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Route;
use Wexample\SymfonyHelpers\Helper\FileHelper;
use Wexample\SymfonyHelpers\Helper\RequestHelper;

abstract class AbstractSymfonyTestCase extends AbstractWebTestCase
{
    private ?array $requestsLog = null;
    private ?array $htaccessRules = null;

    public function getStorageDir($name = null): string
    {
        return self::getProjectDir().
            '/var/'.($name ? $name.'/' : '');
    }

    public function getProjectDir(): string
    {
        return self::getContainer()
                ->get('kernel')
                ->getProjectDir().'/';
    }

    protected function getSrcDir(): string
    {
        $projectDir = $this->getProjectDir();

        return realpath($projectDir.'src').'/';
    }

    public function url(
        $route,
        $args = [],
        bool $absolute = false,
        array $parameters = []
    ): string {
        /** @var Router $router */
        $router = self::getContainer()->get(UrlGeneratorInterface::class);

        $url = $router->generate(
                $route,
                $args,
                $absolute
                    ? UrlGeneratorInterface::ABSOLUTE_URL
                    : UrlGeneratorInterface::ABSOLUTE_PATH
            ).RequestHelper::buildQueryStringPartIfNotEmpty($parameters);

        $requestLogPath = $this->getProjectDir().'assets/json/test-requests-log.json';
        if (!$this->requestsLog) {
            $this->requestsLog = FileHelper::createFileIfMissingAndGetJson(
                $requestLogPath,
                associative: true
            );
        }

        $allRoutes = $router->getRouteCollection();
        $requirements = [];
        /**
         * @var string                           $routeName
         * @var Route $compiledRoute
         */
        foreach ($allRoutes as $routeName => $compiledRoute) {
            if ($routeName === $route) {
                $requirements = ($compiledRoute->getRequirements());
            }
        }

        $dummyArgs = [];
        foreach ($args as $name => $value) {
            if (!isset($requirements[$name])) {
                if (is_int($value)) {
                    $value = 123;
                } else {
                    $value = 'XXX';
                }
            }

            $dummyArgs[$name] = $value;
        }
        ksort($dummyArgs);

        $dummyUrl = $router->generate(
            $route,
            $dummyArgs,
            $absolute
                ? UrlGeneratorInterface::ABSOLUTE_URL
                : UrlGeneratorInterface::ABSOLUTE_PATH
        );

        $logEntry = [
            'route' => $route,
            'args' => $dummyArgs,
            'absolute' => $absolute,
        ];

        $key = md5(json_encode($logEntry));

        $logEntry['url'] = $dummyUrl;

        $updatedLog = false;

        if (!isset($this->requestsLog[$key])) {
            $this->requestsLog[$key] = $logEntry;
            $updatedLog = true;

            $this->logWarn(
                'Added URL to requests log.',
            );
        } else {
            $urlSaved = $this->requestsLog[$key]['url'];
            if ($dummyUrl !== $urlSaved) {
                if ($this->hasRedirectionRule(
                    $urlSaved,
                    $dummyUrl
                )) {
                    // Updates log.
                    $this->requestsLog[$key] = $logEntry;
                    $updatedLog = true;

                    $this->logWarn(
                        'Updated URL in requests log.',
                    );
                } else {
                    $this->error(
                        'A previously registered URL has changed and have no redirection in .htaccess file. Expected rule : "'.$this->buildRedirectionRule(
                            $urlSaved,
                            $dummyUrl
                        ).'"'
                    );
                }
            }
        }

        if ($updatedLog) {
            ksort($this->requestsLog[$key]);

            file_put_contents(
                $requestLogPath,
                json_encode(
                    $this->requestsLog,
                    JSON_PRETTY_PRINT
                )
            );
        }

        return $url;
    }

    private function hasRedirectionRule(
        $urlFrom,
        $urlTo
    ): bool {
        if (!$this->htaccessRules) {
            $rules = explode(
                PHP_EOL,
                file_get_contents(
                    $this->getProjectDir().'public/.htaccess'
                )
            );

            // Filter only interesting part.
            foreach ($rules as $rule) {
                $rule = trim($rule);
                if (str_starts_with($rule, 'RedirectMatch')) {
                    $this->htaccessRules[] = $rule;
                }
            }
        }

        foreach ($this->htaccessRules as $rule) {
            if ($this->redirectionRuleMatch($rule, $urlFrom, $urlTo)) {
                return true;
            }
        }

        return false;
    }

    private function redirectionRuleMatch(
        $rule,
        $urlFrom,
        $urlTo
    ): bool {
        $explodeRule = explode(' ', $rule);
        $explodeExpectedRule = explode(' ', $this->buildRedirectionRule($urlFrom, $urlTo));

        // Compare rule but ignore error code.
        return trim($explodeRule[0] ?? '') === $explodeExpectedRule[0]
            && trim($explodeRule[2] ?? '') === $explodeExpectedRule[2];
    }

    private function buildRedirectionRule(
        $urlFrom,
        $urlTo
    ): string {
        return 'RedirectMatch 301 ^'.$urlFrom.'$ '.$urlTo;
    }
}
