<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FullBackupExport implements WithMultipleSheets
{
    /**
     * @param  array<string, array{headings: array, rows: array}>  $modules
     */
    public function __construct(private readonly array $modules)
    {
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->modules as $title => $module) {
            $sheets[] = new ModuleSheetExport($title, $module['headings'], $module['rows']);
        }

        return $sheets;
    }
}
