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
use Tests\Support\Models\FooModel;

/**
 * @internal
 */
final class BaseActionTest extends CIUnitTestCase
{
    public function testUsesConfigModel()
    {
        $config           = config('Workflows');
        $config->jobModel = FooModel::class;
        Factories::injectMock('config', 'Workflows', $config);

        $action = new class (new Job()) extends BaseAction {};
        $result = $this->getPrivateProperty($action, 'jobs');

        $this->assertInstanceOf(FooModel::class, $result);
    }

    public function testInitialize()
    {
        $action                      = new class (new Job()) extends BaseAction {
            public bool $initialized = false;

            protected function initialize()
            {
                $this->initialized = true;
            }
        };

        $this->assertTrue($action->initialized);
    }
}
