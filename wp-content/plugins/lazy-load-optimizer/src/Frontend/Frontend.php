<?php namespace LazyLoadOptimizer\Frontend;

use Premmerce\SDK\V2\FileManager\FileManager;
use LazyLoadOptimizer\LazyLoadOptimizerPlugin;
use  LazyLoadOptimizer\Admin\Settings;

/**
 * Class Frontend
 *
 * @package LazyLoadOptimizer\Frontend
 */
class Frontend
{

    /**
     * @var FileManager
     */
    private $fileManager;

    private $options;

    public $settings;

    public function __construct(FileManager $fileManager)
    {
        $this->options = get_option(Settings::OPTIONS);


        if ($this->checkUrlExceptions()) {
            return;
        }


        $this->fileManager = $fileManager;
        $this->settings = array(
            'is_transparent' => get_theme_mod('transparent_background', 0),
            'time_animation' => get_theme_mod('time_animation', 300),
        );

        add_action('wp', array($this, 'excludeTerms'), 9);
        add_action('wp', array($this, 'registerActions'), 10);

    }

    public function excludeTerms()
    {
        global $lzl_query;

        if (is_front_page()) $lzl_query['is_front'] = 1;
        if (is_search()) $lzl_query['is_search'] = 1;
        if (is_author()) $lzl_query['is_author'] = 1;
        if (is_singular('post')) $lzl_query['is_post'] = 1;
        if (is_page()) $lzl_query['is_page'] = 1;
        if (is_category()) $lzl_query['is_category'] = 1;
        if (is_tag()) $lzl_query['is_tag'] = 1;

    }


    public function registerActions()
    {

        if ($this->checkExceptions()) {
            return;
        }

        global $allowedposttags;
        $img = $allowedposttags['img'];
        $dataSrc = array('data-src' => true);
        $dataSrcSet = array('data-srcset' => true);
        $allowedposttags['img'] = $img + $dataSrc + $dataSrcSet;

        add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('wp_head', array($this, 'addInlineStyles'));
        add_filter('script_loader_tag', array($this, 'addAsyncAttribute'), 20, 2);

        add_filter('wp_get_attachment_image_attributes', array($this, 'addDataSrcAttachmentImage'), 50, 3);

        add_action('wp_head', array($this, 'bufferStart'));
        add_action('wp_footer', array($this, 'bufferEnd'));


    }

    public function bufferStart()
    {
        ob_start(array($this, 'htmlOutput'));
    }

    public function bufferEnd()
    {
        ob_end_flush();
    }

    public function htmlOutput($buffer)
    {
        $buffer = $this->FilterImagesAll($buffer);
        if (isset($this->options['loadIframes']) && $this->options['loadIframes']) {
            $buffer = $this->filterIframes($buffer);
        }
        return $buffer;
    }

    private function checkUrlExceptions()
    {
        if (!empty($this->options['excludeUrl'])) {
            $uri = 'http';
            if ($_SERVER["HTTPS"] == "on") {
                $uri .= "s";
            }
            $uri .= "://";
            $uri .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            $uris_exclude = explode("\n", $this->options['excludeUrl']);
            $uris_exclude = array_map('trim', $uris_exclude);

            foreach ($uris_exclude as $expr) {

                if ('' !== $expr && stristr($uri, $expr)) {

                    return true;
                }
            }
            return false;
        }
    }

    private function checkExceptions()
    {
        global $lzl_query;

        if (is_feed() || is_admin()) {
            return true;
        }

        if (isset($lzl_query['is_front']) && $this->options['loadFrontPage'] == 1) {
            return true;
        }

        if (isset($lzl_query['is_page']) && $this->options['loadPages'] == 1) {
            return true;
        }

        if (isset($lzl_query['is_post']) && $this->options['loadPosts'] == 1) {
            return true;
        }

        if (isset($lzl_query['is_category']) && $this->options['loadCategory'] == 1) {
            return true;
        }

        if (isset($lzl_query['is_tag']) && $this->options['loadTag'] == 1) {
            return true;
        }

        return false;
    }


    public function enqueueScripts()
    {

        wp_enqueue_script(
            'lazysizes',
            $this->fileManager->locateAsset('frontend/js/lazysizes.min.js'),
            array(),
            LazyLoadOptimizerPlugin::VERSION,
            true
        );


        if (isset($this->options['expand'])) {
            $expand = ($this->options['expand']) ? "window.lazySizesConfig.expand = {$this->options['expand']};" : '';
        } else {
            $expand = '';
        }

        if (isset($this->options['expFactor'])) {
            $expFactor = ($this->options['expFactor']) ? "window.lazySizesConfig.expFactor = {$this->options['expFactor']};" : '';
        } else {
            $expFactor = '';
        }

        if (isset($this->options['loadHidden'])) {
            $loadHidden = ($this->options['loadHidden']) ? "" : "window.lazySizesConfig.loadHidden = false;";
        } else {
            $loadHidden = '';
        }

        if (isset($this->options['init'])) {
            $init = ($this->options['init']) ? '' : 'window.lazySizesConfig.init = false;';
        } else {
            $init = '';
        }


        wp_add_inline_script('lazysizes',
            "window.lazySizesConfig = window.lazySizesConfig || {}; $init $expand $expFactor $loadHidden",
            'before');
    }

