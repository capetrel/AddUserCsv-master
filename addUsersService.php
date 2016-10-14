<?php

include (dirname(__FILE__) . '/AddUsersMapper.php');

$max_size = 5242880 ; // poid du fichier 5 Mo soit : 5242880 octets

// Début de l'upload du fichier, si il y a bien un fichier dans le dossier temporaire tmp_name n'est pas null
if(isset($_FILES['upfile'])){

   $error_test = $_FILES['upfile']['error']; // On attrape l'erreur d'upload si il y en a

    if ($_FILES['upfile']['error'] === 4) {
        echo '<strong style="color: #8a1f11" >Vous n\'avez pas sélectionné de fichier.</strong><br>';

    }else {

        $target_dir = __DIR__ . '/uploads/'; // constante magique __DIR__ -> dans le dossier du script, indique ou stocker le fichier

        //$target_dir_save = __DIR__ . '/files/'; // constante magique __DIR__ -> dans le dossier du script, indique ou stocker le fichier

        $up_file = basename($_FILES['upfile']['name']); // On récupère le nom du fichier uploadé

        $extension = strrchr($_FILES['upfile']['name'], '.'); // On récupère l'extension du fichier en string

        $mime_extension = $_FILES['upfile']['type']; // On récupère le type MIME du fichier

        $file_size = filesize($_FILES['upfile']['tmp_name']);// La taille du fichier avec le nom temporaire donné par php

        // ici c'est particulier avec @ j'empeche php d'afficher le warning
        $info = new finfo(FILEINFO_NONE);
        @$type_extension = $info->file($_FILES['upfile']['tmp_name']); // Utiliser fonction php finfo() plus fine que ['type']

        $extensions = array('.csv'); // On prévoit un tableau si on veut rajouter des extensions.

        $mime_extensions = array('text/csv', 'text/plain'); // tableau pour les MIME V1

        $type_extensions = array('text/plain', 'UTF-8 Unicode text', 'data'); // tableau pour les type MIME V2

        //TODO utiliser d'autre type de fichier (PHPEXcel)

        // debug
        echo '<strong style="color: #204d74">nom du fichier uploadé : ' . $up_file . '</strong><br>';
        /*
        echo 'L\'extension du fichier : ' . $extension . '<br>';
        echo 'Son type MIME (V1) est : ' . $mime_extension . '<br>';
        echo 'Son type MIME (V2) est : ' . $type_extension . '<br>';
        */
        // Vérification sur le fichier, taille, type, mime, etc ...
        if ($type_extension === false) { // Ce if me permet d'attraper le warning précédent
            $error = error_get_last();
            echo '<strong style="color: #8a1f11">Le fichier n\'a pas pu être chargé pour la raison ci-dessous.</strong><br>';
        }

        if ($file_size > $max_size) { // si la taille du fichier est supérieur à taille max
            $error = '<strong style="color: #8a1f11">La taille du fichier '.$up_file.' est de : '.AddUsersHelper::weightFile($file_size).' et dépasse la taille maximum autorisé de : '.AddUsersHelper::weightFile($max_size).'.</strong><br>';
        }

        if (!in_array($extension, $extensions)) { //Si l'extension n'est pas dans les tableaux
            $error = '<strong style="color: #8a1f11">Vous devez uploader un fichier avec l\'extension ".csv".</strong><br>'; // On lance message d'erreur
        }

        if (!in_array($mime_extension, $mime_extensions)) {
            $error = '<strong style="color: #8a1f11">Votre fichier n\'est pas correctement formaté en csv.</strong><br>'; // On lance message d'erreur
        }

        if (!in_array($type_extension, $type_extensions)) {
            $error = '<strong style="color: #8a1f11">Votre fichier n\'est pas formaté en csv.</strong><br>'; // On lance message d'erreur
        }

        //S'il n'y a pas d'erreurs on parse le fichier avec ce code
        if (!isset($error)) {

            // Utilisons l'objet qui formate le texte pour formater le nom.
            $file = new AddUsersHelper();
            $name_file_format = $file->nameFileTraitement($up_file, $extension);
            // echo 'traitement nom du fichier : ' . $name_file_format . '<br>';

            // On enregistre le fichier sur le serveur et si la fonction move renvoie true on commence le traitement
            if (move_uploaded_file($_FILES['upfile']['tmp_name'], $target_dir . $up_file)) {

                // On indique le chemin du fichier
                $file_path = $target_dir . $up_file;

                // on passe $file_path à l'instanciation d'un objet qui renvoie une liste.
                $users_list = new AddUsersHelper();
                $list_test = $users_list->fileTraitment($file_path);

                // file traitement renvoie soit un message d'erreur, soit la liste.
                if ($list_test == 'erreur_1' || $list_test == 'erreur_2') {
                    echo '<strong style="color: #8a1f11">Le fichier ne correspond pas au modèle.</strong><br>';

                // Sinon on lance le traitement pour l'insert
                }else{
                    // J'utilise une classe statique pour l'insertion des donnée en BDD, car je n'ai pas besoin d'instance
                    AddUsersMapper::saveUsersList($list_test);

                    // Le traitement c'est bien passé =>
                    echo '<strong style="color: #204d74">Le traitement du fichier a été réalisé avec succès !</strong><br>';
                }

            } else { //Sinon la fonction move renvoie false il y une erreur le programme s'arrête.

                echo '<strong style="color: #8a1f11">Il y a eu une erreur essayez à nouveau</strong>';
            }

        } else {
            echo $error; //Sinon on affiche l'erreur
        }
    }
}else{
    echo '<strong style="color: #8a1f11">Veuillez choisir un fichier à uploader</strong>';
}
