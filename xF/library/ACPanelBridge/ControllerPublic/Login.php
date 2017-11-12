<?php

class ACPanelBridge_ControllerPublic_Login extends XFCP_ACPanelBridge_ControllerPublic_Login
{
	public function actionLogin()
	{
		$this->_assertPostOnly();

		$data = $this->_input->filter(array(
			'login' => XenForo_Input::STRING,
			'password' => XenForo_Input::STRING,
			'remember' => XenForo_Input::UINT,
			'register' => XenForo_Input::UINT,
			'redirect' => XenForo_Input::STRING,
			'cookie_check' => XenForo_Input::UINT
		));

		if ($data['register'] || $data['password'] === '')
		{
			return $this->responseReroute('XenForo_ControllerPublic_Register', 'index');
		}

		$redirect = ($data['redirect'] ? $data['redirect'] : $this->getDynamicRedirect());

		$loginModel = $this->_getLoginModel();

		if ($data['cookie_check'] && count($_COOKIE) == 0)
		{
			// login came from a page, so we should at least have a session cookie.
			// if we don't, assume that cookies are disabled
			return $this->_loginErrorResponse(
				new XenForo_Phrase('cookies_required_to_log_in_to_site'),
				$data['login'],
				true,
				$redirect
			);
		}

		$needCaptcha = $loginModel->requireLoginCaptcha($data['login']);
		if ($needCaptcha)
		{
			if (!XenForo_Captcha_Abstract::validateDefault($this->_input, true))
			{
				$loginModel->logLoginAttempt($data['login']);

				return $this->_loginErrorResponse(
					new XenForo_Phrase('did_not_complete_the_captcha_verification_properly'),
					$data['login'],
					true,
					$redirect
				);
			}
		}

		$userModel = $this->_getUserModel();

		$userId = $userModel->validateAuthentication($data['login'], $data['password'], $error);
		if (!$userId)
		{
			$loginModel->logLoginAttempt($data['login']);

			return $this->_loginErrorResponse(
				$error,
				$data['login'],
				($needCaptcha || $loginModel->requireLoginCaptcha($data['login'])),
				$redirect
			);
		}

		$loginModel->clearLoginAttempts($data['login']);

		if ($data['remember'])
		{
			$userModel->setUserRememberCookie($userId);
		}

		XenForo_Model_Ip::log($userId, 'user', $userId, 'login');

		$userModel->deleteSessionActivity(0, $this->_request->getClientIp(false));

		$session = XenForo_Application::get('session');

		$session->changeUserId($userId);
		XenForo_Visitor::setup($userId);

		$acpModel = new ACPanelBridge_Model_ACPFunctions();
		if( $acpModel->user_exists($userId) === false )
		{
			// figure out the group
			$cUserInfo = $userModel->getFullUserById($userId);
			$gid = $userModel->getACPanelGroups($cUserInfo, XenForo_Application::get('options')->acpOpt_groupAssoc);
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
					$uname = $data['login'];
					$skey = md5($uname . $config['secretkey']);

					$user_state = $cUserInfo['user_state'];
					$user_state = ($user_state == 'valid') ? 'valid' : (($user_state != 'email_confirm') ? 'moderated' : 'email_confirm');

					$customFields = unserialize($cUserInfo['custom_fields']);

					$insertValues = array(
						'username' => $cUserInfo['username'],
						'password' => md5($data['password']),
						'mail' => $cUserInfo['email'],
						'usergroupid' => $gid,
						'ipaddress' => $acpModel->getRealIpAddr(),
						'secretkey' => $skey,
						'timezone' => $acpModel->getACPTimeZone($cUserInfo['timezone']),
						'reg_date' => $cUserInfo['register_date'],
						'user_state' => $user_state,
						'avatar' => $cUserInfo['avatar_date'],
						'icq' => $customFields['icq'],
					);
					$acpModel->insert($userId, $insertValues);
				}
				else
				{
					if( XenForo_Application::get('options')->acpOpt_debugFile )
					{
						XenForo_Helper_File::log(
							'log-error',
							new XenForo_Phrase('acp_not_path') . ' : ' . new XenForo_Phrase('acp_user_not_created', array('username' => $data['login']))
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
						new XenForo_Phrase('acp_not_assoc') . ' : ' . new XenForo_Phrase('acp_user_not_created', array('username' => $data['login']))
					);
				}
			}
		}

		return $this->responseRedirect(
			XenForo_ControllerResponse_Redirect::SUCCESS,
			$redirect
		);
	}
}