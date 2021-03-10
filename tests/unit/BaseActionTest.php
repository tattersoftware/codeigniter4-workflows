<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Config\Factories;
use Tatter\Workflows\BaseAction;
use Tests\Support\Models\FooModel;

class BaseActionTest extends CIUnitTestCase
{
	public function testUsesDefaultComponents()
	{
		$action = new class extends BaseAction {};

		$this->assertSame(config('Workflows'), $action->config);
		$this->assertSame(service('request'), $action->request);
		$this->assertSame(service('response'), $action->response);
	}

	public function testUsesConfigModel()
	{
		$config = config('Workflows');
		$config->jobModel = FooModel::class;
		Factories::injectMock('config', 'Workflows', $config);

		$action = new class extends BaseAction {};

		$this->assertInstanceOf(FooModel::class, $action->jobs);
	}
}
