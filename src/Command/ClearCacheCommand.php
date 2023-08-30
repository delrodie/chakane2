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
use Symfony\Component\VarDumper\VarDumper;

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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $devises = ['XOF','USD','EUR','xof','usd','eur'];

        $entities = [
            'slides',
            'marque',
            'devise',
            'maintenance',
            'collections',
            'contact',
            'categories',
            'produitsIdDesc',
            'newsProduits',
            'flagProduits',
            'femmes-eur',
            'femmes-usd',
            'femmes-cfa',
            'hommes-cfa',
            'hommes-eur',
            'hommes-usd',
            'enfants-cfa',
            'enfants-eur',
            'enfants-usd',
            'sacs-cfa',
            'sacs-eur',
            'sacs-usd',
            'creations',
//            'newsProduits-USD',
//            'newsProduits-XOF',
//            'newsProduits-EUR',
//            'flagProduits-EUR',
//            'flagProduits-XOF',
//            'flagProduits-USD',
        ];

        foreach ($entities as $entity){
            $this->allRepository->allCache($entity, true);
            $io->info("Cache de {$entity} supprimés avec succès!");
        }

        // Pour les produits
        $produits = [
            'newsProduits',
            'newsProduits',
            'newsProduits',
            'flagProduits-EUR',
            'flagProduits-XOF',
            'flagProduits-USD',
        ];

        $this->allRepository->cacheDiversesRechercheProduits('newsProduits', true);
//        $produits_delete = array_map([$this->allRepository, 'cacheDiversesRechercheProduits'], $produits);

//        VarDumper::dump($produits_delete);

        $io->success('Caches supprimés avec succès!');

        return Command::SUCCESS;
    }

}
