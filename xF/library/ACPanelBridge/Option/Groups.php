<?php

class ACPanelBridge_Option_Groups
{
	public static function renderOption(XenForo_View $view, $fieldPrefix, array $preparedOption, $canEdit)
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

			$database = array(
				'host' => $config['hostname'],
				'port' => '3306',
				'dbname' => $config['dbname'],
				'username' => $config['username'],
				'password' => $config['password']
			);

			if( ACPanelBridge_Option_Database::verifyDatabase($database) )
			{
				$preparedOption['formatParams'] = XenForo_Model::create('ACPanelBridge_Model_Group')->getUserGroupOptions(
					$preparedOption['option_value']
				);

				return XenForo_ViewAdmin_Helper_Option::renderOptionTemplateInternal(
					'acp_options_list_groups',
					$view, $fieldPrefix, $preparedOption, $canEdit
				);
			}
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

		return '';
	}

	public static function verifyOption(array &$groups, XenForo_DataWriter $dw = null, $fieldName = null)
	{
		if (is_array($groups) && array_key_exists(0, $groups)) {
			unset($groups[0]);
		}

		foreach ($groups as $groupId => $selectedGroups) {
			if (!is_array($selectedGroups)) {
				unset($groups[$groupId]);
			} else {
				if ($selectedGroups['acp_group_id'] == '') {
					$dw->error(new XenForo_Phrase('acpanel_group_association_invalid'));
				}
			}
		}

		return true;
	}
}