<?php

class ACPanelBridge_Listeners_TemplateHook
{
	public static function templateHook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template)
	{
		switch($hookName)
		{
			case "account_wrapper_sidebar_settings":

				$contents .= "<li class='section'><h4 class='subHeading'>" . new XenForo_Phrase('acp_settings') . "</h4><ul><li><a class='primaryContent' target='_blank' href='" . XenForo_Application::get('options')->acpOpt_navLink . "?do=profile&s=2'>" . new XenForo_Phrase('acp_my_profile') . "</a></ul></li></li>";
				break;

			/*case "navigation_visitor_tab_links2":

				$contents .= "<li><a href='" . XenForo_Application::get('options')->acpOpt_navLink . "?do=profile&s=2' target='_blank'>" . new XenForo_Phrase('acp_my_profile') . "</a></li>";
				break;*/
		}
	}
}