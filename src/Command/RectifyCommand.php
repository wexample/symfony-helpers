<?php

namespace Wexample\SymfonyHelpers\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Wexample\SymfonyHelpers\Service\RectifyService;

class RectifyCommand extends AbstractCommand
{
    protected static $defaultDescription = 'Validate entities declared with #[RectifiableEntity].';

    public function __construct(
        private readonly RectifyService $rectifyService,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        return $this->executeAndCatchErrors(
            $input,
            $output,
            fn (InputInterface $input, OutputInterface $output, SymfonyStyle $io): int => $this->executeRectify($io)
        );
    }

    private function executeRectify(
        SymfonyStyle $io
    ): int {
        $violations = $this->rectifyService->validateRectifiableEntities();

        if ($violations !== []) {
            $io->error('Rectification checks failed.');
            foreach ($violations as $violation) {
                $io->writeln('- '.$violation);
            }

            return Command::FAILURE;
        }

        $io->success('All RectifiableEntity checks passed.');

        return Command::SUCCESS;
    }
}
