<?php
class Zend_View_Helper_DisplayDateLong
{
	function displayDateLong($date)
	{
		if (ereg('([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}:[0-9]{2}:[0-9]{2})', $date, $matched)) {
			return "$matched[3]-$matched[2]-$matched[1] at $matched[4]";
		}
	}
}
