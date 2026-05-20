<?php

namespace Tests\Utils;

use App\Models\User;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\Utils\Datasets\Datasets;
use Tests\Utils\Datasets\Test\EmptyDataSet;

abstract class TenancyTestCase extends TestCase
{
    private ?Datasets $datasets;

    protected function getDatasetClass(): string
    {
        return EmptyDataSet::class;
    }

    protected function getDataset(): Datasets
    {
        return $this->datasets;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $tenant = Tenancy::find('test');
        Tenancy::initialize($tenant);

        $datasets = $this->getDatasetClass();
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->datasets = new $datasets;
        $this->datasets->setDatasets();
    }

    protected function tearDown(): void
    {
        $this->datasets->clearDatasets();
        $this->datasets = null;

        Tenancy::end();
        parent::tearDown();
    }
}
