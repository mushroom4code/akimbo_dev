<?php
namespace ShortPixel\Helper;

use ShortPixel\Model\Image\ImageModel as ImageModel;
use ShortPixel\Controller\ApiKeyController as ApiKeyController;
use ShortPixel\Controller\QuotaController as QuotaController;
use ShortPixel\Controller\OptimizeController as OptimizeController;

use ShortPixel\Model\AccessModel as AccessModel;


class UiHelper
{

	private static $outputMode = 'admin';

	private static $knowledge_url = 'https://shortpixel.com/knowledge-base/search?query='; // the URL of all knowledge.

	public static function setOutputHandler($name)
	{
		 	self::$outputMode = $name;


	}

  public static function renderBurgerList($actions, $imageObj)
  {
    $output = "";
    $id = $imageObj->get('id');
    $primary = isset($actions['optimizethumbs']) ? 'button-primary' : '';

    $output .= "<div class='sp-column-actions '>
                    <div class='sp-dropdown'>
                        <button onclick='ShortPixel.openImageMenu(event);' class='sp-dropbtn button dashicons dashicons-menu $primary' title='ShortPixel Actions'></button>";
    $output .= "<div id='sp-dd-$id' class='sp-dropdown-content'>";

    foreach($actions as $actionName => $actionData)
    {
        $link = ($actionData['type'] == 'js') ? 'javascript:' . $actionData['function'] : $actionData['function'];
        $output .= "<a href='" . $link . "' class='$actionName' >" . $actionData['text'] . "</a>";

    }

    $output .= "</div> <!--sp-dropdown-content--> </div> <!--sp-dropdown--> </div> <!--sp-column-actions--> ";
    return $output;
  }

