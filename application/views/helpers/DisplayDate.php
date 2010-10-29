<?php
class Zend_View_Helper_DisplayDate
{
	function displayDateShort($date)
	{
		if (ereg('([0-9]{4})-([0-9]{2})-([0-9]{2})', $date, $matched)) {
			return "$matched[3]-$matched[2]-$matched[1]";
		}
	}
	
	function displayDateLong($date)
	{
		if (ereg('([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}:[0-9]{2}:[0-9]{2})', $date, $matched)) {
			return "$matched[3]-$matched[2]-$matched[1] $matched[4]";
		}
	}
}
