<?php

class ACPanelBridge_Model_Session extends XFCP_ACPanelBridge_Model_Session
{
	public function updateUserLastActivityFromSessions($cutOffDate = null)
	{
		if ($cutOffDate === null)
		{
			$cutOffDate = XenForo_Application::$time;
		}

		$userSessions = $this->getSessionActivityRecords(array(
			'userLimit' => 'registered',
			'getInvisible' => true,
			'cutOff' => array('<=', $cutOffDate)
		));

		$acpModel = new ACPanelBridge_Model_ACPFunctions();
		$db = $this->_getDb();
		XenForo_Db::beginTransaction($db);

		foreach( $userSessions AS $userSession )
		{
			$acpModel->updateLastActivity($userSession['user_id'], array('last_visit' => $userSession['view_date']));

			$db->update('xf_user',
				array('last_activity' => $userSession['view_date']),
				'user_id = ' . $db->quote($userSession['user_id'])
			);
		}

		XenForo_Db::commit($db);
	}

	public function processLastActivityUpdateForLogOut($userId)
	{
		if( !$userId )
		{
			return;
		}

		$userSessions = $this->getSessionActivityRecords(array(
			'user_id' => $userId,
			'getInvisible' => true
		));
		if( !$userSessions )
		{
			return;
		}

		$acpModel = new ACPanelBridge_Model_ACPFunctions();
		$db = $this->_getDb();
		XenForo_Db::beginTransaction($db);

		// really should only be 1 session, but hey that's the structure of the return and no harm :)
		foreach( $userSessions AS $userSession )
		{
			$acpModel->updateLastActivity($userSession['user_id'], array('last_visit' => $userSession['view_date']));

			$db->update('xf_user',
				array('last_activity' => $userSession['view_date']),
				'user_id = ' . $db->quote($userSession['user_id'])
			);
		}

		$db->delete('xf_session_activity', 'user_id = ' . $db->quote($userId));

		XenForo_Db::commit($db);
	}
}