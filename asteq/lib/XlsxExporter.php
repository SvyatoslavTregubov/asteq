<?php

namespace Asteq;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

class XlsxExporter
{
    public static function writeToFile(array $heads, array $rows, string $file_name = 'Export'): void
    {
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToBrowser("$file_name.xlsx");

        $heads_row = WriterEntityFactory::createRow(self::getCells($heads));

        $writer->addRow($heads_row);
        foreach ($rows as $row) {
            $writer->addRow(WriterEntityFactory::createRow(self::getCells($row)));
        }

        $writer->close();
    }

    private static function getCells(array $values): array
    {
        $cells = [];
        foreach ($values as $value) {
            $cells[] = WriterEntityFactory::createCell($value);
        }
        return $cells;
    }
}
