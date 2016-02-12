<?php

/**
 * @author    Mauri de Souza Nunes <mauri870@gmail.com>
 * @copyright Copyright (c) 2015, Mauri de Souza Nunes <github.com/mauri870>
 * @license   https://opensource.org/licenses/MIT MIT License
 */

namespace Artesaos\LaravelInstaller\Tests;

use Artesaos\LaravelInstaller\Console\NewCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use PHPUnit_Framework_TestCase as PhpUnit;

class NewCommandTest extends PhpUnit
{

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $application = new Application();
        $application->add(new NewCommand());

        $command = $application->find('new');
        $commandTester = new CommandTester($command);


        $commandTester->execute(['command' => $command->getName(),'name'=>'test','version'=>'LTS']);

        $this->assertRegExp('/Application ready! Build something amazing./', $commandTester->getDisplay());
    }
}