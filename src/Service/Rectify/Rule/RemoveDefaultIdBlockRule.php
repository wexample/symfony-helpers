<?php

namespace Wexample\SymfonyHelpers\Service\Rectify\Rule;

use ReflectionClass;

class RemoveDefaultIdBlockRule extends AbstractRectifyRule
{
    /**
     * @return string[]
     */
    public function apply(
        ReflectionClass $entityReflection
    ): array {
        $filePath = $entityReflection->getFileName();
        $content = file_get_contents($filePath);
        $className = $entityReflection->getShortName();

        $pattern = '/(class\s+'.preg_quote($className, '/').'\b[^{]*\{)(?<body>[\s\S]*?)(^\})/m';
        if (! preg_match($pattern, $content, $matches)) {
            return [];
        }

        $normalizedBody = preg_replace('/\s+/', '', $matches['body']);
        $normalizedSymfonyDefaultIdBlock = preg_replace('/\s+/', '', <<<'PHP'
#[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column]
private ?int $id = null;

public function getId(): ?int
{
    return $this->id;
}
PHP);

        if ($normalizedBody !== $normalizedSymfonyDefaultIdBlock) {
            return [];
        }

        $content = preg_replace(
            $pattern,
            '$1'."\n".'$3',
            $content,
            1
        );

        file_put_contents($filePath, $content);

        return [];
    }
}
