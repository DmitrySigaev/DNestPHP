<?php
// example of how to use advanced selector features
define( '_DNEST_EXEC', 1 );

define('DNEST_PATH_BASE', dirname(__FILE__) );
define( 'DS', DIRECTORY_SEPARATOR );

require_once ( DNEST_PATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( DNEST_PATH_BASE .DS.'includes'.DS.'framework.php' );
require_once (DNEST_PATH_LIBRARIES.DS.'simplehtmldom'.DS.'simple_html_dom.php');

//;include('simple_html_dom.php');





function directoryToArray($directory, $dest_dir, $recursive, $method)
{
	$save = 0;
	if ($handle = opendir($directory)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") 
			{
				if (is_dir($directory. DS . $file)) 
				{
					if($recursive)
					{
						if (strpos($method,"copy") !== false || strpos($method,"move") !== false)
							mkdir($dest_dir.DS.$file);
						$s = directoryToArray($directory.DS.$file, $dest_dir.DS.$file, $recursive, $method);
						if (strpos($method,"move") !== false && !$s)
						{
							rename($directory.DS.$file, $dest_dir.DS.$file);
							rmdir($directory.DS.$file);
						}
						
					}
					$file = $directory . DS . $file;
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
						if($status === 1 && (strpos($method,"prolong") !== false))
							unlink($directory . DS . $file);
						else if (strpos($method,"move") !== false)
							rename($directory.DS.$file, $dest_dir.DS.$file);
					}
					if (strpos($file,".jpg") !== false)
					{
						$save = 1;
					}
					if (strpos($file,".png") !== false)
					{
						$save = 1;
					}
					if (strpos($file,".gif") !== false)
					{
						$save = 1;
					}
					if (strpos($file,".flv") !== false)
					{
						$save = 1;
					}
					if (strpos($file,".WD3") !== false)
						unlink($directory . DS . $file);
					if (strpos($file,".ru") !== false)
						unlink($directory . DS . $file);
					if (strpos($file,".su") !== false)
						unlink($directory . DS . $file);
					if (strpos($file,".net") !== false)
						unlink($directory . DS . $file);
					if (strpos($file,".org") !== false)
						unlink($directory . DS . $file);
					if (strpos($file,".com") !== false)
						unlink($directory . DS . $file);
				}
			}
		}
		closedir($handle);
	}
	return $save;
}

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
			$str_category = $value->innertext; 
			// удаляем ненужные символы их названия
			$str_category = preg_replace(
				array('/&(.)[^;]*quo;/','/&amp;/'),
				array('',''),
				$str_category);			
			$str_utf = JString::transcode($str_category,$charset,'utf-8');
			$array_categoty_utf[] = $str_utf; // сохраняем в ютиф форме
			$array_categoty[] = $str_category; // сохраняем в виндойс кодировке для ставнения в дальнейшем
