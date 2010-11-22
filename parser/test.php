<?php
// example of how to use advanced selector features
define( '_DNEST_EXEC', 1 );

define('DNEST_PATH_BASE', dirname(__FILE__) );
define( 'DS', DIRECTORY_SEPARATOR );

require_once ( DNEST_PATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( DNEST_PATH_BASE .DS.'includes'.DS.'framework.php' );
require_once (DNEST_PATH_LIBRARIES.DS.'simplehtmldom'.DS.'simple_html_dom.php');

//;include('simple_html_dom.php');





function directoryToArray($directory, $recursive, $cmp_str)
{
	if ($handle = opendir($directory)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") 
			{
				if (is_dir($directory. "/" . $file)) 
				{
					if($recursive)
					{
						directoryToArray($directory. "/" . $file, $recursive, $cmp_str);
					}
					$file = $directory . "/" . $file;
					if (strpos($file,".htm") !== false)
					{
						echo $file."\n";
					}
				} 
				else 
				{
					if (strpos($file,".htm") !== false)
					{
						$status = ParseMyProject($directory, $file);
						echo $directory . "/" . $file.$status." ";
					}
				}
			}
		}
		closedir($handle);
	}
}

directoryToArray(".", true, 1);

function getName($file_img) {
	$file_img = str_replace(DS, '/', $file_img);
	$slash = strrpos($file_img, '/');
	if ($slash !== false) {
		return substr($file_img, $slash + 1);
	} else {
		return $file_img;
	}
}			

//InsertArticles();
function CopyImages($directory, $file)
{
	$path = DNEST_PATH_BASE .DS.'images'.DS;
	if ( !file_exists( $directory.DS.$file ) )
	{
		echo "Внимание! Файл".$directory.DS.$file." не сужествет";
		return false;
	}

	if(!copy($directory.DS.$file, $path.DS.$file))
	{	
		echo "Ошибка копирования файла".$directory.DS.$file."...<br />\n";
		return false;
	}
	return true;
}

