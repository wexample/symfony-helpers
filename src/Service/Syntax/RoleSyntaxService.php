<?php

namespace Wexample\SymfonyHelpers\Service\Syntax;

use App\Kernel;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Wexample\SymfonyHelpers\Helper\BundleHelper;
use Wexample\SymfonyHelpers\Helper\ClassHelper;
use Wexample\SymfonyHelpers\Helper\FileHelper;
use Wexample\SymfonyHelpers\Helper\RoleHelper;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

class RoleSyntaxService
{
    private mixed $roles = [];

    public function __construct(
        ParameterBagInterface $parameterBag,
        private readonly ControllerSyntaxService $controllerSyntaxService,
        private readonly Kernel $kernel
    ) {
        $this->roles = RoleHelper::flattenRolesConfig(
            $parameterBag->get('security.role_hierarchy.roles')
        );
    }

    // TODO REMOVE (in rector)
    public function writeRolesClasses()
    {
        foreach ($this->roles as $role) {
            $roleClass = RoleHelper::getRoleNamePartAsClass($role);

            $controllerDirs = [
                '',
                ClassHelper::CLASS_PATH_PART_API,
            ];

            foreach ($controllerDirs as $subDir) {
                FileHelper::forEachValidFile(
                    $this->controllerSyntaxService->buildControllerPath($subDir),
                    function(
                        string $classFilePath
                    ) use
                    (
                        $roleClass,
                        $subDir
                    ) {
                        $classPath = ClassHelper::buildClassNameFromRealPath(
                            $classFilePath,
                            $this->kernel->getProjectDir()
                        );

                        $cousinName = ControllerSyntaxService::COUSIN_TEST_ROLE_INTEGRATION;
                        $this->controllerSyntaxService::getCousinParameters($cousinName);
                        $cousinParams = $this->controllerSyntaxService->getCousinParameters($cousinName);
                        $cousinSuffix = $cousinParams[VariableHelper::SUFFIX];
                        $cousinFolder = $cousinParams[VariableHelper::FOLDER] ?: BundleHelper::FOLDER_SRC;

                        $cousinClassPath = $this
                            ->controllerSyntaxService::buildControllerTestPath(
                                $classPath,
                                $roleClass,
                                $subDir,
                                $cousinSuffix
                            );

                        $this->controllerSyntaxService->writeCousinIfMissing(
                            $classPath,
                            ClassHelper::CLASS_APP_BASE_PATH
                            .ClassHelper::CLASS_PATH_PART_CONTROLLER
                            .ClassHelper::NAMESPACE_SEPARATOR,
                            $cousinClassPath,
                            $cousinSuffix,
                            $cousinFolder,
                            [
                                'roleClass' => $roleClass,
                            ]
                        );
                    }
                );
            }
        }
    }
}
