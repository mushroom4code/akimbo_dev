<?php

class WebP extends WP_Image_Editor
{
	protected $file              = null;
	protected $size              = null;
	protected $mime_type         = null;
	protected $output_mime_type  = null;
	protected $default_mime_type = 'image/jpeg';
	protected $quality           = false;

	// Deprecated since 5.8.1. See get_default_quality() below.
	protected $default_quality = 82;

	/**
	 * Each instance handles a single file.
	 *
	 * @param string $file Path to the file to load.
	 */
	public function __construct( $file ) {
		parent::__construct($file);
		$this->file = $file;
	}

//	protected static function makeWebP($filename, $newfilename, $filetype)
//    {
//        $r = false;
//        switch ($filetype)
//        {
//            case 'image/jpeg':
//                $contents = imagecreatefromjpeg($filename);
//                break;
//            case 'image/jpg':
//                $contents = imagecreatefromjpeg($filename);
//                break;
//            case 'image/bmp':
//                $contents = imagecreatefrombmp($filename);
//                break;
//            case 'image/gif':
//                $contents = imagecreatefromgif($filename);
//                break;
//            case 'image/png':
//                $contents = imagecreatefrompng($filename);
//                break;
//        }
//        $r = imagewebp($contents, $newfilename);
//        return $r;
//    }
//
//    public static function ResizeImageWebPGet($file, $width = 0, $height = 0)
//    {
//        if (!is_array($file) && intval($file) > 0)
//        {
//            $file = \CFile::GetFileArray($file);
//        }
//        if($width > 0 && $height > 0)
//        {
//            $image_src = $_SERVER['DOCUMENT_ROOT'] . $file['SRC'];
//            $tmp_image = $_SERVER['DOCUMENT_ROOT'] . '/upload/' . $file['FILE_NAME'];
//
//            \CFile::ResizeImageFile(
//                $image_src,
//                $tmp_image,
//                array('width'=>$width, 'height'=>$height),
//                BX_RESIZE_IMAGE_PROPORTIONAL
//            );
//            unlink($image_src);
//            rename($tmp_image, $image_src);
//        }
//        if (!is_array($file) || !array_key_exists("FILE_NAME", $file) || strlen($file["FILE_NAME"]) <= 0)
//            return false;
//
//        $uploadDirName = \COption::GetOptionString("main", "upload_dir", "upload");
//
//        $imageFile = "/".$uploadDirName."/".$file["SUBDIR"]."/".$file["FILE_NAME"];
//        $arImageSize = false;
//
//        $io = \CBXVirtualIo::GetInstance();
//        $fn = explode('.', $file["FILE_NAME"]);
//        if($width > 0 && $height > 0)
//        {
//            $cacheImageFile = "/".$uploadDirName."/resize_cache/".$file["SUBDIR"]."/".$fn[0]."_".$width."_".$height.".webp";
//        } else {
//            $cacheImageFile = "/".$uploadDirName."/resize_cache/".$file["SUBDIR"]."/".$fn[0].".webp";
//        }
//
//
//        $cacheImageFileCheck = $cacheImageFile;
//
//        static $cache = array();
//        $cache_id = $cacheImageFileCheck;
//        if(isset($cache[$cache_id]))
//        {
//            return $cache[$cache_id];
//        }
//        elseif (!file_exists($io->GetPhysicalName($_SERVER["DOCUMENT_ROOT"].$cacheImageFileCheck)))
//        {
//            /****************************** QUOTA ******************************/
//            $bDiskQuota = true;
//            if (\COption::GetOptionInt("main", "disk_space") > 0)
//            {
//                $quota = new \CDiskQuota();
//                $bDiskQuota = $quota->checkDiskQuota($file);
//            }
//            /****************************** QUOTA ******************************/
//
//            if ($bDiskQuota)
//            {
//
//                $sourceImageFile = $_SERVER["DOCUMENT_ROOT"].$imageFile;
//                $cacheImageFileTmp = $_SERVER["DOCUMENT_ROOT"].$cacheImageFile;
//
//                if (self::makeWebP($sourceImageFile, $cacheImageFileTmp, $file["CONTENT_TYPE"]))
//                {
//                    $cacheImageFile = substr($cacheImageFileTmp, strlen($_SERVER["DOCUMENT_ROOT"]));
//
//                    /****************************** QUOTA ******************************/
//                    if (\COption::GetOptionInt("main", "disk_space") > 0)
//                        \CDiskQuota::updateDiskQuota("file", filesize($io->GetPhysicalName($cacheImageFileTmp)), "insert");
//                    /****************************** QUOTA ******************************/
//                }
//                else
//                {
//                    $cacheImageFile = $imageFile;
//                }
//            }
//            else
//            {
//                $cacheImageFile = $imageFile;
//            }
//
//            $cacheImageFileCheck = $cacheImageFile;
//        }
//
//        if (!is_array($arImageSize))
//        {
//            $arImageSize = \CFile::GetImageSize($_SERVER["DOCUMENT_ROOT"].$cacheImageFileCheck);
//
//            $f = $io->GetFile($_SERVER["DOCUMENT_ROOT"].$cacheImageFileCheck);
//            $arImageSize[2] = $f->GetFileSize();
//        }
//
//        $cache[$cache_id] = array(
//            "src" => $cacheImageFileCheck,
//            "width" => intval($arImageSize[0]),
//            "height" => intval($arImageSize[1]),
//            "size" => $arImageSize[2],
//        );
//        return $cache[$cache_id];
//    }




