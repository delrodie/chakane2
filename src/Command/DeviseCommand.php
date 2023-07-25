<?php

namespace App\Command;

use App\Repository\AllRepository;
use App\Repository\DeviseRepository;
use App\Service\Utility;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:devise',
    description: 'Initialisation et mise à jour des devises',
)]
class DeviseCommand extends Command
{
    public function __construct(
        private Utility $utility
    )
    {
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->utility->saveDevise();

        $io->success("La mise à jour de la devise est effective");

        return Command::SUCCESS;
    }
}
