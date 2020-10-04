<?php

namespace Asteq;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class XlsxImporter
{
    public static function readFromFile(string $file_path, bool $removeHeads = true): array
    {
        $reader = ReaderEntityFactory::createXLSXReader();

        $reader->open($file_path);

        $rows = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $cells = $row->getCells();
                $rows[] = $cells;
            }
        }

        $reader->close();
        if ($removeHeads) {
            unset($rows[0]);
        }

        return $rows;
    }
}
