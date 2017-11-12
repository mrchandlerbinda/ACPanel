<?php

class ACPanelBridge_Listeners_LoadClassDataWriter
{
	public static function loadClassDataWriter($class, &$extend)
	{
		switch ($class)
		{
			case 'XenForo_DataWriter_User':
				$extend[] = 'ACPanelBridge_DataWriter_User';
			case 'XenForo_DataWriter_Option':
				$extend[] = 'ACPanelBridge_DataWriter_Option';
		}
	}
}