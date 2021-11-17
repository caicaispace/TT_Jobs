<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Utility;

class File
{
    public static function createDir($dirPath)
    {
        if (! is_dir($dirPath)) {
            try {
                return mkdir($dirPath, 0755, true);
            } catch (\Exception $exception) {
                return false;
            }
        } else {
            return true;
        }
    }

    public static function deleteDir($dirPath)
    {
        if (self::clearDir($dirPath)) {
            try {
                return rmdir($dirPath);
            } catch (\Exception $exception) {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function clearDir($dirPath)
    {
        if (! is_dir($dirPath)) {
            return false;
        }
        try {
            $dirHandle = opendir($dirPath);
            if (! $dirHandle) {
                return false;
            }
            while (false !== ($file = readdir($dirHandle))) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if (! is_dir($dirPath . '/' . $file)) {
                    if (! self::deleteFile($dirPath . '/' . $file)) {
                        closedir($dirHandle);
                        return false;
                    }
                } else {
                    if (! self::deleteDir($dirPath . '/' . $file)) {
                        closedir($dirHandle);
                        return false;
                    }
                }
            }
            closedir($dirHandle);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public static function copyDir($dirPath, $targetPath, $overwrite = true)
    {
        if (! is_dir($dirPath)) {
            return false;
        }
        if (! file_exists($targetPath)) {
            if (! self::createDir($targetPath)) {
                return false;
            }
        }
        try {
            $dirHandle = opendir($dirPath);
            if (! $dirHandle) {
                return false;
            }
            while (false !== ($file = readdir($dirHandle))) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if (! is_dir($dirPath . '/' . $file)) {
                    if (! self::copyFile($dirPath . '/' . $file, $targetPath . '/' . $file, $overwrite)) {
                        closedir($dirHandle);
                        return false;
                    }
                } else {
                    if (! self::copyDir($dirPath . '/' . $file, $targetPath . '/' . $file, $overwrite)) {
                        closedir($dirHandle);
                        return false;
                    }
                }
            }
            closedir($dirHandle);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public static function moveDir($dirPath, $targetPath, $overwrite = true)
    {
        try {
            if (self::copyDir($dirPath, $targetPath, $overwrite)) {
                return self::deleteDir($dirPath);
            }
            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public static function createFile($filePath, $overwrite = true)
    {
        if (file_exists($filePath) && $overwrite == false) {
            return false;
        }
        if (file_exists($filePath) && $overwrite == true) {
            if (! self::deleteFile($filePath)) {
                return false;
            }
        }
        $aimDir = dirname($filePath);
        if (self::createDir($aimDir)) {
            try {
                return touch($filePath);
            } catch (\Exception $exception) {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function saveFile($filePath, $content, $overwrite = true)
    {
        if (self::createFile($filePath, $overwrite)) {
            return file_put_contents($filePath, $content);
        }
        return false;
    }

    public static function copyFile($filePath, $targetFilePath, $overwrite = true)
    {
        if (! file_exists($filePath)) {
            return false;
        }
        if (file_exists($targetFilePath) && $overwrite == false) {
            return false;
        }
        if (file_exists($targetFilePath) && $overwrite == true) {
            if (! self::deleteFile($targetFilePath)) {
                return false;
            }
        }
        $aimDir = dirname($filePath);
        if (! self::createDir($aimDir)) {
            return false;
        }
        return copy($filePath, $targetFilePath);
    }

    public static function moveFile($filePath, $targetFilePath, $overwrite = true)
    {
        if (! file_exists($filePath)) {
            return false;
        }
        if (file_exists($targetFilePath) && $overwrite == false) {
            return false;
        }
        if (file_exists($targetFilePath) && $overwrite == true) {
            if (! self::deleteFile($targetFilePath)) {
                return false;
            }
        }
        $targetDir = dirname($targetFilePath);
        if (! self::createDir($targetDir)) {
            return false;
        }
        return rename($filePath, $targetFilePath);
    }

    public static function deleteFile($filePath)
    {
        try {
            unlink($filePath);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param $dirPath
     * @param string $filterType 'file'|'dir'
     * @return array|bool
     */
    public static function scanDir($dirPath, $filterType = 'file')
    {
        $res = [];
        if (is_dir($dirPath)) {
            try {
                $dirHandle = opendir($dirPath);
                if (! $dirHandle) {
                    return false;
                }
                while (false !== ($file = readdir($dirHandle))) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }
                    if ($filterType == 'file') {
                        if (! is_dir($dirPath . '/' . $file)) {
                            $res[] = $dirPath . '/' . $file;
                        }
                    } else {
                        if (is_dir($dirPath . '/' . $file)) {
                            $res[] = $dirPath . '/' . $file;
                        }
                    }
                }
                closedir($dirHandle);
                return $res;
            } catch (\Exception $exception) {
                return false;
            }
        } else {
            return false;
        }
    }
}
