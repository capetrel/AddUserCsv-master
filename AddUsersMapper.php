<?php

include (dirname(__FILE__) . '/AddUsersHelpers.php');

class AddUsersMapper {

    public static function saveUsersList($users_list) {

        foreach($users_list as $list_item){
            $db_user = new Database;

            // Il faut impérativement hasher le mot de passe avant de l'envoyer à la BDD, en plus celle-ci est encodé en UTF-8 il faut le précisé ici pour la compatibilité.
            $mdp = md5( utf8_encode ($list_item['4']) );

            // On instancie l'objet qui formattent le texte en Attendant une correction du bug de Cumulusclips.
            $objFormatTxt = new AddUsersHelper();
            $formated_username = $objFormatTxt->nameFileTraitement($list_item['3'],'');

            $insert_user = "INSERT INTO" . DB_PREFIX . " users (user_id, username, email, password, status, role, date_created, first_name, last_name, about_me, website, confirm_code, views, last_login, avatar, released)
														 VALUES (NULL, '" .$formated_username. "', '".$list_item['5']."', '".$mdp."', 'active', 'user', CURDATE(), '".$list_item['2']."', '".$list_item['1']."', NULL, NULL, NULL, '0', CURDATE(), NULL, '1');" ;

            $db_user->query($insert_user);

            // Requête sur la nouvel entrée : SELECT user_id FROM `users` WHERE username='$formated_username';
            $id_new_user = " SELECT " . DB_PREFIX . "user_id FROM users WHERE username ='" .$formated_username."'";
            $list_id_new_users = $db_user->basicQuery($id_new_user);

            // requête insert dans privacy INSERT INTO `privacy` (`privacy_id`, `user_id`, `video_comment`, `new_message`, `new_video`, `video_ready`, `comment_reply`) VALUES (NULL, '22', '1', '1', '1', '1', '1');
            $insert_id_privacy = "INSERT INTO" . DB_PREFIX . " privacy (privacy_id, user_id, video_comment, new_message, new_video, video_ready, comment_reply)
                                                 VALUES (NULL, '" . $list_id_new_users['0']['user_id'] . "', '1', '1', '1', '1', '1');";

            $db_user->query($insert_id_privacy);

        }
    }
    
}