    public function addInlineStyles()
    {
        $this->settings = array(
            'img_url' => get_theme_mod('spinner_image', $this->fileManager->locateAsset('frontend/img/50x50-loader.gif')),
            'loading_effect' => get_theme_mod('loading_effect', 'spinner'),
            'is_spinner' => get_theme_mod('spinner', 0),
            'is_fadein' => get_theme_mod('fade_in', 1),
            'spinner_size' => get_theme_mod('spinner_size', 30),
            'time_animation' => get_theme_mod('time_animation', 300),
            'time_fadein' => get_theme_mod('time_fadein', 300),
            'is_transparent' => get_theme_mod('transparent_background', 1),
            'background_color' => get_theme_mod('lla_background_color', '#ffffff')
        );

        $spinner = '';
        $opacity = 1;
        $transition = '';

        if ($this->settings['is_spinner']) {
            $spinner = " background-image: url('{$this->settings['img_url']}');
            background-repeat: no-repeat;
            background-position: 50% 50%;
            background-size: {$this->settings['spinner_size']}px;";
        }

        if ($this->settings['is_fadein']) {
            $opacity = 0.001;
            $transition = "-webkit-transition:opacity {$this->settings['time_fadein']}ms;
-moz-transition: opacity {$this->settings['time_animation']}ms;
-ms-transition: opacity {$this->settings['time_animation']}ms;
-o-transition: opacity {$this->settings['time_animation']}ms;
transition: opacity {$this->settings['time_animation']}ms;";
        }

        if ($this->settings['is_transparent']) {
            $backgroundColor = 'background-color: rgba(0,0,0,0);';
        } else {
            $backgroundColor = 'background-color: ' . $this->settings['background_color'] . ';';

        }


        $styles = "<style>img.lazyload,img.lazyloading{
$backgroundColor
$spinner
opacity: $opacity; 
}
img.lazyload, img.lazyloaded {
opacity: 1;
$transition
}
iframe.lazyload, iframe.lazyloading{
display: block;
$backgroundColor
$spinner
}
</style>";

