<?php

namespace Tests\Utils\Datasets;

use Illuminate\Support\Facades\DB;

abstract class Datasets
{
    abstract protected function getTableToClear(): array;

    abstract public function getReferences(): array;

    abstract protected function resetReferences(): void;

    abstract public function setDatasets(): void;

    public function clearDatasets(): void
    {
        $this->resetReferences();

        $tables = DB::select('SHOW TABLES');
        $tableToClear = $this->getTableToClear();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table) {

            $tableArray = (array) $table;
            $tableName = array_values($tableArray)[0];

            if (! in_array($tableName, $tableToClear)) {
                continue;
            }

            DB::table($tableName)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
