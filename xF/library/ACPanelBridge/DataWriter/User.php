<?php

class ACPanelBridge_DataWriter_User extends XFCP_ACPanelBridge_DataWriter_User
{
	protected $_currentPassword = null;

	protected function _postDelete()
	{
		$acpModel = new ACPanelBridge_Model_ACPFunctions();
		$acpModel->delete($this->get('user_id'));

		parent::_postDelete();
	}

	protected function _insert()
	{
		parent::_insert();

		$acpModel = new ACPanelBridge_Model_ACPFunctions();
		$xfUserModel = XenForo_Model::create('XenForo_Model_User');

		// figure out the group
		$gid = $xfUserModel->getACPanelGroups($this, XenForo_Application::get('options')->acpOpt_groupAssoc);
		if( $gid )
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
				$uname = $this->get('username');
				$skey = md5($uname . $config['secretkey']);
				$user_state = $this->get('user_state');
				$user_state = ($user_state == 'valid') ? 'valid' : (($user_state != 'email_confirm') ? 'moderated' : 'email_confirm');

				$customFields = unserialize($this->get('custom_fields'));
				$icq = (isset($customFields['icq'])) ? $customFields['icq'] : "";

				$insertValues = array(
					'username' => $uname,
					'password' => $this->_currentPassword,
					'mail' => $this->get('email'),
					'usergroupid' => $gid,
					'ipaddress' => $acpModel->getRealIpAddr(),
					'secretkey' => $skey,
					'timezone' => $acpModel->getACPTimeZone($this->get('timezone')),
					'reg_date' => $this->get('register_date'),
					'user_state' => $user_state,
					'icq' => $icq,
				);
				$acpModel->insert($this->get('user_id'), $insertValues);
			}
			else
			{
				if( XenForo_Application::get('options')->acpOpt_debugFile )
				{
					XenForo_Helper_File::log(
						'log-error',
						new XenForo_Phrase('acp_not_path') . ' : ' . new XenForo_Phrase('acp_user_not_created', array('username' => $this->get('username')))
					);
				}
			}
		}
		else
		{
			if( XenForo_Application::get('options')->acpOpt_debugFile )
			{
				XenForo_Helper_File::log(
					'log-error',
					new XenForo_Phrase('acp_not_assoc') . ' : ' . new XenForo_Phrase('acp_user_not_created', array('username' => $this->get('username')))
				);
			}
		}
	}

	protected function _update()
	{
		parent::_update();

		$acpModel = new ACPanelBridge_Model_ACPFunctions();

		// update the user if anything we care about changed
		if( $this->isChanged('user_group_id') || $this->isChanged('secondary_group_ids') || $this->isChanged('email') || $this->isChanged('username') || $this->isChanged('user_state') || $this->isChanged('timezone') || isset($this->_updateCustomFields['icq']) || $this->isChanged('avatar_date') || !is_null($this->_currentPassword) )
		{
			$updateArray = array();

			if( $this->isChanged('username') )
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
					$updateArray['username'] = $this->get('username');
					$updateArray['secretkey'] = md5($updateArray['username'] . $config['secretkey']);
				}
                else
                {
					if( XenForo_Application::get('options')->acpOpt_debugFile )
					{
						XenForo_Helper_File::log(
							'log-error',
							new XenForo_Phrase('acp_not_path') . ' : ' . new XenForo_Phrase('acp_user_edit_error', array('userid' => $this->get('user_id')))
						);
					}
                }
			}

			if( $this->isChanged('user_state') )
			{
				$user_state = $this->get('user_state');
				$user_state = ($user_state == 'valid') ? 'valid' : (($user_state != 'email_confirm') ? 'moderated' : 'email_confirm');
				$updateArray['user_state'] = $user_state;
			}

			if( $this->isChanged('email') )
			{
				$updateArray['mail'] = $this->get('email');
			}

			if( $this->isChanged('timezone') )
			{
				$updateArray['timezone'] = $acpModel->getACPTimeZone($this->get('timezone'));
			}

			if( isset($this->_updateCustomFields['icq']) )
			{
				$customFields = unserialize($this->get('custom_fields'));
				$updateArray['icq'] = $customFields['icq'];
			}

			if( $this->isChanged('user_group_id') || $this->isChanged('secondary_group_ids') )
			{
				$xfUserModel = XenForo_Model::create('XenForo_Model_User');

				// figure out the group
				$gid = $xfUserModel->getACPanelGroups($this, XenForo_Application::get('options')->acpOpt_groupAssoc);
				if( $gid )
				{
					$updateArray['usergroupid'] = $gid;
				}
				else
				{
					if( XenForo_Application::get('options')->acpOpt_debugFile )
					{
						XenForo_Helper_File::log(
							'log-error',
							new XenForo_Phrase('acp_not_assoc') . ' : ' . new XenForo_Phrase('acp_user_edit_error', array('userid' => $this->get('user_id')))
						);
					}
				}
			}

			if( $this->isChanged('avatar_date') )
			{
 				$updateArray['avatar'] = $this->get('avatar_date');
			}

			if( !is_null($this->_currentPassword) )
			{
				$updateArray['password'] = $this->_currentPassword;
			}

			if( !empty($updateArray) )
			{
				$acpModel->update($this->get('user_id'), $updateArray);
			}
		}
	}

	public function setPassword($password, $passwordConfirm = false, XenForo_Authentication_Abstract $auth = null, $requirePassword = false)
    {
        $this->_currentPassword = md5($password);
 
        parent::setPassword($password, $passwordConfirm, null, $requirePassword);
    }
}