  public static function renderSuccessText($imageObj)
  {
    $output = '';
    //$percent = $imageObj->getMeta('improvement');
    $percent = $imageObj->getImprovement();

    if($percent == 999) return ;

    if ($percent == 999 )
      $output .= __("Reduced by X%(unknown)", 'shortpixel-image-optimiser');

    if ($percent && $percent > 0)
    {
      $output .= __('Reduced by','shortpixel-image-optimiser') . ' <strong>' . self::formatNumber($percent,2) . '%</strong> ';
    }
    if (intval($percent) < 5)
      $output .= __('Bonus processing','shortpixel-image-optimiser');

    $type = $imageObj->getMeta('compressionType');
    $output .= ' ('. self::compressionTypeToText($type) .')';

    $thumbs = $imageObj->get('thumbnails');
    $thumbsDone = $retinasDone = 0;
    $thumbsTotal = ($thumbs) ? count($thumbs) : 0;

    $retinas = $imageObj->get('retinas');

    $webpsTotal = $imageObj->count('webps');
    $avifsTotal = $imageObj->count('avifs');

    if($retinas)
    {
      foreach($retinas as $retinaObj)
      {
         if ($retinaObj->isOptimized())
         {
           $retinasDone++;
         }
      }
    }

    $improvements = $imageObj->getImprovements();
    $thumbTotal = $thumbsDone = 0;
    if ($imageObj->get('thumbnails'))
    {
      $thumbsTotal = count($imageObj->get('thumbnails'));  //
      $thumbsDone =  (isset($improvements['thumbnails'])) ? count($improvements['thumbnails']) : 0;
    }

    if (isset($improvements['thumbnails']))
    {
       $output .= '<div class="thumbnails optimized">';
       if ($thumbsTotal > $thumbsDone)
         $output .= '<div class="totals">' . sprintf(__('+%s of %s thumbnails optimized','shortpixel-image-optimiser'), self::formatNumber($thumbsDone,0), self::formatNumber($thumbsTotal,0)) . '</div>';
       elseif ($thumbsDone > 0)
         $output .= '<div class="totals">' . sprintf(__('+%s thumbnails optimized','shortpixel-image-optimiser'), self::formatNumber($thumbsDone, 0)) . '</div>';


			 $improvs = array();
			 // Quality Check
			 foreach($improvements['thumbnails'] as $thumbName => $thumbStat)
			 {
				  $stat = $thumbStat[0];
				 	if (is_numeric($stat) && $stat >= 0)
					{
						 $improvs[$thumbName] = $stat; //self::formatNumber($stat,2);
					}

			 }


			 if (count($improvs) > 0)
			 {
		       $output .= "<div class='thumb-wrapper'>";

		       foreach($improvs as $thumbName => $stat)
		       {

						   $statText = self::formatNumber($stat, 2);
		           $title =  sprintf(__('%s : %s', 'shortpixel-image-optimiser'), $thumbName, $statText . '%');
		           $rating = ceil( round($stat) / 10);

		           $blocks_on = str_repeat('<span class="point checked">&nbsp;</span>', $rating);
		           $blocks_off = str_repeat('<span class="point">&nbsp;</span>', (10- $rating));

		           $output .= "<div class='thumb " . $thumbName . "' title='" . $title . "'>"
		                       . "<span class='thumb-name'>" .  $thumbName . '</span>' .
		                        "<span class='optimize-bar'>" . $blocks_on . $blocks_off . "</span>
		                      </div>";

		       }
		       $output .=  "</div></div> <!-- thumb optimized -->";
				}
    }

    if ($retinasDone > 0)
    {
      $output .= '<div class="filetype retina">' . sprintf(__('+%s Retina images optimized','shortpixel-image-optimiser') , $retinasDone) . '</div>';
    }
    if ($webpsTotal > 0)
    {
      $output .=  '<div class="filetype webp">' . sprintf(__('+%s Webp images ','shortpixel-image-optimiser') , $webpsTotal) . '</div>';
    }
    if ($avifsTotal > 0)
    {
        $output .=  '<div class="filetype avif">' . sprintf(__('+%s Avif images ','shortpixel-image-optimiser') , $avifsTotal) . '</div>';
    }
    if ($imageObj->isOptimized() && $imageObj->isProcessable())
    {
        $optimizable = $imageObj->getOptimizeURLS();
        // Todo check if Webp / Acif is active, check for unoptimized items
        $processWebp = ($imageObj->isProcessableFileType('webp')) ? true : false;
        $processAvif = ($imageObj->isProcessableFileType('avif')) ? true : false;

        if (count($optimizable) > 0)
        {
           $output .= '<div class="thumbs-todo"><h4>' . sprintf(__('%d to optimize', 'shortpixel-image-optimiser'), count($optimizable)) . '</h4>';
             $output .= "<span>";
               foreach($optimizable as $optObj)
               {
                  $output .= substr($optObj, strrpos($optObj, '/')+1) . '<br>';
               }
             $output .= "</span>";
           $output .= '</div>';
        }

        if ($processWebp && count($optimizable) == 0)
        {
           $webps = $imageObj->getOptimizeFileType('webp');
           $output .= '<div class="thumbs-todo"><h4>' . sprintf(__('%d Webp files to create', 'shortpixel-image-optimiser'), count($webps)) . '</h4>';
             $output .= "<span>";
               foreach($webps as $optObj)
               {
                  $output .= self::convertImageTypeName(substr($optObj, strrpos($optObj, '/')+1), 'webp') . '<br>';
               }
             $output .= "</span>";
           $output .= '</div>';
        }
        if ($processAvif && count($optimizable) == 0)
        {
            $avifs = $imageObj->getOptimizeFileType('avif');
            $output .= '<div class="thumbs-todo"><h4>' . sprintf(__('%d Avif files to create', 'shortpixel-image-optimiser'), count($avifs)) . '</h4>';
              $output .= "<span>";
                foreach($avifs as $optObj)
                {
                   $output .= self::convertImageTypeName(substr($optObj, strrpos($optObj, '/')+1), 'avif') . '<br>';
                }
              $output .= "</span>";
            $output .= '</div>';
        }
    }



    return $output;

  }




  public static function compressionTypeToText($type)
  {
     if ($type == ImageModel::COMPRESSION_LOSSLESS )
       return __('Lossless', 'shortpixel-image-optimiser');

     if ($type == ImageModel::COMPRESSION_LOSSY )
         return __('Lossy', 'shortpixel-image-optimiser');

     if ($type == ImageModel::COMPRESSION_GLOSSY )
         return __('Glossy', 'shortpixel-image-optimiser');

      return $type;
  }

