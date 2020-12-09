<?php

namespace SimpleBus\CI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RebuildSymfonyRequirementsCommand extends Command
{
    private const PACKAGE = 'package';
    private const VERSION = 'version';
    private const REPLACE_PACKAGES = [
        'symfony/config',
        'symfony/console',
        'symfony/dependency-injection',
        'symfony/finder',
        'symfony/framework-bundle',
        'symfony/http-kernel',
        'symfony/monolog-bridge',
        'symfony/process',
        'symfony/proxy-manager-bridge',
        'symfony/yaml',
        'symfony/routing',
    ];

    protected function configure()
    {
        $this->setName('rebuild-symfony-requirements');
        $this->addArgument(
            self::PACKAGE,
            InputArgument::REQUIRED
        );
        $this->addArgument(
            self::VERSION,
            InputArgument::REQUIRED
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $package = $input->getArgument(self::PACKAGE);
        $nieuweVersie = $input->getArgument(self::VERSION);
        $path = __DIR__ . '/../../packages/' . $package . '/composer.json';

        $content = json_decode(
            file_get_contents(
                $path
            ),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $content['require'] = $this->replace($content['require'], $nieuweVersie);
        $content['require-dev'] = $this->replace($content['require-dev'], $nieuweVersie);

        file_put_contents(
            $path,
            json_encode(
                $content,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            ) . PHP_EOL
        );

        return self::SUCCESS;
    }

    private function replace(array $require, string $nieuweVersie): array
    {
        foreach ($require as $package => $version) {
            if (in_array($package, self::REPLACE_PACKAGES, true)) {
                $require[$package] = $nieuweVersie;
            }
        }

        return $require;
    }
}
