<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Workflows\BaseAction;
use Tatter\Workflows\Entities\Job;
use Tatter\Workflows\Exceptions\WorkflowsException;
use Tests\Support\Models\FooModel;

/**
 * @internal
 */
final class BaseActionTest extends CIUnitTestCase
{
    public function testUsesDefaultComponents()
    {
        $action = new class () extends BaseAction {};

        $this->assertSame(config('Workflows'), $action->config);
        $this->assertSame(service('request'), $action->request);
        $this->assertSame(service('response'), $action->response);
    }

    public function testUsesConfigModel()
    {
        $config           = config('Workflows');
        $config->jobModel = FooModel::class;
        Factories::injectMock('config', 'Workflows', $config);

        $action = new class () extends BaseAction {};

        $this->assertInstanceOf(FooModel::class, $action->jobs);
    }

    public function testInitialize()
    {
        $action                      = new class () extends BaseAction {
            public bool $initialized = false;

            protected function initialize()
            {
                $this->initialized = true;
            }
        };

        $this->assertTrue($action->initialized);
    }

    public function testSetJob()
    {
        $action = new class () extends BaseAction {};
        $job    = new Job();

        $action->setJob($job);

        $this->assertSame($job, $action->job);
    }

    /**
     * @dataProvider methodsProvider
     */
    public function testDefaultMethodsThrow(string $method)
    {
        $action = new class () extends BaseAction {};

        $this->expectException(WorkflowsException::class);
        $this->expectExceptionMessage('Not implemented.');

        $action->{$method}();
    }

    /**
     * @return array Array of default methods
     */
    public function methodsProvider(): array
    {
        return [
            ['get'],
            ['head'],
            ['post'],
            ['put'],
            ['delete'],
            ['connect'],
            ['options'],
            ['trace'],
            ['patch'],
        ];
    }
}
