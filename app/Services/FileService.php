<?php
namespace App\Services;

class FileService{
    public static function getFilesDirectory(String $directory, Array $needleName = []){
        $result = (object)['status' => true, 'message' =>'', 'files' => []];
        try {
            $ignored = ['.', '..', 'index.html', 'index.php'];
            $fileDirectoryAbs = $_SERVER['DOCUMENT_ROOT'] . $directory;
            if(!file_exists($fileDirectoryAbs)){ throw new \Exception('Нет директории'); }

            foreach((scandir($fileDirectoryAbs)??[]) AS $file){
                if(in_array($file, $ignored)) continue;
                if(!empty($needleName) && !in_array($file, $needleName)) continue;
                $filetype = mime_content_type($fileDirectoryAbs . $file);
                if(empty($prev)) $prev = config('app.noImage');
                $result->files[] = (object)[
                    'name' => $file,
                    'src' => $directory . $file,
                    'type' => $filetype,
                    'size' => getimagesize($fileDirectoryAbs . $file),
                    'filesize' => filesize($fileDirectoryAbs . $file),
                    'filemtime' => filemtime($fileDirectoryAbs . $file),
                    'date' => date('d.m.Y H:i:s', filemtime($fileDirectoryAbs . $file)),
                ];
            }
        } catch (\Exception $e) {
            $result->status = false;
            $result->message = $e->getMessage();
        }
        return $result;
    }
}