  public static function getListActions($mediaItem)
  {
      $list_actions = array();
      $id = $mediaItem->get('id');

		  $keyControl = ApiKeyController::getInstance();
			if (! $keyControl->keyIsVerified())
			{
				return array(); // nothing
			}

      $quotaControl = QuotaController::getInstance();

			$access = AccessModel::getInstance();
			if (! $access->imageIsEditable($mediaItem))
			{
				 return array();
			}

			if ($id === 0)
				return array();

      if ($mediaItem->isOptimized())
      {
           $optimizable = $mediaItem->getOptimizeURLS();
           //$webp = $mediaItem->

           if ($mediaItem->isProcessable() && ! $mediaItem->isOptimizePrevented())
           {
             $action = self::getAction('optimizethumbs', $id);
             if (count($optimizable) > 0)
             {
               $action['text']  = sprintf(__('Optimize %s  thumbnails','shortpixel-image-optimiser'),count($optimizable));
             }
             else
             {
                 $optimizableWebp = $mediaItem->isProcessableFileType('webp') ? count($mediaItem->getOptimizeFileType('webp')) : 0;
                 $optimizableAvif = $mediaItem->isProcessableFileType('avif') ? count($mediaItem->getOptimizeFileType('avif')) : 0;

                 if ($optimizableWebp > 0 && $optimizableAvif > 0)
                   $text  = sprintf(__('Optimize %s webps and %s avif','shortpixel-image-optimiser'),$optimizableWebp, $optimizableAvif);
                 elseif ($optimizableWebp > 0)
                   $text  = sprintf(__('Optimize %s webps','shortpixel-image-optimiser'),$optimizableWebp);
                 else
                    $text  = sprintf(__('Optimize %s avifs','shortpixel-image-optimiser'),$optimizableAvif);
                 $action['text'] = $text;
             }
             $list_actions['optimizethumbs'] = $action;
          }


          if ($mediaItem->hasBackup())
          {
            if ($mediaItem->get('type') == 'custom')
            {
                if ($mediaItem->getExtension() !== 'pdf') // no support for this
                  $list_actions[] = self::getAction('compare-custom', $id);
            }
            else
            {
                // PDF without thumbnail can't be compared.
                $showCompare = true;
                if ($mediaItem->getExtension() == 'pdf')
                {
  				            if (! $mediaItem->getThumbnail('full'))
  					               $showCompare = false;
  				            elseif(! $mediaItem->getThumbnail('full')->hasBackup())
  					             $showCompare = false;
  			         }

  				       if ($showCompare)
                   $list_actions[] = self::getAction('compare', $id);
            }
			 			if ($mediaItem->isRestorable())
						{

		           switch($mediaItem->getMeta('compressionType'))
		           {
		               case ImageModel::COMPRESSION_LOSSLESS:
		                 $list_actions['reoptimize-lossy'] = self::getAction('reoptimize-lossy', $id);
		                 $list_actions['reoptimize-glossy'] = self::getAction('reoptimize-glossy', $id);
		               break;
		               case ImageModel::COMPRESSION_LOSSY:
		                 $list_actions['reoptimize-lossless'] = self::getAction('reoptimize-lossless', $id);
		                 $list_actions['reoptimize-glossy'] = self::getAction('reoptimize-glossy', $id);
		               break;
		               case ImageModel::COMPRESSION_GLOSSY:
		                 $list_actions['reoptimize-lossy'] = self::getAction('reoptimize-lossy', $id);
		                 $list_actions['reoptimize-lossless'] = self::getAction('reoptimize-lossless', $id);
		               break;
		           }


		          		$list_actions['restore'] = self::getAction('restore', $id);
							} // isRestorable
						else
						{
							 //echo $mediaItem->getReason('restorable');
						}
        } // hasBackup

      } //isOptimized

      if(! $quotaControl->hasQuota())
      {
         $remove = array('reoptimize-lossy' => '', 'reoptimize-glossy' => '', 'reoptimize-lossless' => '', 'optimizethumbs' => '');
         $list_actions = array_diff_key($list_actions, $remove);

      }
      return $list_actions;
  }

  public static function getActions($mediaItem)
  {
    $actions = array();
    $id = $mediaItem->get('id');
    $quotaControl = QuotaController::getInstance();
		$optimizeController = new OptimizeController();

		$keyControl = ApiKeyController::getInstance();
		if (! $keyControl->keyIsVerified())
		{
			return array(); // nothing
		}

		$access = AccessModel::getInstance();
		if (! $access->imageIsEditable($mediaItem))
		{
			 return array();
		}

		if ($id === 0)
			return array();

    if(! $quotaControl->hasQuota())
    {
       $actions['extendquota'] = self::getAction('extendquota', $id);
       $actions['checkquota'] = self::getAction('checkquota', $id);
    }
    elseif($mediaItem->isProcessable(true) && ! $mediaItem->isOptimized() && ! $mediaItem->isOptimizePrevented() && ! $optimizeController->isItemInQueue($mediaItem))
    {
       $actions['optimize'] = self::getAction('optimize', $id);
    }



    return $actions;
  }

