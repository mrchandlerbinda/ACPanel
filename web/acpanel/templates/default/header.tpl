<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
	<title>{if $cat_current}{$cat_current.title}{else}{if $section_current.id == 1}{$home_title}{else}{$section_current.title}{/if}{/if}{if $site_name && $cat_current || ($section_current.id != 1 && !$cat_current)} | {$site_name|htmlspecialchars}{/if}</title>
	<link href="acpanel/templates/{$tpl}/css/style.css" rel="stylesheet" type="text/css" />
	<link href="acpanel/templates/{$tpl}/css/humanmsg.css" rel="stylesheet" type="text/css" />

	<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=7" /><![endif]-->
	<!--[if lt IE 8]><link href="acpanel/templates/{$tpl}/css/ie.css" rel="stylesheet" type="text/css" /><![endif]-->
	<!--[if IE]><script type="text/javascript" src="acpanel/scripts/js/excanvas.js"></script><![endif]-->

	<script type="text/javascript" src="acpanel/scripts/js/jquery.js"></script>
	<script type="text/javascript">jQuery.noConflict();</script>
	<script type="text/javascript" src="acpanel/scripts/js/jquery.img.preload.js"></script>
	<script type="text/javascript" src="acpanel/scripts/js/jquery.select_skin.js"></script>
	<script type="text/javascript" src="acpanel/scripts/js/jquery.pngfix.js"></script>
	<script type="text/javascript" src="acpanel/scripts/js/cron.js"></script>
	<script type="text/javascript" src="acpanel/scripts/js/custom.js"></script>
	<script type="text/javascript" src="acpanel/scripts/js/humanmsg.js"></script>
	{$headinclude}
	{if $cat_current.description || $section_current.description}<meta name="description" content="{if $cat_current}{$cat_current.description}{else}{$section_current.description}{/if}" />{/if}
</head>
<body>
	{if $site_disabled}<div class="site_disabled">@@offline_warning@@</div>{/if}
	<div id="hld">
		<div class="wrapper">
			<div id="top-header">
				<div class="inner-container clearfix">
					<div id="home"><a href="/"><img src="acpanel/images/home.png" alt="На главную!" title="@@go_home@@" /></a></div>
					<div id="{if count($menu_sections) < 2}not-wrap{else}h-wrap{/if}">
						<div class="inner">
							<h1>
								<a href="{if $section_current.id == 1}{$home}{elseif $section_current.url}{$section_current.url}{else}?cat={$section_current.id}{/if}"><span class="select-cat">{$section_current.title}</span></a>
								<span class="h-arrow"></span>
							</h1>
							{if count($menu_sections) > 1}
								<ul class="clearfix">
									{foreach from=$menu_sections key=k item=i}
										<li{if $k == $section_current.id} class="current"{/if}><a href="{if $k == 1}{$home}{elseif $i.url}{$i.url}{else}?cat={$k}{/if}"><span>{$i.title}</span></a></li>
									{/foreach}
								</ul>
							{/if}
						</div>
					</div>
					<div id="userbox">
						{if $isuser.username}
						<div class="inner">
							<strong><a href="{$home}?do=profile">{$isuser.username}</a></strong>
						</div>
						<a id="logout" href="{$home}?logout">log out<span class="ir"></span></a>
						{else}
						<div class="inner">
							<a class="signin" href="{$home}?do=login"><strong>@@sign_in@@</strong></a>
						</div>
						{/if}
					</div>
				</div>
			</div>
			<div id="header">
				<div class="inner-container clearfix">
					<div class="hdrl"></div>
					<div class="hdrr"></div>
					{if !empty($menu_categories)}
					<ul id="nav">
						{assign var='level' value=''}
						{foreach from=$menu_categories name=catmenu key=k item=cat}
							{if $level}
								{if $cat.level > $level}
									<ul>
								{elseif $cat.level < $level}
									</li></ul></li>
								{else}
									</li>
								{/if}
							{/if}
							<li{if $cat.level == 1} class="menu{if $cat_current.left < $cat.right && $cat_current.left >= $cat.left} active{/if}"{/if}><a href="{if $cat.url}{$cat.url}"{elseif !$cat.link}#" onclick="return false;"{else}{$home}?cat={$section_current.id}&do={$k}"{/if}><span>{$cat.title}</span></a>
							{assign var='level' value=$cat.level}
							{if $smarty.foreach.catmenu.last}{if $level > 1}</li></ul></li>{else}</li>{/if}{/if}
						{/foreach}
					</ul>
					{/if}
					<div class="user">
						<div class="language">
							<form id="forma-lang" method="post" action="">
								<select name="l">
									{foreach from=$arr_lang item=lng key=k}
										<option value="{$k}"{if $get_lang == $k} selected{/if}>{$lng.0}</option>
									{/foreach}
								</select>
							</form>
						</div>
					</div>
				</div>
			</div>