	/**
	 * Checks to see if current environment supports the editor chosen.
	 * Must be overridden in a subclass.
	 *
	 * @since 3.5.0
	 *
	 * @abstract
	 *
	 * @param array $args
	 * @return bool
	 */
	public static function test( $args = array() ): bool {
		return false;
	}

	/**
	 * Checks to see if editor supports the mime-type specified.
	 * Must be overridden in a subclass.
	 *
	 * @since 3.5.0
	 *
	 * @abstract
	 *
	 * @param string $mime_type
	 * @return bool
	 */
	public static function supports_mime_type( $mime_type ): bool {
		return false;
	}

	/**
	 * Loads image from $this->file into editor.
	 *
	 * @since 3.5.0
	 * @abstract
	 *
	 * @return bool|true True if loaded; WP_Error on failure.
	 */
	public function load(): bool {
		return true;
	}

	/**
	 * Saves current image to file.
	 *
	 * @since 3.5.0
	 * @abstract
	 *
	 * @param string $destfilename Optional. Destination filename. Default null.
	 * @param string $mime_type    Optional. The mime-type. Default null.
	 * @return array {'path'=>string, 'file'=>string, 'width'=>int, 'height'=>int, 'mime-type'=>string}
	 */
	public function save( $destfilename = null, $mime_type = null ): array {
		return [];
	}

	/**
	 * Resizes current image.
	 *
	 * At minimum, either a height or width must be provided.
	 * If one of the two is set to null, the resize will
	 * maintain aspect ratio according to the provided dimension.
	 *
	 * @param int|null $max_w Image width.
	 * @param int|null $max_h Image height.
	 * @param bool     $crop
	 *
	 * @return bool|true
	 *@since 3.5.0
	 * @abstract
	 *
	 */
	public function resize( $max_w, $max_h, $crop = false ): bool {
		return true;
	}

	/**
	 * Resize multiple images from a single source.
	 *
	 * @since 3.5.0
	 * @abstract
	 *
	 * @param array $sizes {
	 *     An array of image size arrays. Default sizes are 'small', 'medium', 'large'.
	 *
	 *     @type array $size {
	 *         @type int  $width  Image width.
	 *         @type int  $height Image height.
	 *         @type bool $crop   Optional. Whether to crop the image. Default false.
	 *     }
	 * }
	 * @return array An array of resized images metadata by size.
	 */
	public function multi_resize( $sizes ): array {
		return [];
	}

