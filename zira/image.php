<?php
/**
 * Zira project
 * image.php
 * (c)2015 http://dro1d.ru
 */

namespace Zira;

class Image {
    const EXT_JPEG = 'jpg';
    const EXT_PNG = 'png';
    const EXT_GIF = 'gif';

    const QUALITY_JPEG = 90;
    const QUALITY_PNG = 0;

    protected static function _imagecreate($src_path, &$src_image, &$type, &$size) {
        $size=@getimagesize($src_path);
        if (!$size) return false;

        switch ($size[2])
        {
            case IMAGETYPE_JPEG :
                $src_image=imagecreatefromjpeg($src_path);
                $type=self::EXT_JPEG;
                break;
            case IMAGETYPE_PNG :
                $src_image=imagecreatefrompng($src_path);
                $type=self::EXT_PNG;
                break;
            case IMAGETYPE_GIF :
                $src_image=imagecreatefromgif($src_path);
                $type=self::EXT_GIF;
                break;
        }

        if (!$src_image) return false;
        return true;
    }

    protected static function _imagesave($dst_image, $dst_path, $type) {
        $result = false;
        if ($type == self::EXT_JPEG) $result = imagejpeg($dst_image, $dst_path, self::QUALITY_JPEG);
        else if ($type == self::EXT_PNG) $result = imagepng($dst_image, $dst_path, self::QUALITY_PNG);
        else if ($type == self::EXT_GIF) $result = imagegif($dst_image, $dst_path);
        return $result;
    }

    public static function resize($src_path, $dst_path, $dst_width=null, $dst_height=null, $dst_type = null) {
        if ($dst_width == null && $dst_height == null) return false;

        $type = false;
        $src_image=null;
        $size = false;

        if (!self::_imagecreate($src_path, $src_image, $type, $size)) return false;
        if ($dst_type !== null) $type=$dst_type;

        $src_width=$size[0];
        $src_height=$size[1];
        $src_x=0;
        $src_y=0;

        if ($dst_width === null) {
            $dst_width = $src_width * $dst_height / $src_height;
        } else if ($dst_height === null) {
            $dst_height = $src_height * $dst_width / $src_width;
        }

        $dst_image=imagecreatetruecolor($dst_width,$dst_height);
        if (!$dst_image) return false;

        $bg_color=imagecolorallocate($dst_image, 255, 255, 255);
        imagefill($dst_image, 0, 0, $bg_color);

        if (!imagecopyresampled($dst_image, $src_image, 0, 0, $src_x, $src_y, $dst_width, $dst_height, $src_width, $src_height)) return false;

        $result = self::_imagesave($dst_image, $dst_path, $type);

        imagedestroy($src_image);
        imagedestroy($dst_image);

        return $result;
    }

    public static function crop($src_path, $dst_path, $width_percent, $height_percent, $left_percent, $top_percent, $dst_type = null) {
        $type = false;
        $src_image=null;
        $size = false;

        if (!self::_imagecreate($src_path, $src_image, $type, $size)) return false;
        if ($dst_type !== null) $type=$dst_type;

        $src_width=$size[0];
        $src_height=$size[1];

        $dst_width = $src_width * $width_percent / 100;
        $dst_height = $src_height * $height_percent / 100;

        $src_x = $src_width * $left_percent / 100;
        $src_y = $src_height * $top_percent / 100;
        $src_width = $dst_width;
        $src_height = $dst_height;

        $dst_image=imagecreatetruecolor($dst_width,$dst_height);
        if (!$dst_image) return false;

        $bg_color=imagecolorallocate($dst_image, 255, 255, 255);
        imagefill($dst_image, 0, 0, $bg_color);

        if (!imagecopyresampled($dst_image, $src_image, 0, 0, $src_x, $src_y, $dst_width, $dst_height, $src_width, $src_height)) return false;

        $result = self::_imagesave($dst_image, $dst_path, $type);

        imagedestroy($src_image);
        imagedestroy($dst_image);

        return $result;
    }

