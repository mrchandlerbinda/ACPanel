<?php

class Permissions
{
	static public $mysql = NULL;
	private $permissions = array("read" => false, "add" => false, "write" => false, "delete" => false);

	public function __construct($mysql)
	{
		self::$mysql = $mysql;
	}

	public function toPermission($bitmask)
	{
		$perm = array("read" => false, "add" => false, "write" => false, "delete" => false);

		$i = 0;
		foreach( $perm as $key => $value )
		{
			$perm[$key] = (($bitmask & pow(2,$i)) != 0) ? true : false;
			$i++; 				
		}

		return $perm;
	}

	public function toBitmask($permCurrent)
	{
		$perm = array("read" => false, "add" => false, "write" => false, "delete" => false);

		$bitmask = 0;
		$i = 0;
		foreach( $perm as $key => $value )
		{			
			if( in_array($key, $permCurrent) )
				$bitmask += pow(2,$i);

			$i++;	
		}

		return $bitmask;
	}

	public function getPermissions($action, $group)
	{
		$this->permissions = array("read" => false, "add" => false, "write" => false, "delete" => false);

		if( !is_null(self::$mysql) && is_numeric($group) )
		{
			$db = self::$mysql;

			if( !is_numeric($action) )
			{
				$bitMask = $db->Query("SELECT bitmask FROM `acp_permissons_action` a LEFT JOIN `acp_usergroups_permissions` b ON b.id = a.action WHERE b.varname = '".$action."' AND usergroupid = ".$group." LIMIT 1", array());
			}
			else
			{
				$bitMask = $db->Query("SELECT bitmask FROM `acp_permissons_action` WHERE action = ".$action." AND usergroupid = ".$group." LIMIT 1", array());
			}

			if( !is_null($bitMask) )
			{
				$i=0;
				foreach( $this->permissions as $key => $value )
				{
					$this->permissions[$key] = (($bitMask & pow(2,$i)) != 0) ? true : false;
					$i++; 				
				}
			}
		}

		return $this->permissions;
	}
	
	public function setPermissions($action, $group, $bitmask)
	{
		if( !is_null(self::$mysql) && is_numeric($action) && is_numeric($group) )
		{
			$db = self::$mysql;

			$set = $db->Query("INSERT INTO `acp_permissons_action` SET action = '{action}', usergroupid = '{group}', bitmask = '{bitmask}' ON DUPLICATE KEY UPDATE bitmask = '{bitmask}'", array('action' => $action, 'group' => $group, 'bitmask' => $bitmask));
			if( $set ) return true;
		}

		return false;
	}
}

?>