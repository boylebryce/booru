<?php

    function delete_imgtag($db, $img_id, $tag_id) {
        $query = 'DELETE FROM `imagetags` WHERE `imgID` = :img_id AND `tagID` = :tag_id';
        $statement = $db->execute($query);
        $statement->bindValue(':img_id', $img_id);
        $statement->bindValue(':tag_id', $tag_id);
        $statement->execute();
        $statement->closeCursor();
    }

    function random_filename($length, $directory = '', $extension = '') {
        $dir = $directory;
    
        do {
            $key = '';
            $keys = array_merge(range(0, 9), range('a', 'z'));
    
            for ($i = 0; $i < $length; $i++) {
                $key .= $keys[array_rand($keys)];
            }
        } while (file_exists($dir . '/' . $key . (!empty($extension) ? '.' . $extension : '')));
    
        return $key . (!empty($extension) ? '.' . $extension : '');
    }

?>