    public static function cut($src_path, $dst_path, $width, $height, $left, $top, $dst_type = null) {
        $type = false;
        $src_image=null;
        $size = false;

        if (!self::_imagecreate($src_path, $src_image, $type, $size)) return false;
        if ($dst_type !== null) $type=$dst_type;

        $dst_width = $width;
        $dst_height = $height;

        $src_x = $left;
        $src_y = $top;
        $src_width = $width;
        $src_height = $height;

        $dst_image=imagecreatetruecolor($dst_width,$dst_height);
        if (!$dst_image) return false;

        $bg_color=imagecolorallocate($dst_image, 255, 255, 255);
        imagefill($dst_image, 0, 0, $bg_color);

        if (!imagecopyresampled($dst_image, $src_image, 0, 0, $src_x, $src_y, $dst_width, $dst_height, $src_width, $src_height)) return false;

        $result = self::_imagesave($dst_image, $dst_path, $type);

        imagedestroy($src_image);
        imagedestroy($dst_image);

        return $result;
    }

    public static function createThumb($src_path, $dst_path, $dst_width, $dst_height, $dst_type = null) {
        $type = false;
        $src_image=null;
        $size = false;

        if (!self::_imagecreate($src_path, $src_image, $type, $size)) return false;
        if ($dst_type !== null) $type=$dst_type;

        $src_width=$size[0];
        $src_height=$size[1];
        $src_x=0;
        $src_y=0;

        $s = $src_width / $src_height;
        $p = $dst_width / $dst_height;

        if ($s >= $p) {
            $_src_width = $src_height * $p;
            $src_x = ($src_width - $_src_width) / 2;
            $src_width = $_src_width;
        } else {
            $_src_height = $src_width / $p;
            $src_y = ($src_height - $_src_height) / 2;
            $src_height = $_src_height;
        }

        $dst_image=imagecreatetruecolor($dst_width,$dst_height);
        if (!$dst_image) return false;

        $bg_color=imagecolorallocate($dst_image, 255, 255, 255);
        imagefill($dst_image, 0, 0, $bg_color);

        if (!imagecopyresampled($dst_image, $src_image, 0, 0, $src_x, $src_y, $dst_width, $dst_height, $src_width, $src_height)) return false;

        $result = self::_imagesave($dst_image, $dst_path, $type);

        imagedestroy($src_image);
        imagedestroy($dst_image);

        return $result;
    }

    public static function save(array $file, $dir = null, $create_thumb = false, $watermark = false) {
        $savedir = IMAGES_DIR;
        if (!empty($dir)) $savedir .= DIRECTORY_SEPARATOR . $dir;
        $files = File::save($file, $savedir);
        if (!$files) return false;
        if ($create_thumb) {
            $savedir = THUMBS_DIR;
            if (!empty($dir)) $savedir .= DIRECTORY_SEPARATOR . $dir;
            $save_path = File::getAbsolutePath($savedir);
            foreach($files as $path=>$name) {
                if (!self::createThumb($path, $save_path . DIRECTORY_SEPARATOR . $name, Config::get('thumbs_width'), Config::get('thumbs_height'))) return false;
            }
        }
        if ($watermark) {
            $savedir = IMAGES_DIR;
            if (!empty($dir)) $savedir .= DIRECTORY_SEPARATOR . $dir;
            $save_path = File::getAbsolutePath($savedir);
            foreach($files as $name) {
                if (!self::watermark($save_path . DIRECTORY_SEPARATOR . $name)) break;
            }
        }
        return $files;
    }

    public static function watermark($src_path, $margin = 10) {
        $watermark_path = Config::get('watermark');
        if (empty($watermark_path)) return false;
        else $watermark_path = ROOT_DIR . DIRECTORY_SEPARATOR . $watermark_path;
        if (!file_exists($watermark_path)) return false;

        $type = false;
        $src_image=null;
        $size = false;

        if (!self::_imagecreate($src_path, $src_image, $type, $size)) return false;

        $src_width=$size[0];
        $src_height=$size[1];

        $watermark_type = false;
        $watermark_image=null;
        $size = false;

        if (!self::_imagecreate($watermark_path, $watermark_image, $watermark_type, $size)) return false;

        $watermark_width=$size[0];
        $watermark_height=$size[1];

        if (!imagecopy(
            $src_image,
            $watermark_image,
            $src_width - $watermark_width - $margin,
            $src_height - $watermark_height - $margin,
            0,
            0,
            $watermark_width,
            $watermark_height
        )) return false;

        $result = self::_imagesave($src_image, $src_path, $type);

        imagedestroy($src_image);
        imagedestroy($watermark_image);

        return $result;
    }
}