  public static function getStatusText($mediaItem)
  {
    $keyControl = ApiKeyController::getInstance();
    $quotaControl = QuotaController::getInstance();
		$optimizeController = new OptimizeController();

    $text = '';

    if (! $keyControl->keyIsVerified())
    {
      $text = __('Invalid API Key. <a href="options-general.php?page=wp-shortpixel-settings">Check your Settings</a>','shortpixel-image-optimiser');
    }
		// This basically happens when a NextGen gallery is not added to Custom Media.
		elseif ($mediaItem->get('id') === 0)
		{
			 $text = __('This image was not found in our database. Refresh folders, or add this gallery', 'shortpixel-image-optimiser');
		}
    elseif ($mediaItem->isOptimized())
    {
       $text = UiHelper::renderSuccessText($mediaItem);
    }
    elseif (! $mediaItem->isProcessable(true) && ! $mediaItem->isOptimized())
    {
       $text = __('Not Processable: ','shortpixel_image_optimiser');
       $text  .= $mediaItem->getProcessableReason();

    }
    elseif (! $mediaItem->exists())
    {
       $text = __('File does not exist.','shortpixel-image-optimiser');
    }
    elseif ($mediaItem->getMeta('status') < 0)
    {
      $text = $mediaItem->getMeta('errorMessage');
    }
		elseif( $optimizeController->isItemInQueue($mediaItem) === true)
		{
			 $text = __('This item is waiting to be processed', 'shortpixel-image-optimiser');
		}

      if ($mediaItem->isOptimizePrevented() !== false)
      {

          $retry = self::getAction('retry', $mediaItem->get('id'));
          $text .= "<div class='shortpixel-image-error'>" . esc_html($mediaItem->isOptimizePrevented());
          $text .= "<span class='shortpixel-error-reset'>" . sprintf(__('After you have fixed this issue, you can %s click here to retry %s', 'shortpixel-image-optimiser'), '<a href="javascript:' . $retry['function'] . '">', '</a>');
          $text .= '</div>';
      }

    return $text;
  }

  public static function getAction($name, $id)
  {
     $action = array('function' => '', 'type' => '', 'text' => '', 'display' => '');
     $keyControl = ApiKeyController::getInstance();

    switch($name)
    {
      case 'optimize':
         $action['function'] = 'window.ShortPixelProcessor.screen.Optimize(' . $id . ')';
         $action['type']  = 'js';
         $action['text'] = __('Optimize Now', 'shortpixel-image-optimiser');
         $action['display'] = 'button';
      break;
      case 'optimizethumbs':
          $action['function'] = 'window.ShortPixelProcessor.screen.Optimize(' . $id . ');';
          $action['type'] = 'js';
          $action['text']  = '';
          $action['display'] = 'inline';
      break;

      case 'retry':
         $action['function'] = 'window.ShortPixelProcessor.screen.Optimize(' . $id . ');';
         $action['type']  = 'js';
         $action['text'] = __('Retry', 'shortpixel-image-optimiser') ;
         $action['display'] = 'button';
     break;

     case 'restore':
         $action['function'] = 'window.ShortPixelProcessor.screen.RestoreItem(' . $id . ');';
         $action['type'] = 'js';
         $action['text'] = __('Restore backup','shortpixel-image-optimiser');
         $action['display'] = 'inline';
     break;

     case 'compare':
        $action['function'] = 'ShortPixel.loadComparer(' . $id . ')';
        $action['type'] = 'js';
        $action['text'] = __('Compare', 'shortpixel-image-optimiser');
        $action['display'] = 'inline';
     break;
     case 'compare-custom':
        $action['function'] = 'ShortPixel.loadComparer(' . $id . ',"custom")';
        $action['type'] = 'js';
        $action['text'] = __('Compare', 'shortpixel-image-optimiser');
        $action['display'] = 'inline';
     break;
     case 'reoptimize-glossy':
        $action['function'] = 'window.ShortPixelProcessor.screen.ReOptimize(' . $id . ',' . ImageModel::COMPRESSION_GLOSSY . ')';
        $action['type'] = 'js';
        $action['text'] = __('Re-optimize Glossy','shortpixel-image-optimiser') ;
        $action['display'] = 'inline';
     break;
     case 'reoptimize-lossy':
        $action['function'] = 'window.ShortPixelProcessor.screen.ReOptimize(' . $id . ',' . ImageModel::COMPRESSION_LOSSY . ')';
        $action['type'] = 'js';
        $action['text'] = __('Re-optimize Lossy','shortpixel-image-optimiser');
        $action['display'] = 'inline';
     break;

     case 'reoptimize-lossless':
        $action['function'] = 'window.ShortPixelProcessor.screen.ReOptimize(' . $id . ',' . ImageModel::COMPRESSION_LOSSLESS . ')';
        $action['type'] = 'js';
        $action['text'] = __('Re-optimize Lossless','shortpixel-image-optimiser');
        $action['display'] = 'inline';
     break;

     case 'extendquota':
        $action['function'] = 'https://shortpixel.com/login'. $keyControl->getKeyForDisplay();
        $action['type'] = 'button';
        $action['text'] = __('Extend Quota','shortpixel-image-optimiser');
        $action['display'] = 'button';
     break;
     case 'checkquota':
        $action['function'] = 'ShortPixel.checkQuota()';
        $action['type'] = 'js';
        $action['display'] = 'button';
        $action['text'] = __('Check&nbsp;&nbsp;Quota','shortpixel-image-optimiser');

     break;
   }

   return $action;
  }

