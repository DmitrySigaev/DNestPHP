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
					$file = $directory . "/" . $file;
					if (strpos($file,".htm") !== false)
					{
						$status = ParseMyProject($file);
						echo $file.$status." ";
					}
				}
			}
		}
		closedir($handle);
	}
}

directoryToArray(".", true, 1);
InsertArticles();


function ParseMyProject($file_html)
{
	$ret_val = null;
	$html = new simple_html_dom();
	$html = file_get_html($file_html);
	$charset_str = $html->find('meta', 0)->attr['content'];
	$charset_pos = strpos($charset_str,"charset=");
	if($charset_pos !== false)
		$charset = substr($charset_str, $charset_pos+strlen("charset="));

	
	$e = $html->find('div[id=article]', 0);
	if(isset($e))
	{
		$ret_val = "article";
		if(isset($charset) && 'utf-8'!== strtolower($charset) )
			$http_article = JString::transcode($e->outertext,$charset,'utf-8');
		else
			$http_article = $e->outertext;
				
		$html = str_get_html($http_article);
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
				$e1 = $e->next_sibling();
				$e1->outertext = '';
			}
		}
		$html->save($file_html."a.htm");
		//file_put_contents('default_save.htm', $e->outertext);
	}
	return $ret_val;
}

function InsertArticles()
{
	$db = & JFactory::getDBO();
	$row = & JTable::getInstance('content');
	$row->load(1);
	$row->created_by_alias = "Dmitry Sigaev";
	if (!$row->check()) {
		echo "error data";
	}
	if (!$row->store()) {
		echo " save error";
	}

}
?>