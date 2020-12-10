<?php

namespace SimpleBus\CI\Command;

use Composer\Semver\Constraint\ConstraintInterface;
use Composer\Semver\Constraint\MultiConstraint;
use Composer\Semver\VersionParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RebuildSymfonyRequirementsCommand extends Command
{
    private const PACKAGE = 'package';
    private const VERSION = 'version';
    private const IGNORED_PACKAGES = ['symfony/monolog-bundle'];

    /**
     * @var VersionParser
     */
    private $versionParser;

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->versionParser = new VersionParser();
    }

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
        $newVersion = $input->getArgument(self::VERSION);
        $path = __DIR__ . '/../../packages/' . $package . '/composer.json';

        $content = json_decode(
            file_get_contents($path),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $content['require'] = $this->replace($content['require'], $newVersion);
        $content['require-dev'] = $this->replace($content['require-dev'], $newVersion);

        file_put_contents(
            $path,
            json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
        );

        return self::SUCCESS;
    }

    private function replace(array $require, string $newVersion): array
    {
        $newVersionConstraint = $this->versionParser->parseConstraints($newVersion);

        foreach ($require as $package => $version) {
            if (strpos($package, 'symfony/') !== 0) {
                continue;
            }

            if (in_array($package, self::IGNORED_PACKAGES, true)) {
                continue;
            }

            $constraints = $this->versionParser->parseConstraints($version);
            $newConstraint = $this->matches($constraints, $newVersionConstraint);

            if ($newConstraint === false) {
                throw new \LogicException(sprintf('Cannot match %s with %s', $version, $newVersion));
            }

            $newVersion = sprintf('^%s', $newConstraint->getLowerBound()->getVersion());

            echo sprintf("Change %s \"%s\" to \"%s\"\n", $package, $version, $newVersion);

            $require[$package] = $newVersion;
        }

        return $require;
    }

    /**
     * @param MultiConstraint     $multi
     * @param ConstraintInterface $provider
     *
     * @return ConstraintInterface|false
     */
    public function matches(MultiConstraint $multi, ConstraintInterface $provider)
    {
        if (false === $multi->isConjunctive()) {
            foreach ($multi->getConstraints() as $constraint) {
                if ($provider->matches($constraint)) {
                    return $constraint;
                }
            }

            return false;
        }

        foreach ($multi->getConstraints() as $constraint) {
            if (!$provider->matches($constraint)) {
                return false;
            }
        }

        return $provider;
    }
}
