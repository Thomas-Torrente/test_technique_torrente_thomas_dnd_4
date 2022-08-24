<?php

/* Pour créer la commande utilisation des ressources suivantes : (Ca marche pas mais ....)  */

/* 
- https://symfony.com/doc/current/components/console/helpers/table.html
- https://symfony.com/doc/4.4/console.html
- https://stackoverflow.com/questions/9139202/how-to-parse-a-csv-file-using-php
- https://medium.com/@ankit.yadav726/working-with-csv-files-in-php-symfony-5e87ee2b55b
- https://regex101.com/library/tQ0bN5?orderBy=RELEVANCE&search=slug




*/

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class CreateTableCsvCommand extends Command
{
    // the name of the command (the part after "bin/console")
    public static $defaultName = 'app:table-csv';

    public function configure(): void
    {
        $this
            ->setDescription('Créer un tableau avec des données csv')
            ->setHelp('Cette commande permet de créer un tableau qui prend en paramètre un fichier csv');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        /* On définit le chemin du fichier csv et on lui donne comme argument r = read */
        if (($handle = fopen("https://recrutement.dnd.fr/products.csv", "r"))) {
            /* on fait une boucle tant que data = $handle (l.42)    */

            while (($data = fgetcsv($handle, 3000, ";"))) {
                /* on définit num en comptant le nombre d'élèment qui est séparé par un point vigule grace au argument de fgetcsv  */

                /* SLUG */
                $slug = trim($data[1]); // trim — Supprime les espaces en début et fin d'une chaîne
                $slug = preg_replace('/^(?!-)((?:[a-z0-9]+-?)+)(?<!-)$/', '', $slug); //  Rechercher  grace au regex et remplacer  par des espaces (voir l.10)
                $slug = str_replace(' ', '-', $slug); // remplacer les espaces du preg par des tirets
                $slug = strtolower($slug);  // on met tout en miniscule



                /* TABLE */
                /* On définit le tableau qui va contenir les données */
                $table = new Table($output);
                $table->setHeaders([$data]);


                /* $table->setRows([$data]); */

                /* TEST */
                /* dump($slug);*/
                /* dump($handle); */
                /* dump('$data = ', $data); */
                /*dump($num); */

                echo $table->render();
            }
            fclose($handle);
            $output->writeln('Le tableau à été créer avec succès');
        } else {
            $output->writeln('Erreur lors de la création du tableau');
        }
    }
}
