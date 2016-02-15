<?php

/**
 * @author    Mauri de Souza Nunes <mauri870@gmail.com>
 * @copyright Copyright (c) 2015, Mauri de Souza Nunes <github.com/mauri870>
 * @license   https://opensource.org/licenses/MIT MIT License
 */

namespace Artesaos\LaravelInstaller\Tests;

use Artesaos\LaravelInstaller\Console\NewCommand;
use Guzzle\Common\Exception\RuntimeException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use PHPUnit_Framework_TestCase as PhpUnit;

class NewCommandTest extends PhpUnit
{

    /**
     * Return a new instance of commandTester for the NewCommand
     */
    public function returnCommandTester()
    {
        $application = new Application();
        $application->add(new NewCommand());
        $command = $application->find('new');

        $commandTester = new CommandTester($command);

        return $commandTester;
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $commandTester = $this->returnCommandTester();

        $commandTester->execute(['command' => 'new','name'=>'test','version'=>'LTS']);

        $this->assertRegExp('/Application ready! Build something amazing./', $commandTester->getDisplay());
    }

    /**
     * Test if application exists
     *
     * @expectedException RuntimeException
     */
    public function testCheckIfApplicationExists()
    {
        $commandTester = $this->returnCommandTester();

        $commandTester->execute(['command' => 'new', 'name' => 'test', 'version' => 'LTS']);
    }

    /**
     * Test if a RuntimeException is thrown when the wrong version is passed
     *
     * @expectedException RuntimeException
     * @expectedExceptionMessage The version you are trying to download is not available!
     */
    public function testCanInstallANotAvailableVersion()
    {
        $commandTester = $this->returnCommandTester();

        $commandTester->execute(['command' => 'new', 'name' => 'test', 'version' => 'WrongVersion']);
    }
}