<?php

class ACPanelBridge_Option_Database
{
	public static function verifyDatabase(&$database, XenForo_DataWriter $dw = null, $fieldName = null)
	{
		if (!$database['host'] ||
			!$database['port'] ||
			!$database['dbname'] ||
			!$database['username'] ||
			!$database['password'])
		{
			return false;
		}

		try
		{
			$db = Zend_Db::factory('mysqli', array(
				'host' => $database['host'],
				'port' => $database['port'],
				'dbname' => $database['dbname'],
				'username' => $database['username'],
				'password' => $database['password']
			));
			$db->getConnection();

			$acpTables = array(
				'acp_usergroups',
				'acp_category',
				'acp_config'
			);
			$query = $db->listTables();
			if (count(array_diff($acpTables, $query)) > 0) {
				$dw->error(new XenForo_Phrase('acpanel_table_name_invalid'));
				return false;
			}
		}
		catch (Zend_Db_Adapter_Exception $e)
		{
			if ($dw)
			{
				$dw->error($e->getMessage(), $fieldName);
			}
			return false;
		}

		return true;
	}
}