<?php

class Application_Model_Documentos
{
	/**
	 * 
	 * @param string $path
	 * @param string $id
	 */
    public function readDir($path, $id)
    {
        
        if(!is_dir($path)){
            return false;
        };
        
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

    /**
     *
     * @param array|string $names            
     */
    public function removeFiles($names, $path = 'uploads')
    {
        $path = PUBLIC_PATH . DIRECTORY_SEPARATOR . $path;
        $arrayReturn = array();
        
        if (is_array($names)) {
            foreach ($names as $name) {
                $arrayReturn[] = array(
                    'name' => $name,
                    'removed' => unlink($path . DIRECTORY_SEPARATOR . $name)
                );
            }
        } else {
            $arrayReturn[] = array(
                'name' => $names,
                'removed' => unlink($path . DIRECTORY_SEPARATOR . $names)
            );
        }
        
        return $arrayReturn;
    }

    public function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        
        return $bytes;
    }
}