// old			$array_categoty_alias[] =  JFilterOutput::transliterateRuToEngUrl($str_utf);

			$categoty_alias = JFilterOutput::transliterateRuToEngUrl($str_utf);
			$initial = array("/^stati$/", "/^zagorodniy-otdih$/", "/^inzhenernie-sistemi$/", "/^stroitelstvo$/", "/^landshaft$/", "/^nedvizhimost$/", "/^interer$/");
			$dictionary   = array("articles", "holidays", "engineering", "construction", "landscape", "realty", "interior");
			$str_translated = preg_replace($initial, $dictionary, $categoty_alias);
			if(isset($str_translated))
				$array_categoty_alias[] = $str_translated;
			else
				$array_categoty_alias[] = $categoty_alias;
		}
		
	}
	
	// время статьи 
	$e = $html->find('div[class="border date"]', 0);	
	if(isset($e))
	{
		$date_created = $e->innertext;
//		$day_month_year = preg_split('/[/\.\-]/', $date_created);
//		$day_month_year = preg_split("/[\/\.\-,]+/", $date_created);
		list($day,$month, $year) = preg_split("/[\/\.\-,]+/", $date_created);
		$date_created = $year."-"."$month"."-".$day." ".date("H:i:s");    
		
		//		list($day,$month, $year) = preg_split('/./', $date_created);
	}
	
	// порядок $row->ordering старая позже
	
	// просмотры
	$hits = 0;
	$p_tags = $html->find('p[class="service"]'); // tag problem: minor service
	if(isset($p_tags))
	{
		foreach ($p_tags as $value) {
			if($value->attr['class'] === "service")
			{
				$str = preg_replace('/[^0-9]/', '', $value->innertext);
				$hits = intval($str);
			}
			if($value->attr['class'] === "minor service")
			{
				$is_the_last = $value->next_sibling();
				if(!isset($is_the_last))
				{
					$e1 = $value->nodes[0];
					$e1->outertext =$e1->outertext."</p>";
					$html_str =	$html->root->innertext();
					$html = str_get_html($html_str);
					//				$hits = intval($str);
				}

			}
			
		}
	}
	// проверяем изменения...
	$p_tags = $html->find('p[class="service"]'); // tag problem: minor service
	if(isset($p_tags))
	{
		foreach ($p_tags as $value) {
			if($value->attr['class'] === "service")
			{
				$str = preg_replace('/[^0-9]/', '', $value->innertext);
				$hits = intval($str);
			}
			if($value->attr['class'] === "minor service")
			{
				$is_the_last = $value->next_sibling();
				if(!isset($is_the_last))
				{
					$e1 = $value->nodes[0];
					$e1->outertext =$e1->outertext."</p>";
					//				$hits = intval($str);
				}

			}
			
		}
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
			$title_html = str_get_html($e->innertext);
			$br = $title_html->find('br'); // удаляем бр
			foreach ($br as $value)
			{
				$value->outertext = ' ';
			}
			$br = $title_html->find('span'); // удаляем бр
			foreach ($br as $value)
			{
				$value->outertext = $value->innertext;
			}
			$alias_process = $title_html->root->outertext;

			$title = JFilterOutput::deletingExtraSymbolUTF($e->innertext);
			$alias =  JFilterOutput::transliterateRuToEngUrl($alias_process);
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
				$full_filename = $directory.DS.$full_filename;				
				$full_filename = str_replace(DS, '/', $full_filename);
				$full_filename = str_replace('//', '/', $full_filename);
				if ( !file_exists( $full_filename ) )
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
		
		$a_str = $html->find('a');
		foreach ($a_str as $value)
		{
			$link = $value->attr['href'];
			if (isset($link))
			{
				if (strpos($link,"tags") !== false)
				{
					$in_text = $value->innertext;
					$value->outertext = $in_text;
				}
			}
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
		}
		$e = $html->find('p[class=author]',0);
		if(isset($e))
		{
			echo $created_by_alias = $e->innertext;
//			$e1 = $e->next_sibling();
//			$e1->outertext = '';
		}
//		else //два различных варианта: убираем гавно...
		{
			$e = $html->find('div[class=service]', 0);
			if(isset($e))
			{
				if($e->parent()->getAttribute('style') != null)
					$e->parent()->outertext = '';
				
			}
		}
		
//		$html->save($file_html."a.htm");
		// save all changes
		$http_article = $html->innertext;
		
		$db = & JFactory::getDBO();
//-------------------------
		
		$in_2 = sizeof($array_categoty);
		if($array_categoty[$in_2 - 1] == "Главная")
			$in_2--;
		$max_s_c = $in_2;
		$sec_cat = array ("sections", "categories", "subcategories");
		$sec_cat2 = array ("section", "category", "subcategory");

		for (; $in_2 != 0; $in_2--)
		{
			$in_2i = $max_s_c-$in_2;
			$from_table = $sec_cat[$in_2i];
			
			$query = 'SELECT id'
				. ' FROM #__'.$from_table
				. ' WHERE alias = "'.$array_categoty_alias[$in_2-1].'"';
			;
			$db->setQuery( $query );
			$id_section[$in_2i] = $db->loadResult();
			// если существуетуже такаю категория в базе, то ничего не делаем


			if(!isset($id_section[$in_2i]))
			{
				$row_s = & JTable::getInstance($sec_cat2[$in_2i]);
				$row_s->load(0);
				$row_s->title = $array_categoty_utf[$in_2-1];
				$row_s->alias = $array_categoty_alias[$in_2-1];
				if(!($in_2i))
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
					. ' FROM #__'.$from_table
					. ' WHERE alias = "'.$array_categoty_alias[$in_2-1].'"';
				;
				$db->setQuery( $query );
				$id_section[$in_2i] = $db->loadResult();					
			}
		}
		/*
///---------------				
			
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
*/

		$query = 'SELECT id'
			. ' FROM #__content'
			. ' WHERE alias = "'.$alias.'"';
		;
		$db->setQuery( $query );
		$id_content = $db->loadResult();					
		//		если существует такая статья в базе, то ничего не делаем
		//-----			
		
//		if(!isset($id_content))
		{
			$nullDate = $db->getNullDate();
			$createdate =& JFactory::getDate();		
			
			$row = & JTable::getInstance('content');
			$row->load(0);
			//file_put_contents('default_save.htm', $e->outertext);
			$row->title = $title;
			$row->alias = $alias;
			//		$row->introtext = 'text';
			//		$row->fulltext = 'fulltext';

			$row->introtext = $introtext;
			$row->fulltext = $http_article;

			$row->sectionid = $id_section[0];
			$row->catid = $id_section[1];
			$row->subcatid = $id_section[2];

			$row->images = array ();
			if(!isset($date_created))
			{
				$date_created = $createdate->toMySQL();
			}
			$row->publish_up = $date_created;
			$row->publish_down = $nullDate;
			$row->created = $date_created; //$createdate->toMySQL();
			$row->modified = $nullDate;
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
		$ret_val = 1;
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


//directoryToArray("C:\\SITE\\www.zagorod.spb.ru\\articles", "C:\\SITE2",true,"prolong&&move");
directoryToArray(".", "C:\\SITE2",true, "test");
//directoryToArray(".", true, 1, 0);


?>