<?php

class ACPanelBridge_Model_User extends XFCP_ACPanelBridge_Model_User
{
	public function getACPanelGroups($xfUser, $groupMapping = null)
	{
		if( is_array($xfUser) )
		{
			$user['user_group_id'] = $xfUser['user_group_id'];
			$user['secondary_group_ids'] = $xfUser['secondary_group_ids'];
		}
		else
		{
			$user['user_group_id'] = $xfUser->get('user_group_id');
			$user['secondary_group_ids'] = $xfUser->get('secondary_group_ids');
		}

		// if the primary group is mapped, return that info
		if( array_key_exists($user['user_group_id'], $groupMapping) )
		{
			return $groupMapping[$user['user_group_id']]['acp_group_id'];
		}

		// go through the secondary groups to see if they need added
		$secondaryGroups = ($user['secondary_group_ids'] ? explode(',', $user['secondary_group_ids']) : array());
		foreach( $secondaryGroups as $groupId )
		{
			// only try to get the group ids if the options have been set for that group
			if( array_key_exists($groupId, $groupMapping) )
			{
				return $groupMapping[$groupId]['acp_group_id'];
			}
		}
	}
}