<?php

/* Pour créer la commande utilisation des ressources suivantes :  */

/*
- https://symfony.com/doc/current/components/console/helpers/table.html
- https://symfony.com/doc/4.4/console.html
- https://stackoverflow.com/questions/9139202/how-to-parse-a-csv-file-using-php
- https://medium.com/@ankit.yadav726/working-with-csv-files-in-php-symfony-5e87ee2b55b
- https://regex101.com/library/tQ0bN5?orderBy=RELEVANCE&search=slug
- https://stackoverflow.com/questions/32614584/how-can-i-remove-all-html-tags-from-an-array#:~:text=No%20need%20for%20a%20regex,trim()%20the%20output%2C%20e.g.&text=To%20add%20to%20JessGabriel's%20answer,to%20internal%20function%20parameters%20(php.





*/

namespace App\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class CreateTableCsvCommand extends Command
{
    // the name of the command (the part after "bin/console")
    public static $defaultName = 'app:table-csv';

    public function configure(): void
    {
        $this
            ->setDescription('Créer un tableau avec des données csv')
            ->setHelp('Cette commande permet de créer un tableau qui prend en paramètre un fichier csv')
            ->addArgument('link', InputArgument::REQUIRED, 'Lien du fichier csv');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        /* on récupère link qui  est le paramètre */
        $linkCsv = $input->getArgument('link');
        $output->writeln('<info>Lien du fichier csv donné : ' . $linkCsv . '</info>');

        /* On définit le chemin du fichier csv et on lui donne comme argument r = read */
        $csvProducts = fopen("$linkCsv", "r");



        /* LE HEADER DU TABLEAU */

        /* on définit $dataHeader avec notre fichier csv et on separe chaque élèment grâce au troisieme paramètre de fgetcsv */
        $dataHeader = fgetcsv($csvProducts, 0, ";");

        if ($dataHeader[2] == "is_enabled") {
            $dataHeader[2] = "status";
        }
        /* Je renomme  */
        $dataHeader[3] = "Price";
        unset($dataHeader[4]);

        $table = new Table($output);

        /* J'ajoute dans le tableau du header l'élement slugs */
        $dataHeader = array_merge($dataHeader, ['slugs']);

        $dataHeader = array_map("ucwords", $dataHeader);



        /* Je défini le header du tableau grâce à notre variable dataHeader */
        $table->setHeaders([$dataHeader]);



        /* LE BODY DU TABLEAU */
        /* Je boucle sur  */
        while (($dataBody = fgetcsv($csvProducts, 0, ";"))) {
            /* On précise que si le deuxième élèment du tableau est = 1 : */
            if ($dataBody[2] == "1") {
                /* on remplace le 2eme élement du tableau par Enable */
                $dataBody[2] = "Enable";
            } else {
                /* Sinon par disable */
                $dataBody[2] = "Disable";
            }

            /* SLUG */
            $slug = trim($dataBody[1]); // trim supprime les espaces en début et fin d'une chaîne
            $slug = preg_replace('/^(?!-)((?:[a-z0-9]+-?)+)(?<!-)$/', '', $slug); //  Rechercher  grace au regex et remplacer  par des espaces (voir l.10)
            $slug = str_replace(' ', '-', $slug); // remplacer les espaces du preg par des tirets
            $slug = strtolower($slug);  // on met tout en miniscule

            /* dump($slug); */

            /* J'applique grâce à la fonction array_map la fonction strip_tags à tous les élèments de mon tableau // strip_tags nous permet de supprimer les balise dans une chained e caractere  */
            //$dataBody = array_map('strip_tags', $dataBody);


            /* J'ajoute dans le tableau du body les slugs */
            $dataBody = array_merge($dataBody, [$slug]);
            /* J'ajoute  au 3 eme élèment du tableau le contenu du 4eme */
            $dataBody[3] = $dataBody[3] . " " . $dataBody[4];
            unset($dataBody[4]);


            /* date */
            /* on applique à notre colonnes "dates"  la fonctions date() on lui passe en paramètre 2 choses, en premier le format ici "r" / en deuxième on utilise la fonction strtotime() en visant cette même colonne */
            $dataBody[6] = date("r", strtotime($dataBody[6]));
            $dataBody[5] = str_replace("<br/>", "\n", $dataBody[5]);

            /* formatage html */

            $table->addRow($dataBody, $slug);
        }








        /* TEST */
        /* dump($slug);*/
        /* dump($csvProducts); */
        /* dump('$dataHeader = ', $dataHeader); */
        /*dump($num); */


        /* Affichage du tableau */
        echo $table->render();
        /* Fermeture du fichier */
        fclose($csvProducts);


        $output->writeln('Le tableau à été créer avec succès');
    }
}
