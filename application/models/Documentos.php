<?php

class Application_Model_Documentos
{

    public function readDir($path, $id)
    {
        $dir = new DirectoryIterator($path);
        $fileArray = array();
        
        foreach ($dir as $fileInfo) {
            
            $name = explode('-', $fileInfo->getFilename());
            
            if ($fileInfo->getType() == 'file') {
                if ($id == $name[0]) {
                    $fileArray[] = array(
                        'name' => $fileInfo->getFilename(),
                        'date' => $fileInfo->getCTime(),
                        'size' => $fileInfo->getSize(),
                        'path' => $fileInfo->getPath(),
                        'extension' => $fileInfo->getExtension()
                    );
                } else 
                    if ($id == null) {
                        $fileArray[] = array(
                            'name' => $fileInfo->getFilename(),
                            'date' => $fileInfo->getCTime(),
                            'size' => $fileInfo->getSize(),
                            'path' => $fileInfo->getPath(),
                            'extension' => $fileInfo->getExtension()
                        );
                    }
            }
        }
        
        return $fileArray;
    }
    
    function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }
    
        return $bytes;
    }
}