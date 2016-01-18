<?php

namespace Mauri870\LaravelInstaller\Console;

use ZipArchive;
use RuntimeException;
use GuzzleHttp\Client;
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
            ->addArgument('name', InputArgument::REQUIRED,"What your application name?")
            ->addArgument('version', InputArgument::OPTIONAL, 'Which version you want to install?');
    }

    /**
     * Execute the command.
     *
     * @param  InputInterface  $input
     * @param  OutputInterface  $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->verifyApplicationDoesntExist(
            $directory = getcwd().'/'.$input->getArgument('name'),
            $output
        );

        $output->writeln('<info>Getting laravel version...</info>');

        $version = $this->getVersion($input);

        $output->writeln('<info>Set version <comment>'.$version.'</comment>...</info>');

        $output->writeln('<info>Crafting application...</info>');

        $this->craftApplication($directory, $version, $output);

        $output->writeln('<comment>Application ready! Build something amazing.</comment>');
    }

    /**
     * Verify that the application does not already exist.
     *
     * @param  string  $directory
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
    protected function craftApplication($directory, $version, $output)
    {
        $composer = $this->findComposer();

        $installationCommand = $this->getInstallationCommand($version,$directory);

        $install = new Process($installationCommand, dirname($directory),null,null,null);
        $install->run();

        $commands = [
            $composer.' install --no-scripts',
            $composer.' run-script post-root-package-install',
            $composer.' run-script post-install-cmd',
            $composer.' run-script post-create-project-cmd',
            "php -r \"copy('.env.example', '.env');\"",
            "php artisan key:generate",
        ];

        $output->writeln('<info>Install dependencies...</info>');

        $process = new Process(implode(' && ', $commands), $directory, null, null, null);

        $process->run();

        return $this;
    }


    /**
     * Get composer installation command
     *
     * @param $version
     * @param $directory
     * @return string
     */
    protected function getInstallationCommand($version, $directory){
        $composer = $this->findComposer();

        switch ($version){
            case "4.2":
                return $composer." create-project laravel/laravel ".$directory." 4.2 --prefer-dist";
                break;
            case "5.0":
                return  $composer." create-project laravel/laravel ".$directory." \"~5.0.0\" --prefer-dist";
                break;
            case "5.1":
                return $composer." create-project laravel/laravel ".$directory." \"5.1.*\" --prefer-dist";
                break;
            case "LTS":
                return $composer." create-project laravel/laravel ".$directory." \"5.1.*\" --prefer-dist";
                break;
            case "5.2":
                return $composer." create-project laravel/laravel ".$directory." --prefer-dist";
                break;
            default:
        }
    }

    /**
     * Get the version that should be installed.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @return string
     */
    protected function getVersion($input)
    {
        $version = $input->getArgument('version');

        if($version == "5.2" || $version == ""){
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
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" composer.phar';
        }

        return 'composer';
    }
}
