<?php

namespace App\Command;

use App\Controller\Services\ImportProspectionService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:importcsv')]
class ImportInventaireCommand extends Command

{
    public function __construct(private ImportProspectionService $prospectionService)
    {
        parent::__Construct();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Importer le fichier Inventaire CSV');

        $this->prospectionService->importCSV($io);

        return Command::SUCCESS;
    }
}