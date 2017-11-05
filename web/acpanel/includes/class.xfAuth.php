<?php 

class XF_auth 
{
	/**
	* The path to the xenForo install
	* @var string
	*/
	private $fileDir = '';
	
	
	/**
	* The base URL to xenForo 
	* @var string
	*/
	private $forumUrl = '';
	
	
	/**
	* The xFUser instance
	*/
	private $xfUser = null;

	private $dependencies;
	
	
	/**
	* Get the ACPanel Instance, load up the config
	* and then try to auth a session with xF to see
	* if there is one.
	*/
	function __construct($config)  
	{
		$this->fileDir  = $config['fileDir'];
		$this->forumUrl = $config['forumUrl'];
		
		//$this->authenticateSession();
		$this->authenticateSession($config);
		
		// DO we need this here ?
		#$this->set_userinfo($this->default_user);
	}
	
	
	/**
	* Uses the XenForo_Autoloader to initialize and startPublicSession to get
	* and instance of the Visitor, if there is one. 
	* @return int
	*/
	//function authenticateSession()
	function authenticateSession($config)
	{
		/**
		* Get the xenForo Autoloader
		*/
		if( is_dir($this->fileDir) )
		{
			$startTime = microtime(true);
			require_once($this->fileDir . '/library/XenForo/Autoloader.php');
			XenForo_Autoloader::getInstance()->setupAutoloader($this->fileDir . '/library');
			
			/**
			* initialize
			*/
			//XenForo_Application::initialize($this->fileDir . '/library', $this->fileDir);
			if( isset($config['AJAX']) ) XenForo_Application::initialize($this->fileDir . '/library', $this->fileDir);
			XenForo_Application::set('page_start_time', $startTime);

			$this->dependencies = new XenForo_Dependencies_Public();
			$this->dependencies->preLoadData();

			XenForo_Session::startPublicSession();			
			$this->xfUser = XenForo_Visitor::getInstance();
			
			return $this->xfUser->getUserId();
		}
		die('no path');
		// TODO: ACP error log
		return false;
	}
	
	
	/**
	* Wrapper function to get User Id.
	* @return int 
	*/
	public function getUserId()
	{
		return $this->xfUser->getUserId();
	}
	
	
	/**
	* Checks if the current user is logged in to xenForo
	* @return boolean
	*/
	public function isLoggedIn()
	{
		return (bool)$this->xfUser->getUserId();
	}
	
	/**
	* Checks if the current user is a xF super administrator
	* @return bool
	*/
	public function isSuperAdmin()
	{
		return (bool)$this->xfUser->isSuperAdmin();
	}	
	
	/**
	* Wrapper function for xF Visitor instance
	*/
	public function get($name)
	{
		return $this->xfUser->get($name);
	}

	/**
	* Delete the current session and log out
	*/
	public function logout()
	{
		if( $this->xfUser->get('is_admin') )
		{
			$adminSession = new XenForo_Session(array('admin' => true));
			$adminSession->start();
			if( $adminSession->get('user_id') == $this->xfUser->getUserId() )
			{
				$adminSession->delete();
			}
		}
		XenForo_Model::create('XenForo_Model_Session')->processLastActivityUpdateForLogOut($this->xfUser->getUserId());
		XenForo_Application::get('session')->delete();
		XenForo_Helper_Cookie::deleteAllCookies(
			array('session'),
			array('user' => array('httpOnly' => false))
		);
		$this->xfUser->setup(0);
		return;
	}

	public function setVisitorLanguage($langID, $userID = 0)
	{
		if( !$userID )
			$userID = $this->xfUser->getUserId();

		if( $userID )
		{
			$writer = XenForo_DataWriter::create('XenForo_DataWriter_User');
			$writer->setExistingData($userID);
			$writer->set('language_id', $langID);
			$writer->save();

			XenForo_Helper_Cookie::deleteCookie('language_id');
		}
		else
		{
			XenForo_Helper_Cookie::setCookie('language_id', $langID, 86400 * 365);
		}
	}

