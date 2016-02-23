<?php

namespace Artesaos\LaravelInstaller\Console;

use Artesaos\LaravelInstaller\Console\Traits\InteractsWithZip;
use ZipArchive;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class NewCommand extends Command
{
    use InteractsWithZip;

    private $server_url = "https://github.com/mauri870/laravel-releases/raw/master/";

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
            ->setHelp(<<<EOT
Craft a new laravel application based on version number

Usage:

<info>laravel new <comment>name</comment> <comment>version</comment></info>

You can specify a version to install or leave blank for the latest stable release

Example:

<info>laravel new <comment>blog</comment> <comment>LTS</comment></info>

You can choose one of this versions to install:
<comment>4.2</comment>
<comment>5.0</comment>
<comment>5.1</comment> - <info>You can use <comment>LTS</comment> instead</info>
<comment>5.2</comment> - <info>Default version</info>
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
        if (! class_exists('ZipArchive')) {
            throw new \RuntimeException('The Zip PHP extension is not installed. Please install it and try again.');
        }

        $this->verifyApplicationDoesntExist(
            $directory = getcwd() . '/' . $input->getArgument('name'),
            $output
        );

        $output->writeln('<info>Getting laravel version...</info>');

        $version = $this->getVersion($input);

        $output->writeln('<info>Set version <comment>' . $version . '</comment>...</info>');

        $output->writeln('<info>Crafting application...</info>');

        $this->download($zipFile = $this->makeFilename(), $version)
            ->extract($zipFile, $directory)
            ->cleanUp($zipFile);

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
            throw new \RuntimeException('Application already exists!');
        }
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
     * Get the version that should be installed.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface $input
     * @return string
     */
    protected function getVersion($input)
    {
        $version = $input->getArgument('version');

        switch($version) {
            case "":
                $version = "5.2";
                break;
            case "LTS":
                $version = "5.1";
                break;
        }

        $available_versions = ['4.2','5.0','5.1','5.2'];

        if(!in_array($version, $available_versions)){
            throw new \RuntimeException("The version you are trying to download is not available!");
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