        echo apply_filters('lazy_load_styles', $styles);

    }

    public function addAsyncAttribute($tag, $handle)
    {

        if ('lazysizes' !== $handle) {
            return $tag;
        }

        return str_replace(' src', ' async="async" src', $tag);
    }

    public function addDataSrcAttachmentImage($attr, $attachment, $size)
    {

        if (function_exists('is_amp_endpoint') && is_amp_endpoint() === true) {
            return $attr;
        }
        if($attr['class'] == 'no-lazyload'){
            return $attr;
        }
        if ($this->options['cssClasses']) {
            $classes = $this->options['cssClasses'];
            $classesArray = explode(",", $classes);
            foreach ($classesArray as $class) {
                if (!empty($class)) {
                    if (strpos($attr['class'], $class) !== false) {
                        return $attr;
                    }
                }
            }
        }

        $image = image_downsize($attachment->ID, $size);
        if (!empty($image[1]) && !empty($image[2])) {
            $ratio = $image[1] / $image[2];
            $attr['width'] = $image[1];
        } else {
            $ratio = 2;
        }

        //$attr['style'] = '--aspect-ratio:'.$ratio.';';
        if (isset($attr['src'])) {
            $dataSrc = array('data-src' => $attr['src']);
            $attr['src'] = 'data:image/svg+xml,%3Csvg%20xmlns=%22http://www.w3.org/2000/svg%22%20viewBox=%220%200%20' . $image[1] . '%20' . $image[2] . '%22%3E%3C/svg%3E';
            $attr = $dataSrc + $attr;
        }


        if (isset($attr['srcset'])) {
            $dataSrcSet = array('data-srcset' => $attr['srcset']);
            unset($attr['srcset']);
            $attr = $dataSrcSet + $attr;
        }
        $attr['class'] = $attr['class'] . ' lazyload';

        return $attr;
    }

    public function FilterImagesAll($content)
    {

        if (function_exists('is_amp_endpoint') && is_amp_endpoint() === true) {
            return $content;
        }

        $matches = array();
        preg_match_all('/<img[\s\r\n]+(.*?)>/is', $content, $matches);

        $search = array();
        $replace = array();

        foreach ($matches[0] as $img_html) {
            $flag = false;
            if (strpos($img_html, 'data-src') !== false || strpos($img_html, 'data-srcset') !== false) {
                continue;
            }
            //CSS classes to exclude
            if(strpos($img_html, 'no-lazyload') !== false){
                continue;
            }
            if ($this->options['cssClasses']) {
                $classes = $this->options['cssClasses'];
                $classesArray = explode(",", $classes);

                foreach ($classesArray as $class) {
                    if (!empty($class)) {
                        if (strpos($img_html, $class) !== false) {
                            $flag = true;
                            break;
                        }
                    }
                }
                if ($flag) {
                    continue;
                }
            }

            $imageSizes = $this->getImageSizes($img_html);
            $width = $imageSizes[0];
            $height = $imageSizes[1];

            if ($width && $height) {
                $tempSrc = 'data:image/svg+xml,%3Csvg%20xmlns=%22http://www.w3.org/2000/svg%22%20viewBox=%220%200%20' . $width . '%20' . $height . '%22%3E%3C/svg%3E';
                $widthHtml = ' width="' . $width . '" ';
            } else {
                $tempSrc = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
                $widthHtml = ' ';
            }

            $class = 'lazyload';

            $output = '';
            $output = preg_replace('/<img(.*?)src=/is', '<img $1' . $widthHtml . 'src="' . $tempSrc . '" data-src=', $img_html);
            $output = preg_replace('/<img(.*?)srcset=/is', '<img$1data-srcset=', $output);


            if (preg_match('/class=["\']/i', $output)) {
                $output = preg_replace('/class=(["\'])(.*?)["\']/is', 'class=$1$2 ' . $class . '$1', $output);
            } else {
                $output = preg_replace('/<img/is', '<img class="' . $class . '"', $output);
            }


            array_push($search, $img_html);
            array_push($replace, $output);
        }

        $search = array_unique($search);
        $replace = array_unique($replace);
        $content = str_replace($search, $replace, $content);

        return $content;
    }

    public function getImageSizes($img_html)
    {

        $width = array();
        $height = array();
        $imageSizes = array();

        preg_match('/width=["\']([0-9]{2,})["\']/i', $img_html, $width);
        preg_match('/height=["\']([0-9]{2,})["\']/i', $img_html, $height);


        if (!empty($width) && !empty($height)) {
            $imageSizes[0] = $width[1];
            $imageSizes[1] = $height[1];
            return $imageSizes;

        } else {

            $widthSizes = array();
            preg_match('/sizes=\"\(max-width: ([0-9]{2,})px/i', $img_html, $widthSizes);

            if (!empty($widthSizes)) {
                preg_match('/-([0-9]{2,})x/i', $img_html, $width);
                preg_match('/[0-9]{2,}x([0-9]{2,})\./i', $img_html, $height);

                if (!empty($width) && !empty($height)) {

                    $ratio = $width[1] / $height[1];

                    $imageSizes[0] = $widthSizes[1];
                    $imageSizes[1] = $widthSizes[1] / $ratio;

                    return $imageSizes;
                } else {
                    $imageSizes[0] = '';
                    $imageSizes[1] = '';
                    return $imageSizes;
                }
            }

            preg_match('/-([0-9]{2,})x/i', $img_html, $width);
            preg_match('/[0-9]{2,}x([0-9]{2,})\./i', $img_html, $height);
            if (!empty($width) && !empty($height)) {
                $imageSizes[0] = $width[1];
                $imageSizes[1] = $height[1];
                return $imageSizes;
            } else {
                $imageSizes[0] = '';
                $imageSizes[1] = '';
                return $imageSizes;
            }
        }

    }


    public function FilterIframes($content)
    {
        if (empty($content)) {
            return $content;
        }
        if (function_exists('is_amp_endpoint') && is_amp_endpoint() === true) {
            return $content;
        }

        $matches = array();
        preg_match_all('/<iframe[\s\r\n]+(.*?)>/is', $content, $matches);

        $search = array();
        $replace = array();


        foreach ($matches[0] as $img_html) {
            $flag = false;

            //CSS classes to exclude
            if ($this->options['cssClasses']) {
                $classes = $this->options['cssClasses'];
                $classesArray = explode(",", $classes);

                foreach ($classesArray as $class) {
                    if (!empty($class)) {
                        if (strpos($img_html, $class) !== false) {
                            $flag = true;
                            break;
                        }
                    }
                }
                if ($flag) {
                    continue;
                }
            }


            $output = '';
            $output = preg_replace('/<iframe(.*?)src=/is', '<iframe $1data-src=', $img_html);


            if (preg_match('/class=["\']/i', $output)) {
                $output = preg_replace('/class=(["\'])(.*?)["\']/is', 'class=$1$2 lazyload$1', $output);
            } else {
                $output = preg_replace('/<iframe/is', '<iframe class="lazyload"', $output);
            }

            array_push($search, $img_html);
            array_push($replace, $output);
        }

        $search = array_unique($search);
        $replace = array_unique($replace);
        $content = str_replace($search, $replace, $content);

        return $content;
    }

}