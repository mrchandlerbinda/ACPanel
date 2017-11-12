<?php

class ACPanelBridge_Listeners_Navigation
{
    public static function navtabs(array &$extraTabs, $selectedTabId)
    {
        $positionNav = XenForo_Application::get('options')->acpOpt_navPosition;
        $linkNav = XenForo_Application::get('options')->acpOpt_navLink;
        $visitor = XenForo_Visitor::getInstance();

        if( $user_id = $visitor->getUserId() ) // быстрые ссылки для авторизованного пользователя
        {
            $childLinks = array(
			'0' => array('cat'=>'1','do'=>'16','title'=>'Администрация'),
			'1' => array('cat'=>'1','do'=>'67','title'=>'Список банов'),
			'2' => array('cat'=>'1','do'=>'77','title'=>'Игровой чат'),
			'3' => array('cat'=>'1','do'=>'96','title'=>'Игровой магазин')
            );
        }
        else // быстрые ссылки для гостей
        {
            $childLinks = array(
			'0' => array('cat'=>'1','do'=>'16','title'=>'Администрация'),
			'1' => array('cat'=>'1','do'=>'67','title'=>'Список банов'),
			'2' => array('cat'=>'1','do'=>'77','title'=>'Игровой чат')
            );
        }
		
        $extraTabs['acp'] = array(
		'title' => new XenForo_Phrase('acp_home'),
		'href' => XenForo_Link::buildPublicLink($linkNav),		
		'position' => $positionNav,
		'selected' => ($selectedTabId == 'acp'),
		'linksTemplate' => 'acp_game_navtabs',
		'childLinks' => $childLinks,
        );
    }
}