	public function getCurrentUser()
	{	 
		if( $this->xfUser->getUserId() )
		{
			$dbUserModel = XenForo_Model::create('XenForo_Model_User');
			$cUserInfo = $dbUserModel->getFullUserById($this->xfUser->getUserId());
		}
		return $cUserInfo;
	}

	public function getRightContent()
	{	
		$result = XenForo_Application::get('db')->fetchOne("
			SELECT a.property_value FROM `xf_style_property` a
			LEFT JOIN `xf_style` c ON c.style_id = a.style_id
			LEFT JOIN `xf_style_property_definition` b ON a.style_id = b.definition_style_id
			WHERE c.title = 'xenBlueStyle' AND b.property_name = 'rightContentAreaContent'
			AND a.property_definition_id = b.property_definition_id
		");

		return $result;
	}

	public function createUser($userName, $userMail, $userPassword, $userState, array $additionalData = array())
	{
		$writer = XenForo_DataWriter::create('XenForo_DataWriter_User');
		$writer->set('username', $userName);
		$writer->set('email', $userMail);
		$writer->setPassword($userPassword);
		$writer->set('user_state', $userState);
		$uSetGroup = false;

		if( isset($additionalData['user_group_id']) )
		{			
			if( ($k = $this->_getXFGroupID($additionalData['user_group_id'])) !== false )
			{
				$writer->set('user_group_id', $k);
				$uSetGroup = true;
			}
			else
			{
				if( XenForo_Application::get('options')->acpOpt_debugFile )
				{
					XenForo_Helper_File::log(
						'log-error',
						new XenForo_Phrase('acp_not_assoc') . ' : ' . new XenForo_Phrase('acp_user_create_error')
					);
				}
			}
		}

		if( !$uSetGroup )
		{
			$writer->set('user_group_id', XenForo_Model_User::$defaultRegisteredGroupId);
		}
		
		foreach( $additionalData AS $data => $key )
		{
			switch($data)
			{
				case "timezone":
					$writer->set($data, $this->getXFTimeZone($key));
					break;

				case "icq":
					$writer->setCustomFields(array($data => $key), array($data));
					break;

				default:
					break;
			}
		}
		
		$writer->save();
		$user = $writer->getMergedData();

		if( $userState == 'email_confirm' )
		{
			XenForo_Model::create('XenForo_Model_UserConfirmation')->sendEmailConfirmation($user);
		}

		return $user;
	}

	private function _getXFGroupID($idACP)
	{
		$arrayOptions = XenForo_Application::get('options')->acpOpt_groupAssoc;

		foreach($arrayOptions as $k => $v)
		{
			if( isset($v['acp_group_id']) )
				if( $v['acp_group_id'] == $idACP )
					return $k;
		}

		return false;
	}

	public function setLogin($iUserID)
	{
		$dbUserModel = XenForo_Model::create('XenForo_Model_User');
		$dbUserModel->setUserRememberCookie($iUserID);
		XenForo_Model_Ip::log($iUserID, 'user', $iUserID, 'login');
		$dbUserModel->deleteSessionActivity(0, $_SERVER['REMOTE_ADDR']);
		$cSession = XenForo_Application::get('session');
		$cSession->changeUserId($iUserID);
		XenForo_Visitor::setup($iUserID);
	}

	public function userLogin($sLogin, $sPassword, $bRemember = true)
	{ 
		error_reporting(E_ALL);
		restore_error_handler();
		restore_exception_handler();
		 
		$dbLoginModel = XenForo_Model::create('XenForo_Model_Login');
		$dbUserModel = XenForo_Model::create('XenForo_Model_User');
		$sError = "";
		 
		$iUserID = $dbUserModel->validateAuthentication($sLogin, $sPassword, $sError);
		if( !$iUserID )
		{
			$dbLoginModel->logLoginAttempt($sLogin);
			return $sError;
		}
		 
		$dbLoginModel->clearLoginAttempts($sLogin);
		 
		if( $bRemember )
		{
			$dbUserModel->setUserRememberCookie($iUserID);
		}
		 
		XenForo_Model_Ip::log($iUserID, 'user', $iUserID, 'login');		 
		$dbUserModel->deleteSessionActivity(0, $_SERVER['REMOTE_ADDR']);		 
		$cSession = XenForo_Application::get('session');
		$cSession->changeUserId($iUserID);
		XenForo_Visitor::setup($iUserID);
		 
		return $iUserID;
	}

	public function getAvatarFilePath($size, $userID = 0)
	{
		if( !$userID ) 
			$userID = $this->xfUser->getUserId();

		return sprintf($this->forumUrl.'data/avatars/%s/%d/%d.jpg',
			$size,
			floor($userID / 1000),
			$userID
		);
	}

	public function deleteAvatar()
	{
		XenForo_Model::create('XenForo_Model_Avatar')->deleteAvatar($this->xfUser->getUserId());
	}

	public function uploadAvatar()
	{
		$avatar = XenForo_Upload::getUploadedFile('userfile');
		$dbAvatarModel = XenForo_Model::create('XenForo_Model_Avatar');
		$avatarData = $dbAvatarModel->uploadAvatar($avatar, $this->xfUser->getUserId(), false);

		return new XenForo_Phrase('upload_completed_successfully');
	}

	public function setUserData($userID, $additionalData = array())
	{
		$writer = XenForo_DataWriter::create('XenForo_DataWriter_User');
		$writer->setExistingData($userID);
		$email_changed = false;

		foreach( $additionalData AS $field => $value )
		{
			switch($field)
			{
				case "user_group_id":
					if( ($k = $this->_getXFGroupID($value)) !== false )
					{
						$writer->set($field, $k);
					}
					else
					{
						if( XenForo_Application::get('options')->acpOpt_debugFile )
						{
							XenForo_Helper_File::log(
								'log-error',
								new XenForo_Phrase('acp_not_assoc') . ' : ' . new XenForo_Phrase('acp_user_edit_error', array('userid' => $userID))
							);
						}

						return array('user_id' => 0);
					}
					break;

				case "password":
					$writer->setPassword($value);
					break;

				case "timezone":
					$writer->set($field, $this->getXFTimeZone($value));
					break;

				case "blockScroll":
					if( !$value )
						$value = array();
					else
						$value = array(1=>1);

					$writer->setOption(XenForo_DataWriter_User::OPTION_ADMIN_EDIT, true);
					$writer->setCustomFields(array($field => $value), array($field));
					break;

				case "icq":
					$writer->setCustomFields(array($field => $value), array($field));
					break;

				default:
					$writer->set($field, $value);
					break;
			}
		}

		if( $writer->isChanged('email') && XenForo_Application::get('options')->get('registrationSetup', 'emailConfirmation') )
		{
			$writer->set('user_state', 'email_confirm');
			$email_changed = true;
		}
		
		$writer->save();
		$user = $writer->getMergedData();

		if( $email_changed )
		{
			XenForo_Model::create('XenForo_Model_UserConfirmation')->sendEmailConfirmation($user);
		}

		return $user;
	}

	public function getXFTimeZone($acpTimeZone)
	{
		$timeZones = array(
			'Pacific/Midway' => '-11',
			'Pacific/Apia' => '-11',
			'Pacific/Honolulu' => '-10',
			'Pacific/Marquesas' => '-10',
			'America/Anchorage' => '-9',
			'America/Los_Angeles' => '-8',
			'America/Santa_Isabel' => '-8',
			'America/Tijuana' => '-8',
			'America/Denver' => '-7',
			'America/Chihuahua' => '-7',
			'America/Phoenix' => '-7',
			'America/Chicago' => '-6',
			'America/Belize' => '-6',
			'America/Mexico_City' => '-6',
			'Pacific/Easter' => '-6',
			'America/New_York' => '-5',
			'America/Havana' => '-5',
			'America/Bogota' => '-5',
			'America/Caracas' => '-4.5',
			'America/Halifax' => '-4',
			'America/Goose_Bay' => '-4',
			'America/Asuncion' => '-4',
			'America/Santiago' => '-4',
			'America/Cuiaba' => '-4',
			'America/La_Paz' => '-4',
			'Atlantic/Stanley' => '-4',
			'America/St_Johns' => '-3.5',
			'America/Argentina/Buenos_Aires' => '-3',
			'America/Argentina/San_Luis' => '-3',
			'America/Argentina/Mendoza' => '-3',
			'America/Godthab' => '-3',
			'America/Montevideo' => '-3',
			'America/Sao_Paulo' => '-3',
			'America/Miquelon' => '-3',
			'America/Noronha' => '-2',
			'Atlantic/Cape_Verde' => '-1',
			'Atlantic/Azores' => '-1',
			'Europe/London' => '0',
			'Africa/Casablanca' => '0',
			'Atlantic/Reykjavik' => '0',
			'Europe/Amsterdam' => '1',
			'Africa/Algiers' => '1',
			'Africa/Windhoek' => '1',
			'Africa/Tunis' => '1',
			'Europe/Athens' => '2',
			'Africa/Johannesburg' => '2',
			'Asia/Amman' => '2',
			'Asia/Beirut' => '2',
			'Africa/Cairo' => '2',
			'Asia/Jerusalem' => '2',
			'Europe/Minsk' => '2',
			'Asia/Gaza' => '2',
			'Asia/Damascus' => '2',
			'Africa/Nairobi' => '3',
			'Europe/Kaliningrad' => '3',
			'Asia/Tehran' => '3.5',
			'Europe/Moscow' => '4',
			'Asia/Dubai' => '4',
			'Asia/Yerevan' => '4',
			'Asia/Baku' => '4',
			'Indian/Mauritius' => '4',
			'Asia/Kabul' => '4.5',
			'Asia/Tashkent' => '5',
			'Asia/Kolkata' => '5.5',
			'Asia/Kathmandu' => '5.75',
			'Asia/Dhaka' => '6',
			'Asia/Yekaterinburg' => '6',
			'Asia/Almaty' => '6',
			'Asia/Rangoon' => '6.5',
			'Asia/Bangkok' => '7',
			'Asia/Novosibirsk' => '7',
			'Asia/Hong_Kong' => '8',
			'Asia/Krasnoyarsk' => '8',
			'Asia/Singapore' => '8',
			'Australia/Perth' => '8',
			'Asia/Irkutsk' => '9',
			'Asia/Tokyo' => '9',
			'Asia/Seoul' => '9',
			'Australia/Adelaide' => '9.5',
			'Australia/Darwin' => '9.5',
			'Australia/Brisbane' => '10',
			'Australia/Sydney' => '10',
			'Asia/Yakutsk' => '10',
			'Pacific/Noumea' => '11',
			'Asia/Vladivostok' => '11',
			'Pacific/Norfolk' => '11',
			'Asia/Anadyr' => '12',
			'Pacific/Auckland' => '12',
			'Pacific/Fiji' => '12',
			'Asia/Magadan' => '12',
			'Pacific/Chatham' => '12',
			'Pacific/Tongatapu' => '12',
			'Pacific/Kiritimati' => '12'
		);

		return ($key_tz = array_search($acpTimeZone, $timeZones)) ? $key_tz : 'Europe/London';
	}

	public function getUserInfo($userID)
	{
		$dbUserModel = XenForo_Model::create('XenForo_Model_User');
		$cUser = $dbUserModel->getUserById($userID, array('join' => XenForo_Model_User::FETCH_USER_PROFILE + XenForo_Model_User::FETCH_LAST_ACTIVITY));

		return $cUser;
	}

	public function deleteUser($userID)
	{
		$writer = XenForo_DataWriter::create('XenForo_DataWriter_User');
		$writer->setExistingData($userID);
		return $writer->delete();
	}

	public function getPhrase($name)
	{
		return new XenForo_Phrase($name);
	}
}