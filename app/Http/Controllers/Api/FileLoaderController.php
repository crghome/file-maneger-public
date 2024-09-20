<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CropImages;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ZipArchive;

class FileLoaderController extends Controller
{
    private function setFileNameLoadFile(String $name, String $fileExt, Int $key = 0){
        $filename = Str::slug($name);
        if(!empty($key)) $filename .= '_' . $key;
        $filenameExt = $filename . '.' . $fileExt;
        $fileDirectory = config('app.directories.files');
        $fileDirectoryAbs = $_SERVER['DOCUMENT_ROOT'] . $fileDirectory;

        if(file_exists($fileDirectoryAbs . $filenameExt)){
            $filenameExt = $this->setFileNameLoadFile($name, $fileExt, ++$key);
        }

        return $filenameExt;
    }

    public function uploadFile(Request $request){
        $response = array('status' => false, 'message' => 'Не предвиденная остановка сервера', 'data' => array());
        try {
            $start = microtime(true);
            if (!$request->isMethod('POST') || !$request->expectsJson('data')){ throw new \Exception('Некорректное обращение к серверу'); }
            
            $token = $request->_token;
            $file = $request->file('file');
            if(!$file){ throw new \Exception('Не смогли получить файл'); }

            // $filename = Str::slug($file->getClientOriginalName()) . '_s' . $file->getSize() . '_l' . date('YmdHis');
            // $filename = $filename . '.' . $file->extension();

            $filename = $this->setFileNameLoadFile($file->getClientOriginalName(), $file->extension());

            $fileDirectory = config('app.directories.files');
            $fileDirectoryAbs = $_SERVER['DOCUMENT_ROOT'] . $fileDirectory;
            if(!file_exists($fileDirectoryAbs)) mkdir($fileDirectoryAbs, 0777, true);

            $fileAbs = $fileDirectoryAbs . $filename;
            
            move_uploaded_file( $file, $fileAbs );

            $time = round(microtime(true) - $start, 4);
            $response = array(
                'status' => true, 
                'message' => 'Файл загружен', 
                'data' => [
                    'file' => $filename,
                    'directory' => $fileDirectory,
                    'src' => $fileDirectory . $filename,
                    'time' => $time,
                ]
            );
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }
        return response()->json($response);
    }   

    public function getFiles(Request $request){
        $response = array('status' => false, 'message' => 'Не предвиденная остановка сервера', 'data' => array());
        try {
            $start = microtime(true);
            if (!$request->isMethod('POST')){ throw new \Exception('Некорректное обращение к серверу'); }
            
            $token = $request->_token;
            
            $ignored = ['.', '..', 'index.html', 'index.php'];
            $fileDirectory = config('app.directories.files');
            $fileDirectoryAbs = $_SERVER['DOCUMENT_ROOT'] . $fileDirectory;
            if(!file_exists($fileDirectoryAbs)){ throw new \Exception('Нет директории'); }

            $cropImages = new CropImages();

            $files = [];
            foreach((scandir($fileDirectoryAbs)??[]) AS $file){
                if(in_array($file, $ignored)) continue;
                $filetype = mime_content_type($fileDirectoryAbs . $file);
                switch($filetype){
                    case preg_match('/image/', $filetype): $prev = $cropImages->cropImages(imagePath: $fileDirectory.$file, createWidth: 200); break;
                    case preg_match('/video/', $filetype): $prev = config('app.images.video'); break;
                    case preg_match('/audio/', $filetype): $prev = config('app.images.audio'); break;
                    default: $prev = config('app.noImage'); break;
                }
                $prev = $cropImages->cropImages(imagePath: $fileDirectory.$file, createWidth: 200);
                if(empty($prev)) $prev = config('app.noImage');
                $files[] = [
                    'name' => $file,
                    'src' => $fileDirectory . $file,
                    'prev' => $prev,
                    'type' => $filetype,
                    'size' => getimagesize($fileDirectoryAbs . $file),
                    'filesize' => filesize($fileDirectoryAbs . $file),
                    'filemtime' => filemtime($fileDirectoryAbs . $file),
                    'date' => date('d.m.Y H:i:s', filemtime($fileDirectoryAbs . $file)),
                ];
            }

            usort($files, function($a, $b){
                return (strnatcmp($b["filemtime"], $a["filemtime"]));
            });

            $time = round(microtime(true) - $start, 4);
            $response = array(
                'status' => true, 
                'message' => 'Файлы получены', 
                'data' => [
                    'files' => $files,
                    'time' => $time,
                ]
            );
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }
        return response()->json($response);
    }   

    public function getFilesZip(Request $request){
        $response = array('status' => false, 'message' => 'Не предвиденная остановка сервера', 'data' => array());
        try {
            $start = microtime(true);
            if (!$request->isMethod('POST')){ throw new \Exception('Некорректное обращение к серверу'); }
            $data = (object)($request->data??[]);
            if (empty($data->arrFileNames)){ throw new \Exception('Не определены файлы'); }
            
            $needleFiles = (array)$data->arrFileNames;
            // $token = $data->_token;
            
            $ignored = ['.', '..', 'index.html', 'index.php'];
            $fileDirectory = config('app.directories.files');
            $fileDirectoryAbs = $_SERVER['DOCUMENT_ROOT'] . $fileDirectory;
            if(!file_exists($fileDirectoryAbs)){ throw new \Exception('Нет директории'); }

            $files = [];
            $zip = new ZipArchive();
            $fileName = 'zipFile.zip';
            if(file_exists(public_path($fileName))) unlink(public_path($fileName));
            if($zip->open(public_path($fileName), \ZipArchive::CREATE) == TRUE){
                foreach((scandir($fileDirectoryAbs)??[]) AS $file){
                    if(in_array($file, $ignored) || !in_array($file, $needleFiles)) continue;
                    $files[] = [
                        'name' => $file,
                        'src' => $fileDirectory . $file,
                    ];
                    $zip->addFile($fileDirectoryAbs . $file, $file);
                }
                $zip->close();
            }

            $time = round(microtime(true) - $start, 4);
            $response = array(
                'status' => true, 
                'message' => 'Load', 
                'data' => [
                    'search' => $needleFiles,
                    'files' => $files,
                    'zip' => '/' . $fileName,
                    'time' => $time,
                ]
            );
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }
        return response()->json($response);
    }   

}
