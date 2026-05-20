<?php

namespace Tests\Utils\Datasets\Test;

use Tests\Utils\Datasets\Datasets;

class EmptyDataSet extends Datasets
{
    protected function getTableToClear(): array
    {
        return [];
    }

    public function getReferences(): array
    {
        return [];
    }

    protected function resetReferences(): void {}

    public function setDatasets(): void {}
}
