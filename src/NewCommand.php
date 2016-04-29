<?php

namespace Artesaos\LaravelInstaller\Console;

use Symfony\Component\Process\Exception\ProcessFailedException;
use RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('new')
            ->setDescription('Create a new Laravel application.')
            ->addArgument('name', InputArgument::REQUIRED, "What your application name?")
            ->addArgument('version', InputArgument::OPTIONAL, 'Which version you want to install?')
            ->addOption('interactive', null, InputOption::VALUE_NONE, 'Add packages dynamically to your project')
            ->setHelp(<<<EOT
Craft a new laravel application based on version number

Usage:

<info>laravel new <comment>name</comment> <comment>version</comment></info>

You can specify a version to install or leave blank for the latest stable release

The option <info>--interactive</info> is available. This will ask for packages to require on your project

Example:

<info>laravel new <comment>blog</comment> <comment>LTS</comment></info>

You can choose one of this versions to install:
<comment>4.2</comment>
<comment>5.0</comment>
<comment>5.1</comment> - <info>You can use <comment>LTS</comment> instead</info>
<comment>5.2</comment> - <info>Default version</info>
<comment>master</comment> - <info>Install from the current master branch</info>
<comment>develop</comment> - <info>Install the development version from the next release</info>
EOT
            );
    }

    /**
     * Execute the command.
     *
     * @param  InputInterface $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->verifyApplicationDoesntExist(
            $directory = getcwd() . '/' . $input->getArgument('name'),
            $output
        );

        $output->writeln('<info>Getting laravel version...</info>');

        $version = $this->getVersion($input);

        $output->writeln('<info>Set version <comment>' . $version . '</comment>...</info>');

        $output->writeln('<info>Crafting application...</info>');

        $this->craftApplication($directory, $version, $output);

        $output->writeln('<info>Install dependencies...</info>');

        $this->installDependencies($directory, $output);

        if ($input->getOption('interactive')) {
            $this->requirePackages($directory, $output);
        }

        $output->writeln('<comment>Application ready! Build something amazing.</comment>');
    }

    /**
     * Verify that the application does not already exist.
     *
     * @param  string $directory
     * @return void
     */
    protected function verifyApplicationDoesntExist($directory, OutputInterface $output)
    {
        if (is_dir($directory)) {
            throw new RuntimeException('Application already exists!');
        }
    }


    /**
     * Craft a new application
     *
     * @param $directory
     * @param $version
     * @param $output
     * @return $this
     */
    protected function craftApplication($directory, $version, OutputInterface $output)
    {
        $composer = $this->findComposer();

        $installationCommand = $this->getInstallationCommand($version, $directory);

        $install = new Process($installationCommand, dirname($directory), null, null, null);
        $install->run();

        return $this;
    }

    public function installDependencies($directory, OutputInterface $output)
    {
        $composer = $this->findComposer();
        $commands = [
            $composer . ' install --no-scripts',
            $composer . ' run-script post-root-package-install',
            $composer . ' run-script post-install-cmd',
            $composer . ' run-script post-create-project-cmd'
        ];

        $process = new Process(implode(' && ', $commands), $directory, null, null, null);
        return $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });
    }


    /**
     * Get composer installation command
     *
     * @param $version
     * @param $directory
     * @return string
     */
    protected function getInstallationCommand($version, $directory)
    {
        $composer = $this->findComposer();

        $command = $composer . " create-project laravel/laravel --prefer-dist " . $directory;

        switch ($version) {
            case "4.2":
                return $command . " 4.2";
                break;
            case "5.0":
                return $command . " \"~5.0.0\"";
                break;
            case "5.1":
            case "LTS":
                return $command . " \"5.1.*\"";
                break;
            case "5.2":
                return $command;
                break;
            case "develop":
                return $command . " --stability=dev \"dev-develop\"";
                break;
            case "master":
                return $command . " --stability=dev \"dev-master\"";
                break;
        }

        throw new RuntimeException("The version " . $version . " doesn't exist!");
    }

    /**
     * Get the output of a Symfony Process
     * @param Process $process
     */
    protected function getProccessOutput(Process $process)
    {
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    /**
     * Require packages recursively
     *
     * @param $directory
     * @param $output
     */
    public function requirePackages($directory, $output)
    {
        $output->writeln('<info>Require additional packages...</info>');
        $output->writeln('<info>Press enter for skip</info>');
        system('cd '.$directory.' && '.$this->findComposer().' require');
    }

    /**
     * Get the version that should be installed.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface $input
     * @return string
     */
    protected function getVersion($input)
    {
        $version = $input->getArgument('version');

        if ($version == "5.2" || $version == "") {
            $version = "5.2";
        }

        return $version;
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if (file_exists(getcwd() . '/composer.phar')) {
            return '"' . PHP_BINARY . '" composer.phar';
        }

        return 'composer';
    }
}