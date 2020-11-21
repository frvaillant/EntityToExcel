<?php


namespace EntityToExcel\Excel;


use EntityToExcel\Services\DataTransformer;
use Doctrine\ORM\EntityManager;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpKernel\KernelInterface;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use \PhpOffice\PhpSpreadsheet\Style\Alignment;
use \PhpOffice\PhpSpreadsheet\Style\Border;

class ExcelFileBuilder
{

    private $properties;
    private $kernel;
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;
    /**
     * @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    private $mainSheet;

    /**
     * @var EntityManager
     */
    private $em;

    const FIRST_LINE  = 1;
    const SECOND_LINE = 2;
    const THIRD_LINE  = 3;


    public function __construct(KernelInterface $kernel, EntityManager $em, $name)
    {
        $this->em = $em;
        $this->kernel = $kernel;
        $this->spreadsheet = new Spreadsheet();
        $this->mainSheet = $this->spreadsheet->getActiveSheet();
        $this->mainSheet->setTitle($name);
    }

    public function setProperties($properties)
    {
        $this->properties = $properties;
    }


    private function setColumns($sheet, $properties): void
    {
        $letter = 'A';
        foreach ($properties as $key => $property) {

            // If is just a simple property
            if (isset($property['name'])) {

                $sheet->setCellValue($letter . self::FIRST_LINE, $property['name']);

                $this->formatCell($sheet, $letter . self::FIRST_LINE, $property['type']);

                $sheet->setCellValue($letter . self::SECOND_LINE, $property['displayName']);

                // Make a dropdown choice list if necessary
                if(null !== $property['listFromEntity']) {
                    $repository = $this->em->getRepository($property['listFromEntity']);
                    $data = DataTransformer::makeListFromEntity($repository->findAll(), $property['fieldName']);
                    $this->createChoiceList($sheet, $letter . self::THIRD_LINE, $data);
                }

                // Make a dropdown choice list if necessary
                if(null !== $property['list']) {
                    $this->createChoiceList($sheet, $letter . self::THIRD_LINE, $property['list']);
                    $sheet->setCellValue($letter . self::THIRD_LINE, $property['defaultValue']);
                }

                // Set default value if there is one
                if ($property['defaultValue']) {
                    $sheet->setCellValue($letter . self::THIRD_LINE, $property['defaultValue']);
                }

                $sheet->getColumnDimension($letter)->setAutoSize(true);

                $letter++;

            } else { // Create new sheet to store secondary entity
                $this->createSubSheet($key, $property);
            }
        }
        $this->setMainLineStyle($sheet);
    }

    private function createSubSheet($name, $property)
    {
        list($namespace, $entityName) = explode('App\Entity\\', $name);
        $subSheet = $this->createSheet($entityName);
        $this->setColumns($subSheet, $property);
    }

    private function setMainLineStyle($sheet)
    {
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],

            'fill' => [
                'fillType' => Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 0,
                'startColor' => [
                    'argb' => 'FF00b7d0',
                ],
                'endColor' => [
                    'argb' => 'FF00b7d0',
                ],
            ],
        ];

        $borders = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ]
        ];

        $sheet->getStyle('A' . self::FIRST_LINE . ':' . $sheet->getHighestColumn() . self::FIRST_LINE)->applyFromArray($styleArray);
        $sheet->getStyle('A' . self::SECOND_LINE . ':' . $sheet->getHighestColumn() . self::SECOND_LINE)->applyFromArray($borders);
        $sheet->getStyle('A' . self::THIRD_LINE . ':' . $sheet->getHighestColumn() . self::THIRD_LINE)->applyFromArray($borders);
    }

    private function formatCell(Worksheet $sheet, string $cell, string $type)
    {
        switch ($type) {
            case 'integer' :
                $sheet->getStyle($cell)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            case 'float' :
                $sheet->getStyle($cell)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            default :

        }
    }

    private function createSheet($sheetName)
    {
        $worksheet = $this->spreadsheet->createSheet();
        $worksheet->setTitle($sheetName);
        return $worksheet;
    }

    public function createDir()
    {
        if (!is_dir($this->kernel->getProjectDir() . '/public/xls')) {
            mkdir($this->kernel->getProjectDir() . '/public/xls');
        }
    }

    public function saveFile($name)
    {
        list($namespace, $name) = explode('App\Entity\\', $name);

        $this->setColumns($this->mainSheet, $this->properties);

        $this->spreadsheet->setActiveSheetIndex(0);
        $this->mainSheet->setSelectedCell('B2');

        $this->createDir();

        $writer = new Xlsx($this->spreadsheet);
        $writer->save($this->kernel->getProjectDir() . '/public/xls/' . $name . '.xlsx');

        return $this->kernel->getProjectDir() . '/public/xls/' . $name . '.xlsx';
    }

    public function createChoiceList(Worksheet $sheet, string $cell, array $array, $required = false)
    {
        $validation = $sheet->getCell($cell)->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $list = '"' . implode(', ', $array) . '"';
        $validation->setFormula1($list);
        $validation->setAllowBlank(!$required);
        $validation->setShowDropDown(true);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
    }
}
