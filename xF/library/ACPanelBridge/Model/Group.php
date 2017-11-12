<?php 

class ACPanelBridge_Model_Group extends XenForo_Model
{
	public function getUserGroupOptions($selectedGroupIds)
	{
		$acpGroupModel = new ACPanelBridge_Model_ACPGroup();
		
		$userGroups = array();
		foreach ($this->getUserGroups() AS $userGroup)
		{
			$selectedACPGroupId = (array_key_exists($userGroup['user_group_id'], $selectedGroupIds) && is_array($selectedGroupIds[$userGroup['user_group_id']]) && array_key_exists('acp_group_id', $selectedGroupIds[$userGroup['user_group_id']])) ? $selectedGroupIds[$userGroup['user_group_id']]['acp_group_id'] : array(); 
			
			$userGroups[] = array(
				'label' => $userGroup['title'],
				'value' => $userGroup['user_group_id'],
				'selected' => in_array($userGroup['user_group_id'], array_keys($selectedGroupIds)),
				'acp_group_id' => $acpGroupModel->getACPanelGroupOptions($selectedACPGroupId)
			);
		}
		
		return $userGroups;
	}
	
	public function getUserGroups()
	{
		return $this->_getDb()->fetchAll('
		SELECT user_group_id, title
		FROM xf_user_group
		ORDER BY user_group_id
		');
	}
}