function ParseMyProject($directory, $file)
{
	$file_html = $directory. DS .$file;
	$ret_val = null;
	$html = new simple_html_dom();
	$html = file_get_html($file_html);
	$charset_str = $html->find('meta', 0)->attr['content'];
	$charset_pos = strpos($charset_str,"charset=");
	if($charset_pos !== false)
		$charset = substr($charset_str, $charset_pos+strlen("charset="));

	$meta_str = $html->find('meta');
	foreach ($meta_str as $value) {
		switch($value->attr['name'])
		{
			case "keywords":
				$keywords_str = $value->attr['content'];
				break;
			case "description":		
				$description_str = $value->attr['content'];
				break;
			case "robots":		
				$robots_str = $value->attr['content'];
			
		}
		
	}
	// рубрики
	$e = $html->find('div[id=breadcrumb]', 0);
	if(isset($e))
	{
		$rubriki = str_get_html($e->innertext);
		$a_tag = $rubriki->find('a');
		foreach ($a_tag as $value) {
			$str_utf = JString::transcode($value->innertext,$charset,'utf-8');
			$array_categoty_alias[] =  JFilterOutput::transliterateRuToEngUrl($str_utf);
			$array_categoty_utf[] = $str_utf;
			$array_categoty[] = $value->innertext;
		}
		
	}
	// порядок $row->ordering старая позже
	
	// просмотры
	$e = $html->find('p[class=service]', 0);
	if(isset($e))
	{
		$str = preg_replace('/[^0-9]/', '', $e->innertext);
		$hits = intval($str);
	}
	// статьи
	$e = $html->find('div[id=article]', 0);
	if(isset($e))
	{
		$ret_val = "article";
		if(isset($charset) && 'utf-8'!== strtolower($charset) )
			$http_article = JString::transcode($e->outertext,$charset,'utf-8');
		else
			$http_article = $e->outertext;
				
		$html = str_get_html($http_article);
		
		$e = $html->find('h1', 0);
		if(isset($e))
		{
			$title = $e->innertext;
			$alias =  JFilterOutput::transliterateRuToEngUrl($e->innertext);
			echo $alias;
		}
		
		$e = $html->find('div[class=intro]', 0);
		if(isset($e))
		{
			$introtext =  $e->outertext;
			echo $introtext;
		}
		
		$img_str = $html->find('img');
		foreach ($img_str as $value)
		{
			$full_filename = $value->attr['src'];
			if ( !file_exists( $full_filename ) )
			{
				echo "Внимание! Файл ".$$full_filename." не сужествет";
			}
			$file_img = getName($full_filename);
			$path_img = DNEST_PATH_BASE .DS.'images'.DS;
			/*	
			$in_ = sizeof($array_categoty);
			if($array_categoty[$in_ - 1] == "Главная")
				$in_--;
			$path_str = '';
			for (; $in_ != 0; $in_--)
			{
				$path_str = $path_str.$array_categoty_alias[$in_ - 1];
				if(!is_dir($path_img.$path_str))
					mkdir($path_img.$path_str);
				$path_str = $path_str.DS;
			}
			$path_url_str = str_replace(DS, '/', $path_str);

			if(!copy($full_filename, $path_img.$path_str.$file_img))
			{	
				echo "Ошибка копирования файла".$directory.DS.$file."...<br />\n";
			}
			$value->attr['src'] =  "images/".$path_url_str.$file_img;
			*/
			if(!copy($full_filename, $path_img."articles".DS.$file_img))
			{	
				echo "Ошибка копирования файла".$directory.DS.$file."...<br />\n";
			}
			$value->attr['src'] =  "images/articles/".$file_img;
		}
		
		
		$e = $html->find('div[class=direct]', 0);
		if(isset($e))
		{
			if($e->parent()->getAttribute('class')  === "mb15")
			{
				$e->parent()->outertext = '<div class="advert"> </div>';
			}
			else
			{ 
				$e->outertext = '<div class="advert"> </div>';
			}
			
			$e = $html->find('p[class=author]',0);
			if(isset($e))
			{
				echo $created_by_alias = $e->innertext;
				$e1 = $e->next_sibling();
				$e1->outertext = '';
			}
		}
		$html->save($file_html."a.htm");
		// save all changes
		$http_article = $html->innertext;
		
		$db = & JFactory::getDBO();
//-------------------------

		$in = 0;
		for (; $array_categoty[$in] != "Главная" && $in < 5; $in++);
		echo $in;
		
		if($in >= 4)
			$in = 0;
		$query = 'SELECT id'
			. ' FROM #__sections'
			. ' WHERE alias = "'.$array_categoty_alias[$in-1].'"';
		;
		$db->setQuery( $query );
		$id_section = $db->loadResult();					
//		существует ли уже такаю категория в базе то ничего не делаем
//-----			
		
		if(!isset($id_section))
		{
			$row_s = & JTable::getInstance('section');
			$row_s->load(0);
			$row_s->title = $array_categoty_utf[$in-1];
			$row_s->alias = $array_categoty_alias[$in-1];
			$row_s->scope = "content";
			$row_s->published = 1;
			if (!$row_s->check()) {
				echo "error data";
			}
			if (!$row_s->store()) {
				echo " save error";
			}
			// where was insert
			$query = 'SELECT id'
				. ' FROM #__sections'
				. ' WHERE alias = "'.$array_categoty_alias[$in-1].'"';
			;
			$db->setQuery( $query );
			$id_section = $db->loadResult();					
		}
// ---------------------------------


		$nullDate = $db->getNullDate();
		$now	=& JFactory::getDate();
		$config =& JFactory::getConfig();
		$publish_up =& JFactory::getDate($row->publish_up);
		$publish_down =& JFactory::getDate($row->publish_down);
		$publish_up->setOffset($config->getValue('config.offset'));
		$publish_down->setOffset($config->getValue('config.offset'));
		
				
		$row = & JTable::getInstance('content');
		$row->load(0);
		//file_put_contents('default_save.htm', $e->outertext);
		$row->title = $title;
		$row->alias = $alias;
//		$row->introtext = 'text';
//		$row->fulltext = 'fulltext';

		$row->introtext = $introtext;
		$row->fulltext = $http_article;
		$row->sectionid = $id_section;
		$row->publish_up = $publish_up;
		$row->publish_down = $publish_down;
		$row->hits =$hits;
		$row->state = 1; // публиковать
		$row->created_by = 62;
		$row->created_by_alias = $created_by_alias;
				
		$row->metakey = JString::transcode($keywords_str,$charset,'utf-8');
		$row->metadesc = JString::transcode($description_str, $charset,'utf-8');
		if (!$row->check()) {
			echo "error data";
		}
		if (!$row->store()) {
			echo " save error";
		}

	}
	return $ret_val;
}

function InsertArticles()
{
	$db = & JFactory::getDBO();
	$row = & JTable::getInstance('content');
	$row->load(3);
	$row->created_by_alias = "Dmitry Sigaev";
	if (!$row->check()) {
		echo "error data";
	}
	if (!$row->store()) {
		echo " save error";
	}

}
?>