<?php

namespace App\Command;

use App\Repository\AllRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:cache-clear',
    description: 'Suppression des caches repository et http',
)]
class ClearCacheCommand extends Command
{
    public function __construct(
        private AllRepository $allRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $entities = [
            'slides',
            'marque',
            'devise',
            'maintenance'
        ];

        foreach ($entities as $entity){
            $this->allRepository->allCache($entity, true);
            $io->info("Cache de {$entity} supprimés avec succès!");
        }

        $io->success('Caches supprimés avec succès!');

        return Command::SUCCESS;
    }

}