	public static function getKBSearchLink($subject)
	{
			return esc_url(self::$knowledge_url . sanitize_text_field($subject));
	}

	// @param MediaLibraryModel Object $imageItem
	// @param String $size  Preferred size
	// @param String Preload The preloader tries to guess what the preview might be for a more smooth process. Ignore optimize / backup
	public static function findBestPreview($imageItem, $size = 800, $preload = false)
	{
		 	$closestObj = $imageItem;

			// set the standard.
			if ($imageItem->getExtension() == 'pdf') // try not to select non-showable extensions.
				$bestdiff = 0;
			else
				$bestdiff = abs($imageItem->get('width') - $size);

			$thumbnails = $imageItem->get('thumbnails');

			if (! is_array($thumbnails))
			{
				 return $closestObj; // nothing more to do.
			}

			foreach($thumbnails as $thumbnail)
			{
				 if (! $preload && (! $thumbnail->isOptimized() || ! $thumbnail->hasBackup()))
				 	continue;

					$diff = abs($thumbnail->get('width') - $size);
					if ($diff < $bestdiff)
					{
						 $closestObj = $thumbnail;
						 $bestdiff = $diff;
					}
			}

			return $closestObj;
	}

  public static function formatTS($ts)
  {
      //$format = get_option('date_format') .' @ ' . date_i18n(get_option('time_format');
			if (function_exists('wp_date'))
			{
      	$date = wp_date(get_option('date_format'), $ts);
				$date .= ' @ ' . wp_date(get_option('time_format'), $ts);
			}
			else
			{
      	$date = date_i18n(get_option('date_format'), $ts);
				$date .= ' @ ' . date_i18n(get_option('time_format'), $ts);

			}
      return $date;
  }

  public static function formatBytes($bytes, $precision = 2) {
      $units = array('B', 'KB', 'MB', 'GB', 'TB');

      $bytes = max($bytes, 0);
      $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
      $pow = min($pow, count($units) - 1);

      $bytes /= pow(1024, $pow);

      return number_format_i18n(round($bytes, $precision), $precision) . ' ' . $units[$pow];
  }

	public static function formatNumber($number, $precision = 2)
	{
		  return  number_format_i18n( (int) $number, $precision);
	}

	protected static function convertImageTypeName($name, $type)
	{
		if ($type == 'webp')
		{
			$is_double = \wpSPIO()->env()->useDoubleWebpExtension();
		}
		if ($type == 'avif')
		{
			$is_double = \wpSPIO()->env()->useDoubleAvifExtension();
		}

		if ($is_double)
		{
			 return $name . '.' . $type;
		}
		else
		{
			 return substr($name, 0, strpos($name, '.')) . '.' . $type;
		}

	}



} // class
