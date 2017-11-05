<?php

if (!defined('IN_ACP')) die("Hacking attempt!");

function getArrayDurations($value, $type)
{
	$return = array();
	switch($type)
	{
		case "year":

			if( $value )
			{
				$sum = $value;
				do
				{
					$sum = $sum + $value;
					$return[$sum] = get_correct_str($sum, "@@time_".$type."_one@@", "@@time_".$type."_several@@", "@@time_".$type."_many@@"); 
				}
				while( $sum < 2 );
			}
			break;

		case "month":

			if( $value )
			{
				$sum = $value;
				do
				{
					$sum = $sum + $value;
					$return[$sum] = get_correct_str($sum, "@@time_".$type."_one@@", "@@time_".$type."_several@@", "@@time_".$type."_many@@"); 
				}
				while( $sum < 24 );
			}
			break;

		case "day":

			if( $value )
			{
				$sum = $value;
				do
				{
					$sum = $sum + $value;
					$return[$sum] = get_correct_str($sum, "@@time_".$type."_one@@", "@@time_".$type."_several@@", "@@time_".$type."_many@@"); 
				}
				while( $sum < 31 );
			}
			break;

		default:

			if( $value )
			{
				$sum = $value;
				do
				{
					$sum = $sum + $value;
					$return[$sum] = get_correct_str($sum, "@@time_".$type."_one@@", "@@time_".$type."_several@@", "@@time_".$type."_many@@"); 
				}
				while( $sum < 31 );
			}
			break;
	}

	return $return;
}

?>