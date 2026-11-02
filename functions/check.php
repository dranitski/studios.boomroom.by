<?php
	
	defined('access') or die ('no access');
	
	function check_vkontakte($url) {
		$str = @file_get_contents("http://vk.com/share.php?act=count&index=1&url=".urlencode($url));
		preg_match('#VK.Share.count\(1, ([0-9]+)\);#', $str, $matches);
		return (count($matches) > 1) ? intval($matches[1]) : false;
	}
	
	function check_facebook($url) {
		$json = json_decode(@file_get_contents("http://graph.facebook.com/?id=".urlencode($url)));
		return $json !== false ? intval($json->shares) : false;
	}
	
	function check_twitter($url) {
		$json = json_decode(@file_get_contents("http://urls.api.twitter.com/1/urls/count.json?url=".urlencode($url)));
		return $json !== false ? intval($json->count) : false;
	}
	
	function check_odnoklassniki($url) {
		$str = @file_get_contents("http://ok.ru/dk?st.cmd=extLike&ref=".urlencode($url));
		preg_match('#ODKL.updateCount\(\'null\',\'([0-9]+)\'\);#', $str, $matches);
		return (count($matches) > 1) ? intval($matches[1]) : false;
	}
	
	function check_moimir($url) {
		$json = json_decode(@file_get_contents("http://connect.mail.ru/share_count?url_list=".urlencode($url)), true);
		return $json !== false ? intval($json[$url]['shares']) : false;
	}
	
	function check_gplus($url) {
		
		$html = @file_get_contents("https://plusone.google.com/_/+1/fastbutton?url=".urlencode($url)."&count=true");
		preg_match('#<div id="aggregateCount" class=".*">([0-9]+)</div>#', $html, $matches);
		return (count($matches) > 1) ? intval($matches[1]) : false;
	}
	
?>