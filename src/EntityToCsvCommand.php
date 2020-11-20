<?php


namespace App\Command\EntityToExcel;


use EntityToExcel\Excel\ExcelFileBuilder;
use EntityToExcel\Services\DataTransformer;
use EntityToExcel\Services\PropertyReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

class EntityToCsvCommand extends Command
{

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(string $name = null, KernelInterface $kernel, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->kernel = $kernel;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('entity:excel');
        $this->addArgument('entity', InputArgument::REQUIRED, 'The name of the entity.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $class = new \ReflectionClass(sprintf('App\Entity\%s', $input->getArgument('entity')) );

        $properties = $class->getProperties();

        $excelMaker = new ExcelFileBuilder($this->kernel, $this->em, $input->getArgument('entity'));

        $excelMaker->setProperties(DataTransformer::makePropertiesArray($properties));

        $fileUrl = $excelMaker->saveFile($class->getName());
        $io->writeln($fileUrl);
    }
}
