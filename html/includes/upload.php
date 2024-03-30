<?php

class  Media
{

    public $imageInfo;
    public $fileName;
    public $fileType;
    public $fileTempPath;
    //Set destination for upload
    public $userPath = SITE_ROOT . DS . '..' . DS . 'uploads/users';
    public $productPath = SITE_ROOT . DS . '..' . DS . 'uploads/products';

    public $errors = array();

    public $upload_errors = array(
        0 => 'Fisierul a fost incarcat, nu s-a generat nici o eroare.',
        1 => 'Dimensiunea fisierului este mai mare decat maximul permis de php.ini.',
        2 => 'Dimensiunea fisierului este mai mare decat valoarea MAX_FILE_SIZE.',
        3 => 'Fisierul a fost incarcat partial',
        4 => 'Nici un fisier nu s-a incarcat',
        6 => 'Lipsa (configurare eronata) director pe server',
        7 => 'Nu se poate scrie pe server (disc full?).',
        8 => 'O extensie a PHP a blocat incarcarea fisierului.'
    );

    public $upload_extensions = array(
        'gif',
        'jpg',
        'jpeg',
        'png',
    );

    public function file_ext($filename)
    {
        $ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
        if (in_array($ext, $this->upload_extensions)) {
            return true;
        }
        return false;
    }

    public function upload($file)
    {
        if (!$file || empty($file) || !is_array($file)):
            $this->errors[] = "Nu s-a incarcat nici un fisier.";
            return false;
        elseif ($file['error'] != 0):
            $this->errors[] = $this->upload_errors[$file['error']];
            return false;
        elseif (!$this->file_ext($file['name'])):
            $this->errors[] = 'Fisierul are un format necunoscut ';
            return false;
        else:
            $this->imageInfo = getimagesize($file['tmp_name']);
            $this->fileName = basename($file['name']);
            $this->fileType = $this->imageInfo['mime'];
            $this->fileTempPath = $file['tmp_name'];
            return true;
        endif;

    }

    public function process()
    {

        if (!empty($this->errors)):
            return false;
        elseif (empty($this->fileName) || empty($this->fileTempPath)):
            $this->errors[] = "Locatia de pe server nu este disponibila.";
            return false;
        elseif (!is_writable($this->productPath)):
            $this->errors[] = $this->productPath . " trebuie sa poata fi scris!!!.";
            return false;
        elseif (file_exists($this->productPath . "/" . $this->fileName)):
            $this->errors[] = "Fisierul {$this->fileName} exista pe server.";
            return false;
        else:
            return true;
        endif;
    }
    /*--------------------------------------------------------------*/
    /* Function for Process media file
    /*--------------------------------------------------------------*/
    public function process_media()
    {
        if (!empty($this->errors)) {
            return false;
        }
        if (empty($this->fileName) || empty($this->fileTempPath)) {
            $this->errors[] = "Locatia pe server nu este disponibila.";
            return false;
        }

        if (!is_writable($this->productPath)) {
            $this->errors[] = $this->productPath . " trebuie sa poata fi scris!!!.";
            return false;
        }

        if (file_exists($this->productPath . "/" . $this->fileName)) {
            $this->errors[] = "Fisierul {$this->fileName} exista pe server.";
            return false;
        }

        return true;

    }
    /*--------------------------------------------------------------*/
    /* Function for Process user image
    /*--------------------------------------------------------------*/
    public function process_user($id)
    {

        if (!empty($this->errors)) {
            return false;
        }
        if (empty($this->fileName) || empty($this->fileTempPath)) {
            $this->errors[] = "Locatia pentru fisier nu este disponibila.";
            return false;
        }
        if (!is_writable($this->userPath)) {
            $this->errors[] = $this->userPath . " trebuia sa poata fi scrisa!!!.";
            return false;
        }
        if (!$id) {
            $this->errors[] = " Lipsa user id.";
            return false;
        }
        $ext = explode(".", $this->fileName);
        $new_name = randString(8) . $id . '.' . end($ext);
        $this->fileName = $new_name;
        if ($this->user_image_destroy($id)) {
            if (move_uploaded_file($this->fileTempPath, $this->userPath . '/' . $this->fileName)) {

                if ($this->update_userImg($id)) {
                    unset($this->fileTempPath);
                    return true;
                }

            } else {
                $this->errors[] = "Nu s-a incarcat fisierul din cauza permisiunilor necorespunzatoare ale directorului de pe server";
                return false;
            }
        }
        return false;
    }
    /*--------------------------------------------------------------*/
    /* Function for Update user image
    /*--------------------------------------------------------------*/
    private function update_userImg($id)
    {
        global $db;
        $sql = "UPDATE Useri SET image='{$this->fileName}' WHERE id= $id";
        $result = $db->query($sql);
        return ($result && $db->affected_rows() === 1 ? true : false);

    }
    /*--------------------------------------------------------------*/
    /* Function for Delete old image
    /*--------------------------------------------------------------*/
    public function user_image_destroy($id)
    {
        global $db;
        $sql = "SELECT image FROM Useri WHERE id= $id";

        $result = $db->query($sql);
        $image = $db->fetch_assoc($result);

        if ($image['image'] === 'no_image.jpg') {
            return true;
        } else {
            unlink($this->userPath . '/' . $image['image']);
            return true;
        }
    }
}
