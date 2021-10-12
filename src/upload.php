<?php

/*
* Original Write by Lamhot Simamora
* Library Upload File PHP
* October 2021
*/

header("Access-Control-Allow-Origin: *");

class Upload
{
    private $filename = true; // true -> random filename , false -> original filename
    private $lengthFilename;
    private $allowExt;
    private $obj_file;
    private $file = [];
    private $maxsize = 2000; // in kilobyte
    private $targetPath;

    private $message = [];
    private $hashFile;
    private $image_only = true;

    private $result = false;
    private $data = false;
    private $skip_error = false;

    private $image_ext = array(
        'image/png',
        'image/jpeg',
        'image/jpg',
        'image/gif',
        'image/bmp',
        'image/webp',
        'image/svg+xml', 'image/x-icon'
    );

    private $document_ext = array(
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // excel 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' //word
    );

    public function setLengthFilename($val = null)
    {
        if ($val) {
            $this->lengthFilename = $val;
        }
    }

    public function documentOnly()
    {
        $this->image_only = false;
        $this->allowExt = $this->document_ext;
        return $this;
    }

    public function imageOnly()
    {
        $this->allowExt = $this->image_ext;
        return $this;
    }

    public function init($data)
    {
        if ($data) {
            if (!isset($data['file'])) {
                $this->message = 'The file is not defined !';
                $this->output();
            } else {
                $this->obj_file = $data['file'];
            }

            $this->message = '';
            $this->image_only = isset($data['image_only']) ? $data['image_only'] : true;

            if (isset($data['filename'])) {
                if ($data['filename'] == true) {
                    $this->filename = true;
                } else {
                    $this->filename = false;
                }
            } else {
                $this->filename = false;
            }
            $this->lengthFilename = isset($data['lengthFilename']) ? $data['lengthFilename'] : 20;
            $this->maxsize = isset($data['maxsize']) ? $data['maxsize'] : 2000;
            $this->targetPath = isset($data['path']) ? $data['path'] : null;
            $this->skip_error = isset($data['skip_error']) ? $data['skip_error'] : false;

            if ($this->image_only) {
                $this->allowExt = isset($data['extention']) ? $data['extention'] : $this->image_ext;
            } else {
                $this->allowExt = isset($data['extention']) ? $data['extention'] : [];
            }
            $this->result = false;
        }

        return $this;
    }

    public function start()
    {
        if (count($_FILES) == 0) {
            $this->message = 'Files is null !';
            $this->output();
        }

        $i = 1;
        $message = array();
        $files = array();
        foreach ($this->obj_file as $key => $value) {
            if (!isset($_FILES[$value])) {
                $message[$i] = 'The file ' . $this->file . ' from html is not exist !';
                $this->output();
            }

            $obj = $_FILES[$value];

            // checking if image only
            if ($this->image_only) {
                // if file type is not in array, then return false;
                if (!in_array($obj['type'], $this->allowExt)) {
                    if (!$this->skip_error) {
                        $message[$i] = 'File type is not allowed !  Image only !';
                        $this->message = $message;
                        $this->output();
                    } else {
                        continue;
                    }
                }
            } else {
                if (count($this->allowExt) == 0) {
                } else {
                    // if file type is not in array, then return false;
                    if (!in_array($obj['type'], $this->allowExt)) {
                        if (!$this->skip_error) {
                            $message[$i] = 'File type is not allowed ! Allowed file type ' . json_encode($this->allowExt);
                            $this->message = $message;
                            $this->output();
                        } else {
                            continue;
                        }
                    }
                }
            }

            // checking size file
            if (($obj['size'] / 1000) > ($this->maxsize)) {
                $message[$i] = 'File is to big ! Maximum size is {' . $this->maxsize . ' KB }';
                $this->output();
            }


            // checking the destination path

            $final_filename = '';

            if ($this->filename == true) {
                $final_filename = $this->generateFileName($this->lengthFilename) . strtolower($this->getExt($obj));
            } else {
                $final_filename = $obj['name'];
            }

            if (is_dir($this->targetPath)) {
                move_uploaded_file($obj['tmp_name'], $this->targetPath . '/' . $final_filename);
                $this->result = true;
                $message[$i] = 'File {' . $final_filename . '} upload success !';
                $files[$i] = $final_filename;
            } else {
                $this->message = 'Directory path {' . $this->targetPath . '} is not exist !';
                $this->output();
            }
            $i++;
        }
        $this->message = $message;
        $this->file    = $files;
        $this->output();
    }

    private function getExt($obj)
    {
        $findext = strpos($obj['name'], ".");

        return substr($obj['name'], $findext, strlen($obj['name']));
    }

    private function generateFileName($length = 15)
    {
        $c = '0123aBcDeFgHiJkLmNoPqRsTuVwXyZ456789';
        $cL = strlen($c);
        $rS = '';
        for ($i = 0; $i < $length; $i++) {
            $rS .= $c[rand(0, $cL - 1)];
        }
        return $rS;
    }

    private function output()
    {
        if ($this->file) {
            exit(json_encode(array('result' => $this->result, 'message' => $this->message, 'filename' => $this->file)));
        }
        exit(json_encode(array('result' => $this->result, 'message' => $this->message)));
    }
}
