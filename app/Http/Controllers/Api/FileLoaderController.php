<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CropImages;
use App\Http\Controllers\Controller;
use App\Services\FileService;
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
            
            $fileDirectory = config('app.directories.files');

            $cropImages = new CropImages();

            $fileRes = FileService::getFilesDirectory(directory: $fileDirectory);
            if(false == ($fileRes->status??false)){ throw new \Exception($fileRes->message??'Ошибка чтения директории'); };

            $files = $fileRes->files??[];
            foreach($files AS $file){
                $prev = $cropImages->cropImages(imagePath: $file->src, createWidth: 200);
                $file->prev = !empty($prev) ? $prev : $prev = config('app.noImage');
            }

            // usort((array)$files, function($a, $b){
            //     return (strnatcmp($b["filemtime"], $a["filemtime"]));
            // });
            usort($files, function($a, $b){
                return (strnatcmp($b->filemtime, $a->filemtime));
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
            
            $fileDirectory = config('app.directories.files');
            $fileDirectoryAbs = $_SERVER['DOCUMENT_ROOT'] . $fileDirectory;
            if(!file_exists($fileDirectoryAbs)){ throw new \Exception('Нет директории'); }

            $fileRes = FileService::getFilesDirectory(directory: $fileDirectory, needleName: $needleFiles);
            if(false == ($fileRes->status??false)){ throw new \Exception($fileRes->message??'Ошибка чтения директории'); };

            $files = [];
            $zip = new ZipArchive();
            $fileName = 'zipFile.zip';
            if(file_exists(public_path($fileName))) unlink(public_path($fileName));
            if($zip->open(public_path($fileName), \ZipArchive::CREATE) == TRUE){
                foreach(($fileRes->files??[]) AS $file){
                    $files[] = [
                        'name' => $file->name,
                        'src' => $file->src,
                    ];
                    $zip->addFile($fileDirectoryAbs . $file->name, $file->name);
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

    public function removeFiles(Request $request){
        $response = array('status' => false, 'message' => 'Не предвиденная остановка сервера', 'data' => array());
        try {
            $start = microtime(true);
            if (!$request->isMethod('POST')){ throw new \Exception('Некорректное обращение к серверу'); }
            $data = (object)($request->data??[]);
            if (empty($data->arrFileNames)){ throw new \Exception('Не определены файлы'); }
            
            $needleFiles = (array)$data->arrFileNames;
            // $token = $data->_token;
            
            $fileDirectory = config('app.directories.files');
            $countUnlink = 0;

            $fileRes = FileService::getFilesDirectory(directory: $fileDirectory, needleName: $needleFiles);
            if(false == ($fileRes->status??false)){ throw new \Exception($fileRes->message??'Ошибка чтения директории'); };
            foreach(($fileRes->files??[]) AS $file){
                $fileAbs = $_SERVER['DOCUMENT_ROOT'] . $file->src;
                if(!file_exists($fileAbs)) continue;
                unlink($fileAbs);
                $countUnlink++;
            }

            $time = round(microtime(true) - $start, 4);
            $response = array(
                'status' => true, 
                'message' => 'Unlinked', 
                'data' => [
                    'countUnlink' => $countUnlink,
                    'time' => $time,
                ]
            );
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }
        return response()->json($response);
    }   

}