	/**
	 * Crops Image.
	 *
	 * @param int  $src_x   The start x position to crop from.
	 * @param int  $src_y   The start y position to crop from.
	 * @param int  $src_w   The width to crop.
	 * @param int  $src_h   The height to crop.
	 * @param int  $dst_w   Optional. The destination width.
	 * @param int  $dst_h   Optional. The destination height.
	 * @param bool $src_abs Optional. If the source crop points are absolute.
	 *
	 * @return bool|true
	 *@since 3.5.0
	 * @abstract
	 *
	 */
	public function crop( $src_x, $src_y, $src_w, $src_h, $dst_w = null, $dst_h = null, $src_abs = false ): bool {
		return true;
	}

	/**
	 * Rotates current image counter-clockwise by $angle.
	 *
	 * @param float $angle
	 *
	 * @return bool|true
	 *@since 3.5.0
	 * @abstract
	 *
	 */
	public function rotate( $angle ): bool {
		return true;
	}

	/**
	 * Flips current image.
	 *
	 * @param bool $horz Flip along Horizontal Axis
	 * @param bool $vert Flip along Vertical Axis
	 *
	 * @return bool|true
	 *@since 3.5.0
	 * @abstract
	 *
	 */
	public function flip( $horz, $vert ): bool {
		return true;
	}

	/**
	 * Streams current image to browser.
	 *
	 * @since 3.5.0
	 * @abstract
	 *
	 * @param string $mime_type The mime type of the image.
	 * @return bool|true True on success, WP_Error object on failure.
	 */
	public function stream( $mime_type = null ): bool {
		return true;
	}

	/**
	 * Gets dimensions of image.
	 *
	 * @since 3.5.0
	 *
	 * @return int[]|null {
	 *     Dimensions of the image.
	 *
	 *     @type int $width  The image width.
	 *     @type int $height The image height.
	 * }
	 */
	public function get_size(): ?array {
		return $this->size;
	}

	/**
	 * Sets current image size.
	 *
	 * @since 3.5.0
	 *
	 * @param int $width
	 * @param int $height
	 * @return true
	 */
	protected function update_size( $width = null, $height = null ): bool {
		$this->size = array(
			'width'  => (int) $width,
			'height' => (int) $height,
		);
		return true;
	}

	/**
	 * Gets the Image Compression quality on a 1-100% scale.
	 *
	 * @since 4.0.0
	 *
	 * @return bool Compression Quality. Range: [1,100]
	 */
	public function get_quality(): bool {
		if ( ! $this->quality ) {
			$this->set_quality();
		}

		return $this->quality;
	}

	/**
	 * Returns the default compression quality setting for the mime type.
	 *
	 * @since 5.8.1
	 *
	 * @param string $mime_type
	 * @return int The default quality setting for the mime type.
	 */
	protected function get_default_quality( $mime_type ): int {
		switch ( $mime_type ) {
			case 'image/webp':
				$quality = 86;
				break;
			case 'image/jpeg':
			default:
				$quality = $this->default_quality;
		}

		return $quality;
	}


	/**
	 * Builds and returns proper suffix for file based on height and width.
	 *
	 * @since 3.5.0
	 *
	 * @return string|false suffix
	 */
	public function get_suffix() {
		if ( ! $this->get_size() ) {
			return false;
		}

		return "{$this->size['width']}x{$this->size['height']}";
	}


	/**
	 * Returns first matched mime-type from extension,
	 * as mapped from wp_get_mime_types()
	 *
	 * @since 3.5.0
	 *
	 * @param string $extension
	 * @return string|false
	 */
	protected static function get_mime_type( $extension = null ) {
		if ( ! $extension ) {
			return false;
		}

		$mime_types = wp_get_mime_types();
		$extensions = array_keys( $mime_types );

		foreach ( $extensions as $_extension ) {
			if ( preg_match( "/{$extension}/i", $_extension ) ) {
				return $mime_types[ $_extension ];
			}
		}

		return false;
	}

	/**
	 * Returns first matched extension from Mime-type,
	 * as mapped from wp_get_mime_types()
	 *
	 * @since 3.5.0
	 *
	 * @param string $mime_type
	 * @return string|false
	 */
	protected static function get_extension( $mime_type = null ) {
		if ( empty( $mime_type ) ) {
			return false;
		}

		return wp_get_default_extension_for_mime_type( $mime_type );
	}
}