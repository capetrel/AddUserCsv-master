<?php
/**
 * Created by PhpStorm.
 * User: capetrel
 * Date: 07/10/2016
 * Time: 13:56
 */
//include (dirname(__FILE__) . '/PHPExcel.php');

class AddUsersHelper {

    // Pitite fonction qui affiche la taille d'un fichier en Ko, Mo, Go, To, Po,
    public static function weightFile($octets){

        $resultat = $octets;
        for ($i=0; $i < 8 && $resultat >= 1024; $i++) {
            $resultat = $resultat / 1024;
        }
        if ($i > 0) {
            return preg_replace('/,00$/', '', number_format($resultat, 2, ',', ''))
            . ' ' . substr('KMGTPEZY',$i-1,1) . 'o';
        } else {
            return $resultat . ' o';
        }

    }

    // fonction pour gérer les message d'erreur d'upload de fichier, paramètre code erreur upload, poid du fichier, poid max autorisé
    public function codeToMessage($code, $file_weight, $max_weight){
        switch ($code) {
            case 0:
                $message = " 0 = Succès ! Il n'y a pas eu de problème à l'upload.";
                break;
            case 1:
                $message = "1 = La taille du fichier dépasse les directives du serveur : php.ini/upload_max_filesize";
                break;
            case 2:
                $message = "2 = La taille du fichier : ".$this::weightFile($file_weight)." dépasse la taille maximum autorisé : ".$this::weightFile($max_weight).". ";
                break;
            case 3:
                $message = "3 = Le fichier n'a été que partiellement uploadé";
                break;
            case 4:
                $message = "4 = Aucun fichier n'a été uploader";
                break;
            case 5:
                $message = "6 = Le dossier temporaire est manquant";
                break;
            case 6:
                $message = "7 = Échec de l'écriture du fichier sur le disque";
                break;
            case 7:
                $message = "8 = Une extension PHP a arrêté le téléversement ";
                break;

            default:
                $message = "default = Erreur d'upload inconnue";
                break;
        }
        return $message;
    }

    // fonction qui formate le nom d'un fichier prend en paramètre le fichier et son extension
    public function nameFileTraitement($txt, $ext='',$charset='utf-8'){

        $txt = htmlentities($txt, ENT_NOQUOTES, $charset);
        // $ext = strstr($txt, '.'); affiche la chaine de caractère se situant après un needle (après le point)

        $txt = preg_replace('#Ç#', 'C', $txt);
        $txt = preg_replace('#ç#', 'c', $txt);
        $txt = preg_replace('#è|é|ê|ë#', 'e', $txt);
        $txt = preg_replace('#È|É|Ê|Ë#', 'E', $txt);
        $txt = preg_replace('#à|á|â|ã|ä|å#', 'a', $txt);
        $txt = preg_replace('#@|À|Á|Â|Ã|Ä|Å#', 'A', $txt);
        $txt = preg_replace('#ì|í|î|ï#', 'i', $txt);
        $txt = preg_replace('#Ì|Í|Î|Ï#', 'I', $txt);
        $txt = preg_replace('#ð|ò|ó|ô|õ|ö#', 'o', $txt);
        $txt = preg_replace('#Ò|Ó|Ô|Õ|Ö#', 'O', $txt);
        $txt = preg_replace('#ù|ú|û|ü#', 'u', $txt);
        $txt = preg_replace('#Ù|Ú|Û|Ü#', 'U', $txt);
        $txt = preg_replace('#ý|ÿ#', 'y', $txt);
        $txt = preg_replace('#Ý#', 'Y', $txt);
        $txt = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $txt); // pour les ligatures e.g. '&oelig;'
        $txt = preg_replace('#&[^;]+;#', '', $txt); // supprime les autres caractères
        $txt = preg_replace('/([^a-z0-9]+)/i', '', $txt); //supprime les points

        return $txt . $ext;
    }

    // function qui lit un fichier cvs, enregistre les entrées  et les renvoie dans un tableau.
    public function fileTraitment($file_path) {

        if (($handle = fopen($file_path, "r")) !== FALSE) { // fopen permet d'ouvrir le fichier

            fgetcsv($handle); // En Ajoutant cette ligne le programme lira la premiere ligne(qui n'est pas utilisé). Du coup le pointeur sera sur la deuxième ligne
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) { // la fonction fgetcsv va insérer chaque ligne de notre fichier dans un tableau avec un indice incrémenté pour chaque champ séparé par une virgule,(1000 représente le nombre de caractère maximum pour une ligne du fichier lu)
                // Si la première entrée (colonnes) n'est pas vide -> erreur
                if ($data['0'] != null) { // contrôler le contenu de $data
                    return 'erreur_1'; // renvoie un erreur
                    break; // traitement s'arrête
                    // Sinon on insère la donnée dans le tableau

                }elseif ($data['1'] == null){
                    return 'erreur_2'; // renvoie un erreur
                    break; // traitement s'arrête

                }elseif ($data['2'] == null){
                    return 'erreur_2'; // renvoie un erreur
                    break; // traitement s'arrête

                }else{
                $users_list[] = $data;
                }
            }
            fclose($handle); // ferme le fichier
            unlink($file_path); // on le supprime
        }
        return $users_list;
    }

    // TODO fonction avec PHPExcel qui lira les feuilles de calculs.

}