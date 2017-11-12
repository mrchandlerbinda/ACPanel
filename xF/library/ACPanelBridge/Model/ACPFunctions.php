<?php

class ACPanelBridge_Model_ACPFunctions extends ACPanelBridge_Model_ACPDatabase
{
	public function getACPTimeZone($xfTimeZone)
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

		return (isset($timeZones[$xfTimeZone])) ? $timeZones[$xfTimeZone] : 0;
	}

	public function getRealIpAddr()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	public function delete($xfUserId)
	{
		$this->_getDb()->delete('acp_users', 'uid = ' . $xfUserId);
		if( $productID = $this->_getDb()->fetchOne('SELECT productid FROM acp_products WHERE productid = "gameAccounts"') )
		{
			$this->_getDb()->delete('acp_players', 'userid = ' . $xfUserId);
			$this->_getDb()->delete('acp_access_mask_players', 'userid = ' . $xfUserId);
		}
	}

	public function user_exists($xfUserId)
	{
		return ($productID = $this->_getDb()->fetchOne('SELECT uid FROM acp_users WHERE uid = ?', $xfUserId)) ? true : false;
	}

	public function insert($xfUserId, array $insertArray)
	{
		// insert the data into the admins table
		$insertArray['uid'] = $xfUserId;
		$this->_getDb()->insert('acp_users', $insertArray);
	}

	public function update($xfUserId, array $updateArray)
	{
		$this->_getDb()->update('acp_users', $updateArray, 'uid = ' . $xfUserId);
	}

	public function updateLastActivity($xfUserId, array $updateArray)
	{
		$this->_getDb()->update('acp_users', $updateArray, 'uid = ' . $xfUserId);
	}

	public function getChildLinks($acpUserId, $userLang)
	{
		$userLang = ($userLang == 2) ? 'lw_ru' : 'lw_en';

		$query = $this->_getDb()->fetchAll('
			SELECT acp_category.categoryid, acp_category.title AS title_original, acp_lang_words.'.$userLang.' AS title FROM `acp_category`
			LEFT JOIN `acp_users` ON acp_users.uid = ?
			LEFT JOIN `acp_lang_words` ON REPLACE(acp_category.title, "@@", "") = acp_lang_words.lw_word
			LEFT JOIN `acp_usergroups` ON acp_users.usergroupid = acp_usergroups.usergroupid
			WHERE FIND_IN_SET(acp_category.categoryid, acp_usergroups.read_category) AND acp_category.sectionid IS NULL AND acp_category.display_order != 0
			ORDER BY acp_category.display_order
		', $acpUserId);

		$return = array();
		foreach( $query AS $res )
		{
			if( !$res['title'] )
			{
				$res['title'] = $res['title_original'];
			}

			$return[] = array('link'=>$res['categoryid'],'title'=>$res['title']);
		}

		return $return;
	}
}