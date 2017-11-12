<?php

class ACPanelBridge_Model_ACPGroup extends ACPanelBridge_Model_ACPDatabase
{
	public function getACPanelGroupOptions($selectedGroupId)
	{
		$userGroups = array();
		$userGroups[] = array(
			'label' => new XenForo_Phrase('acp_select_group'),
			'value' => '',
			'selected' => ''
		);
		foreach ($this->_getACPanelGroup() as $acpGroup)
		{
			$userGroups[] = array(
				'label' => $acpGroup['usergroupname'],
				'value' => $acpGroup['usergroupid'],
				'selected' => ($selectedGroupId == $acpGroup['usergroupid'])
			);
		}

		return $userGroups;
	}

	public function _getACPanelGroup()
	{
		return $this->_getDb()->fetchAll('
		SELECT usergroupid, usergroupname
		FROM acp_usergroups
		WHERE usergroupid > 0
		');
	}
}