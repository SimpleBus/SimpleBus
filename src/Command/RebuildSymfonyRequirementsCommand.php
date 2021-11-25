<?php

namespace SimpleBus\CI\Command;

use Composer\Semver\Constraint\ConstraintInterface;
use Composer\Semver\Constraint\MultiConstraint;
use Composer\Semver\VersionParser;
use LogicException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class RebuildSymfonyRequirementsCommand extends Command
{
    private const SYMFONY_VERSIONS = 'https://raw.githubusercontent.com/symfony/recipes/master/.github/versions.json';
    private const PACKAGE = 'package';
    private const VERSION = 'version';
    private const DRY_RUN = 'dry-run';

    private VersionParser $versionParser;

    /**
     * @var array<string, array<int, string>>
     */
    private array $symfonySplitPackages;

    public function __construct()
    {
        parent::__construct();

        $this->versionParser = new VersionParser();
    }

    protected function configure(): void
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
        $this->addOption(
            self::DRY_RUN,
            null,
            InputOption::VALUE_NONE
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $package = $input->getArgument(self::PACKAGE);
        $newVersion = $input->getArgument(self::VERSION);
        $dryRun = $input->getOption(self::DRY_RUN);

        if (!is_string($package)) {
            throw new LogicException('Package argument should be a string');
        }

        if (!is_string($newVersion)) {
            throw new LogicException('Version argument should be a string');
        }

        $path = __DIR__.'/../../packages/'.$package.'/composer.json';

        if (false === $content = file_get_contents($path)) {
            throw new LogicException('composer.json content could not be read');
        }

        $content = json_decode(
            $content,
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (!$this->fetchSymfonySplitPackages($output)) {
            return self::FAILURE;
        }

        $content['require'] = $this->replace($output, $content['require'], $newVersion);
        $content['require-dev'] = $this->replace($output, $content['require-dev'], $newVersion);

        $content['conflict'] = $this->getSymfonyConflicts($output, $content['conflict'] ?? [], $newVersion);

        $json = json_encode($content, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($dryRun) {
            $output->writeln($json);

            return self::SUCCESS;
        }

        file_put_contents($path, $json.PHP_EOL);

        return self::SUCCESS;
    }

    private function fetchSymfonySplitPackages(OutputInterface $output): bool
    {
        $json = file_get_contents(self::SYMFONY_VERSIONS);
        if (false === $json) {
            $output->writeln(sprintf('Could not download Symfony versions from %s', self::SYMFONY_VERSIONS));

            return false;
        }

        $this->symfonySplitPackages = json_decode($json, true, 512, JSON_THROW_ON_ERROR)['splits'];

        return true;
    }

    /**
     * @param array<string, string> $conflicts
     *
     * @return array<string, string>
     */
    private function getSymfonyConflicts(OutputInterface $output, array $conflicts, string $newVersion): array
    {
        $newVersionConstraint = $this->versionParser->parseConstraints($newVersion);
        $conflict = sprintf(
            '<%s || >=%s',
            $this->normalizeVersion($newVersionConstraint->getLowerBound()->getVersion()),
            $this->normalizeVersion($newVersionConstraint->getUpperBound()->getVersion())
        );

        foreach ($this->symfonySplitPackages as $packageName => $versions) {
            foreach ($versions as $version) {
                $constraints = $this->versionParser->parseConstraints($version);
                if (!$constraints->matches($newVersionConstraint)) {
                    continue;
                }

                if (isset($conflicts[$packageName])) {
                    $output->writeln(sprintf('There is already a conflict rule for package %s', $packageName));

                    continue;
                }

                $conflicts[$packageName] = $conflict;
            }
        }

        $output->writeln(sprintf('Conflict with Symfony split packages "%s"', $conflict));

        return $conflicts;
    }

    private function normalizeVersion(string $version): string
    {
        return str_replace('.0-dev', '', $version);
    }

    /**
     * @param array<string, string> $require
     *
     * @return array<string, string>
     */
    private function replace(OutputInterface $output, array $require, string $newVersion): array
    {
        $newVersionConstraint = $this->versionParser->parseConstraints($newVersion);

        foreach ($require as $package => $version) {
            if (0 !== strpos($package, 'symfony/')) {
                continue;
            }

            if (!array_key_exists($package, $this->symfonySplitPackages)) {
                continue;
            }

            $constraints = $this->versionParser->parseConstraints($version);
            $newConstraint = $this->matches($constraints, $newVersionConstraint, $version, $newVersion);
            $newVersion = sprintf('^%s', $this->normalizeVersion($newConstraint->getLowerBound()->getVersion()));

            $output->writeln(sprintf('Change %s "%s" to "%s"', $package, $version, $newVersion));

            $require[$package] = $newVersion;
        }

        return $require;
    }

    private function matches(
        ConstraintInterface $multi,
        ConstraintInterface $provider,
        string $version,
        string $newVersion
    ): ConstraintInterface {
        if (!$multi instanceof MultiConstraint) {
            throw new LogicException(sprintf('The constraint is not a %s', MultiConstraint::class));
        }

        if (false === $multi->isConjunctive()) {
            foreach ($multi->getConstraints() as $constraint) {
                if ($provider->matches($constraint)) {
                    return $constraint;
                }
            }

            throw new LogicException(sprintf('Cannot match %s with %s', $version, $newVersion));
        }

        foreach ($multi->getConstraints() as $constraint) {
            if (!$provider->matches($constraint)) {
                throw new LogicException(sprintf('Cannot match %s with %s', $version, $newVersion));
            }
        }

        return $provider;
    }
}
