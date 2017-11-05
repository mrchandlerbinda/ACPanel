<?php

if (!defined('IN_ACP')) die("Hacking attempt!");

function cdrtobin($cdrin)
{
	return str_pad(str_pad("", $cdrin, "1"), 32, "0");
}

function binnmtowm($binin)
{
	$binin = rtrim($binin, "0");
	if( !preg_match("/0/",$binin) )
	{
		return str_pad(str_replace("1","0",$binin), 32, "1");
	} else return "1010101010101010101010101010101010101010";
}

function dqtobin($dqin)
{
        $dq = explode(".",$dqin);
        for( $i=0; $i<4 ; $i++ )
	{
		$bin[$i] = str_pad(decbin($dq[$i]), 8, "0", STR_PAD_LEFT);
        }
        return implode("",$bin);
}

function bintocdr($binin)
{
	return strlen(rtrim($binin,"0"));
}

function dotbin($binin,$cdr_nmask)
{
	if( $binin == "N/A" ) return $binin;
	$oct = rtrim(chunk_split($binin,8,"."),".");
	if( $cdr_nmask > 0 )
	{
		$offset = sprintf("%u",$cdr_nmask/8) + $cdr_nmask ;
		return substr($oct,0,$offset ) . "&nbsp;&nbsp;&nbsp;" . substr($oct,$offset) ;
	}
	else
		return $oct;
}

function bintodq($binin)
{
	if( $binin == "N/A" ) return $binin;
	$binin = explode(".", chunk_split($binin,8,"."));
	for( $i=0; $i<4 ; $i++ )
	{
		$dq[$i] = bindec($binin[$i]);
	}
        return implode(".",$dq) ;
}

?>