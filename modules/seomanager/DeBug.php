<?php
/**
*
*  @author    Onasus <contact@onasus.com>
*  @copyright 20012-2015 Onasus www.onasus.com
*  @license   Refer to the terms of the license you bought at codecanyon.net
*/


class DeBug
{
	public static function logmessage($msg)
	{
		$handle = fopen(dirname(__FILE__).'/debug.log', 'a+');
		if (!$handle)return;
		$reffer = '';
		if (isset($_SERVER['HTTP_REFERER']))
			$reffer = $_SERVER['HTTP_REFERER'];
		fwrite($handle, date('H:i:s').','.$reffer.','.$msg."\r\n");
		fclose($handle);
	}
}