<?php

class ACPanelBridge_Model_ACPDatabase
{
	protected $db;

	protected function _getDb()
	{
		if (!isset($this->_db))
		{
			$config = array();

			$acpConfigPath = XenForo_Application::get('options')->acpOpt_path;
			if( substr($acpConfigPath, -1) !== '/' )
			{
				$acpConfigPath .= '/';
			}
			$acpConfigPath .= 'acpanel/includes/_cfg.php';

			if( file_exists($acpConfigPath) && is_readable($acpConfigPath) )
			{
				include($acpConfigPath);
				$this->_db = Zend_Db::factory('mysqli', array(
					'host' => $config['hostname'],
					'port' => '3306',
					'dbname' => $config['dbname'],
					'username' => $config['username'],
					'password' => $config['password']
				));
			}
			else
			{
				if( XenForo_Application::get('options')->acpOpt_debugFile )
				{
					XenForo_Helper_File::log(
						'log-error',
						new XenForo_Phrase('acp_not_path') . ' : ' . new XenForo_Phrase('acp_connect_db_error')
					);
				}
			}
		}

		return $this->_db;
	}
}