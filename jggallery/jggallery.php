<?php

/*
 * Plugin Name:       JgGallery
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       A simple image gallery for Joomla and Wordpress.
 * Version:           1.0.0
 * Requires PHP:      7.2
 * Author:            James Garrett
 * Author URI:        https://jamesgarrett.com.au
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

// ========= Prevent Direct Access and determine the CMS =============
if(defined('_JEXEC')){
	$cms = 'joomla';
} elseif(defined('WPINC')){
	$cms = 'wp';
} else {
	die();
}

// ==================================================================
class JgGallery
{
	private static $id = 0;
	private static $scripts_n_styles = '<link href="https://cdn.jsdelivr.net/npm/nanogallery2@3/dist/css/nanogallery2.min.css" rel="stylesheet" type="text/css"><script type="text/javascript" src="https://cdn.jsdelivr.net/npm/nanogallery2@3/dist/jquery.nanogallery2.min.js"></script><style>img.jg-gallery-img-thumb{margin: 0 8px 8px 0;}.nGY2 .nGY2GThumbnail{background-color:transparent}</style>';


	static public function renderGalleryFromFolderPath($folder,$public_folder,$thumbnail_width,$width,$quality)
	{
		if(!is_dir($folder)){
			return $folder . '|' . $public_folder . '|' . $thumbnail_width . '|' . $width . '|' . $quality;
			return 'Folder not found';
		}

		$images = self::getImagesListFromFolder($folder);

		if(empty($images)){
			return 'No Images in Folder';
		}

		self::$id++;

		$html = '';
		if(self::$id == 1){
			require_once(__DIR__ . '/ResizeImage.php');
			$html .= self::$scripts_n_styles;
		}

		if(!is_dir($folder . 'images')){
			mkdir($folder . 'images');
		}

		if(!is_dir($folder . 'thumbs')){
			mkdir($folder . 'thumbs');
		}

		$html .= '<div id="my_nanogallery' . self::$id . '" data-nanogallery2 = \'{"thumbnailHeight":"auto","thumbnailWidth":' . $thumbnail_width . ',"itemsBaseURL":"' . $public_folder . '","colorScheme":"light"}\'>';

		foreach($images as $image){
			$filename = array_reverse(explode('/', $image))[0];
			if(!file_exists($folder . 'thumbs/' . $filename)){
				self::cropAndSaveImage($image,$folder . 'thumbs/' . $filename,$thumbnail_width,$quality);
			}
			if(!file_exists($folder . 'images/' . $filename)){
				self::cropAndSaveImage($image,$folder . 'images/' . $filename,$width,$quality);
			}

			$html .= '<a href="images/' . $filename . '" data-ngthumb="thumbs/' . $filename . '"> </a>';
		}

		$html .= '</div>';

		return $html;
	}

	private static function filepathToImgTag($filepath)
	{
		$public_filepath = array_reverse(explode('public_html',$filepath))[0];
		return '<img class="jg-gallery-img-thumb" src="' . $public_filepath . '" />';
	}

	private static function cropAndSaveImage($image,$destination,$width,$quality)
	{
		$resize = new ResizeImage($image);
		$resize->resizeTo($width, $width, 'maxWidth');
		$resize->saveImage($destination,$quality);
	}

	private static function getImagesListFromFolder($folder)
	{
		return glob($folder . '*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF}', GLOB_BRACE);
	}
}

// ======= Wordpress ===============================================

if($cms == "wp"){
	function jggallery($atts){
	    if(empty($atts['folder'])){return 'No folder in shortcode';}

	    $folder = explode('public_html',__DIR__);
	    $folder = $folder[0] . 'public_html/'. trim($atts['folder'],"/") . '/';

	    $public_folder = '';
		$public_folder .= array_reverse(explode('public_html',$folder))[0];

		$options = get_option( 'jg_gallery_plugin_options' );
		if(empty($options['thumbnail_width'])){
			$options = [
				'thumbnail_width' => 150,
				'image_width'	=> 800,
				'image_quality'	=> 80,
			];
		}
	    return JgGallery::renderGalleryFromFolderPath($folder,$public_folder,$options['thumbnail_width'],$options['image_width'],$options['image_quality']);

	}
	add_shortcode('jg-gallery', 'jggallery' );

	if(is_admin()){require_once('wp_settings.php');}
	return;
}

// ====== Joomla ================================================

class plgContentJggallery extends JPlugin{
		
	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{	
		if(empty($article->text) || strpos($article->text,"[jg-gallery") === false){return;}

		$thumbnail_width = $this->params->get('thumbnail_width',150,'INT');
		$image_width = $this->params->get('image_width',800,'INT');
		$image_quality = $this->params->get('image_quality',80,'INT');
		
		preg_match_all('#[jg-gallery(.*?)]#s', $article->text, $matches);

		$tags = [];
		foreach($matches[0] as $i => $m){
			if(empty($matches[1][$i])){continue;}
			$tags[] = [
				'original' => $m,
				'raw_attr' => $matches[1][$i],
				'html' => '',
			];
		}

		foreach($tags as $index => $t){
			$parsed_attr = $this->attrString2Array($t['raw_attr']);

			if(empty($parsed_attr['folder'])){
				$tags[$index]['html'] = 'no folder specified';
				continue;
			}

			$folder = JPATH_ROOT . '/' . trim($parsed_attr['folder'],"/") . '/';
			$public_folder = array_reverse(explode('public_html',$folder))[0];

			$tags[$index]['html'] = JgGallery::renderGalleryFromFolderPath($folder,$public_folder,$thumbnail_width,$image_width,$image_quality);
		}

		$article->text = strtr($article->text,array_combine(array_column($tags,'original'),array_column($tags,'html')));
	}

	private function attrString2Array($attr)
	{
	  $atList = [];
	  if (preg_match_all('/\s*(?:([a-z0-9-]+)\s*=\s*"([^"]*)")|(?:\s+([a-z0-9-]+)(?=\s*|>|\s+[a..z0-9]+))/i', $attr, $m)) {
	    for ($i = 0; $i < count($m[0]); $i++) {
	      if ($m[3][$i])
	        $atList[$m[3][$i]] = null;
	      else
	        $atList[$m[1][$i]] = $m[2][$i];
	    }
	  }
	  return $atList;
	}

}