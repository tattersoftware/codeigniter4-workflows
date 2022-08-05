<?php

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
    public function testUsesConfigModel(): void
    {
        $config           = config('Workflows');
        $config->jobModel = FooModel::class;
        Factories::injectMock('config', 'Workflows', $config);

        $action = new class (new Job()) extends BaseAction {};
        $result = $this->getPrivateProperty($action, 'jobs');

        $this->assertInstanceOf(FooModel::class, $result);
    }

    public function testInitialize(): void
    {
        $action                      = new class (new Job()) extends BaseAction {
            public bool $initialized = false;

            protected function initialize(): void
            {
                $this->initialized = true;
            }
        };

        $this->assertTrue($action->initialized);
    }

    public function testAttributes(): void
    {
        $action                     = new class (new Job()) extends BaseAction {
            public const ATTRIBUTES = [
                'name' => 'Banana',
            ];
        };
        $result = $action::getAttributes();

        $this->assertSame('fas fa-tasks', $result['icon']);
        $this->assertSame('Banana', $result['name']);
        $this->assertSame('Banana', $action::attr('name'));
    }
}
