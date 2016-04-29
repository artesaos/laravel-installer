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
     * Test execute method
     */
    public function testExecute()
    {
        $app = $this->bootApplication();

        $command = $app->find('new');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(),'name'=>'test','version'=>'LTS']);

        $this->assertRegExp('/Application ready! Build something amazing./', $commandTester->getDisplay());
        $this->assertFileExists('test');
    }

    /**
     * Test if application exists
     *
     * @expectedException RuntimeException
     */
    public function testCheckIfApplicationExists()
    {
        $app = $this->bootApplication();

        $command = $app->find('new');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'name' => 'test', 'version' => 'LTS']);

        $this->assertRegExp('/Application ready! Build something amazing./', $commandTester->getDisplay());
    }

    public function testIfCanInstallFromBranch()
    {
        $app = $this->bootApplication();

        $command = $app->find('new');
        $commandTester = new CommandTester($command);

        $branches = ['master', 'develop'];

        foreach ($branches as $branch){
            $commandTester->execute(['command' => $command->getName(), 'name' => 'test-branch-'.$branch, 'version' => $branch]);

            $this->assertRegExp('/Application ready! Build something amazing./', $commandTester->getDisplay());
            $this->assertFileExists('test-branch-'.$branch);
        }
    }

    public static function tearDownAfterClass()
    {
        system('rm -rf test test-branch-develop test-branch-master');
    }

    private function bootApplication() {
        $app = new Application();
        $app->add(new NewCommand());

        return $app;
    }
}