/*
*********************************************************************************
*
*   ACP Vote Ban & Kick v1.5
*   Last Update: 24/03/2012
*   a114 Team
*
*   by Hafner
*   Support: http://www.a114games.com/community/threads/1660/
*
*
*********************************************************************************
*/

#include <amxmodx>
#include <amxmisc>
#include <regex>
#include <sqlx>

#define ACP_GENERAL

#if defined ACP_GENERAL
	#include <acp>
#endif

#define PLUGIN_NAME "[ACP] Vote Ban & Kick"
#define AUTHOR "Hafner"
#define VERSION "1.5"

#define VBK_NUMBER_YES "3"
#define VBK_NUMBER_NO "5"

#define VBK_ACTION_BAN 1
#define VBK_ACTION_KICK 2

#define REGEX_STEAMID_PATTERN_LEGAL "^^STEAM_0:(0|1):\d{4,8}$"
#define REGEX_STEAMID_PATTERN_ALL "^^STEAM_0:(0|1):\d+$"

new Regex:g_SteamID_pattern;
new g_regex_return;
new Handle:info;
new g_voteFinish;

#define IsValidAuthid(%1) (regex_match_c(%1, g_SteamID_pattern, g_regex_return) > 0)

enum _:PlayerData
{
	Pname[44],
	Pip[22],
	Psteam[35],
	Pmyac[35],
	Float:Pkickweight,
	Float:Pbanweight,
	Padmin,
	Pkickimmunity,
	Pbanimmunity,
	Pkickaccess,
	Pbanaccess,
	Pvoteaction,
	Pvoteuserid,
	Pvotereason[128],
	Pvotetime,
	Plastvote
}

enum _:VoteData
{
	Vuserid,
	Vnomid,
	Vlastvote,
	Vall,
	Float:Vyes
}

new g_Player_Data[33][PlayerData];
new g_Vote_Data[VoteData];
new g_Callback[2];
new g_iAdmins;
new g_MaxClients; 
new g_kickReasons[5][128], g_banReasons[5][128], g_banTimes[5];

// pcvars
new pcvar_voteban, pcvar_votekick, pcvar_steam_validated, pcvar_voteratio, pcvar_votetime, pcvar_votedelay, pcvar_votetimeout, pcvar_minplayers, pcvar_flagstop
new pcvar_kick_steam, pcvar_kick_immunity, pcvar_kick_access, pcvar_kick_voteweight, pcvar_kick_reasonmenu, pcvar_kick_reason, pcvar_shownom
new pcvar_kick_reason1, pcvar_kick_reason2, pcvar_kick_reason3, pcvar_kick_reason4, pcvar_kick_reason5, pcvar_savelogs, pcvar_myac
new pcvar_ban_steam, pcvar_ban_immunity, pcvar_ban_access, pcvar_ban_voteweight, pcvar_ban_cmd
new pcvar_ban_reasonmenu, pcvar_ban_reason, pcvar_ban_time, pcvar_ban_prefix, pcvar_ban_type
new pcvar_ban_reason1, pcvar_ban_reason2, pcvar_ban_reason3, pcvar_ban_reason4, pcvar_ban_reason5
new pcvar_ban_time1, pcvar_ban_time2, pcvar_ban_time3, pcvar_ban_time4, pcvar_ban_time5
new pcvar_sql_host, pcvar_sql_user, pcvar_sql_pass, pcvar_sql_db

public plugin_init()
{
	register_plugin(PLUGIN_NAME, VERSION, AUTHOR)
	
	register_dictionary("acp_votebankick.txt")
	
	register_menucmd(register_menuid("ShowVoteMenu"), 1023, "ShowVoteMenu")
	
	register_clcmd("say /voteban", "VotePreStart")
	register_clcmd("say_team /voteban", "VotePreStart")
	register_clcmd("say /votekick", "VotePreStart")
	register_clcmd("say_team /votekick", "VotePreStart")

	pcvar_voteban = register_cvar("acp_vbk_voteban", "1")
	pcvar_votekick = register_cvar("acp_vbk_votekick", "1")
	pcvar_steam_validated = register_cvar("acp_vbk_steam_validated", "1")
	pcvar_voteratio = register_cvar("acp_vbk_voteratio", "0.75")
	pcvar_votetime = register_cvar("acp_vbk_votetime", "15.0")
	pcvar_votedelay = register_cvar("acp_vbk_votedelay", "300")
	pcvar_votetimeout = register_cvar("acp_vbk_votetimeout", "180")
	pcvar_minplayers = register_cvar("acp_vbk_minplayers", "5")
	pcvar_flagstop = register_cvar("acp_vbk_flag_stop", "a")
	pcvar_shownom = register_cvar("acp_vbk_shownom", "0")
	pcvar_savelogs = register_cvar("acp_vbk_savelogs", "1")
	pcvar_myac = register_cvar("acp_vbk_myac", "1")

	pcvar_kick_steam = register_cvar("acp_vbk_kick_steam", "1")
	pcvar_kick_immunity = register_cvar("acp_vbk_kick_immunity", "t")
	pcvar_kick_access = register_cvar("acp_vbk_kick_access", "t")
	pcvar_kick_voteweight = register_cvar("acp_vbk_kick_voteweight", "1.5")
	pcvar_kick_reasonmenu = register_cvar("acp_vbk_kick_reasonmenu", "1")
	pcvar_kick_reason = register_cvar("acp_vbk_kick_reason", "[VOTEKICK]")
	pcvar_kick_reason1 = register_cvar("acp_vbk_kick_reason1", "Lamer")
	pcvar_kick_reason2 = register_cvar("acp_vbk_kick_reason2", "Flood")
	pcvar_kick_reason3 = register_cvar("acp_vbk_kick_reason3", "MaT")
	pcvar_kick_reason4 = register_cvar("acp_vbk_kick_reason4", "")
	pcvar_kick_reason5 = register_cvar("acp_vbk_kick_reason5", "")

	pcvar_ban_steam = register_cvar("acp_vbk_ban_steam", "1")
	pcvar_ban_immunity = register_cvar("acp_vbk_ban_immunity", "t")
	pcvar_ban_access = register_cvar("acp_vbk_ban_access", "t")
	pcvar_ban_voteweight = register_cvar("acp_vbk_ban_voteweight", "1.5")
	pcvar_ban_cmd = register_cvar("acp_vbk_ban_command", "amx_ban %userid% %time% %reason%")
	pcvar_ban_type = register_cvar("acp_vbk_ban_type", "0")
	pcvar_ban_reasonmenu = register_cvar("acp_vbk_ban_reasonmenu", "1")
	pcvar_ban_reason = register_cvar("acp_vbk_ban_reason", "[VOTEBAN]")
	pcvar_ban_time = register_cvar("acp_vbk_ban_time", "60")
	pcvar_ban_prefix = register_cvar("acp_vbk_ban_prefix", "[VOTEBAN]")

	pcvar_ban_reason1 = register_cvar("acp_vbk_ban_reason1", "Cheating/ 4uTbI")
	pcvar_ban_time1 = register_cvar("acp_vbk_ban_time1", "180")
	pcvar_ban_reason2 = register_cvar("acp_vbk_ban_reason2", "Advertising/ PekJlaMa")
	pcvar_ban_time2 = register_cvar("acp_vbk_ban_time2", "120")
	pcvar_ban_reason3 = register_cvar("acp_vbk_ban_reason3", "Oskorblenie/ MaT")
	pcvar_ban_time3 = register_cvar("acp_vbk_ban_time3", "60")
	pcvar_ban_reason4 = register_cvar("acp_vbk_ban_reason4", "Laming/ Troublemaker/ FLOOD")
	pcvar_ban_time4 = register_cvar("acp_vbk_ban_time4", "15")
	pcvar_ban_reason5 = register_cvar("acp_vbk_ban_reason5", "Lagged/ Jlarep")
	pcvar_ban_time5 = register_cvar("acp_vbk_ban_time5", "5")

	pcvar_sql_host = register_cvar("acp_sql_host", "localhost")
	pcvar_sql_user = register_cvar("acp_sql_user", "root")
	pcvar_sql_pass = register_cvar("acp_sql_pass", "")
	pcvar_sql_db = register_cvar("acp_sql_db", "amx")

    	g_Callback[0] = menu_makecallback( "MenuCallbackEnabled" )
    	g_Callback[1] = menu_makecallback( "MenuCallbackDisabled" )

	g_MaxClients = get_maxplayers(); 
}

public plugin_cfg()
{
	new configsDir[64]
	get_configsdir(configsDir, 63)
	server_cmd("exec %s/acp/sql.cfg", configsDir)
	server_cmd("exec %s/acp/votebankick.cfg", configsDir)

	new regerror[2];

	if( get_pcvar_num(pcvar_steam_validated) )
		g_SteamID_pattern = regex_compile(REGEX_STEAMID_PATTERN_LEGAL, g_regex_return, regerror, sizeof(regerror) - 1)
	else
		g_SteamID_pattern = regex_compile(REGEX_STEAMID_PATTERN_ALL, g_regex_return, regerror, sizeof(regerror) - 1)	

	if( get_pcvar_num(pcvar_votekick) && get_pcvar_num(pcvar_kick_reasonmenu) )
	{
		get_pcvar_string(pcvar_kick_reason1, g_kickReasons[0], 127)
		get_pcvar_string(pcvar_kick_reason2, g_kickReasons[1], 127)
		get_pcvar_string(pcvar_kick_reason3, g_kickReasons[2], 127)
		get_pcvar_string(pcvar_kick_reason4, g_kickReasons[3], 127)
		get_pcvar_string(pcvar_kick_reason5, g_kickReasons[4], 127)
	}

	if( get_pcvar_num(pcvar_voteban) && get_pcvar_num(pcvar_ban_reasonmenu) )
	{
		get_pcvar_string(pcvar_ban_reason1, g_banReasons[0], 127)
		g_banTimes[0] = get_pcvar_num(pcvar_ban_time1)
		get_pcvar_string(pcvar_ban_reason2, g_banReasons[1], 127)
		g_banTimes[1] = get_pcvar_num(pcvar_ban_time2)
		get_pcvar_string(pcvar_ban_reason3, g_banReasons[2], 127)
		g_banTimes[2] = get_pcvar_num(pcvar_ban_time3)
		get_pcvar_string(pcvar_ban_reason4, g_banReasons[3], 127)
		g_banTimes[3] = get_pcvar_num(pcvar_ban_time4)
		get_pcvar_string(pcvar_ban_reason5, g_banReasons[4], 127)
		g_banTimes[4] = get_pcvar_num(pcvar_ban_time5)
	}

#if !defined ACP_GENERAL
	if( get_pcvar_num(pcvar_savelogs) )
	{
		set_task(0.1, "sql_init")
	}
#endif
}

#if defined ACP_GENERAL
public acp_sql_initialized(Handle:sqlTuple)
{
	if( !get_pcvar_num(pcvar_savelogs) )
	{
		return PLUGIN_HANDLED
	}

	if( info != Empty_Handle )
	{
		log_amx("[VBK] DB Info Tuple from acp_general initialized twice!")
		return PLUGIN_HANDLED
	}
	
	info = sqlTuple

	if( info == Empty_Handle )
	{
		log_amx("[VBK] DB Info Tuple from acp_votebankick is empty! Trying to get a valid one")

		new host[32], user[32], pass[32], db[32]
		get_pcvar_string(pcvar_sql_host, host, 31);
		get_pcvar_string(pcvar_sql_user, user, 31);
		get_pcvar_string(pcvar_sql_pass, pass, 31);
		get_pcvar_string(pcvar_sql_db, db, 31);
		
		info = SQL_MakeDbTuple(host, user, pass, db)
	}

	return PLUGIN_CONTINUE
}

public acp_endmap_func()
{

#else

public sql_init()
{
	new host[32], user[32], pass[32], dbname[32];
	get_pcvar_string(pcvar_sql_host,host,31);
	get_pcvar_string(pcvar_sql_user,user,31);
	get_pcvar_string(pcvar_sql_pass,pass,31);
	get_pcvar_string(pcvar_sql_db,dbname,31);

	info = SQL_MakeDbTuple(host,user,pass,dbname);
}

public plugin_end()
{
	if( info != Empty_Handle )
	{
		SQL_FreeHandle(info)
	}

#endif
}

public client_putinserver(id)
{
	new str_stop[32], str_kick_access[32], str_kick_immunity[32], str_ban_access[32], str_ban_immunity[32], iflags = get_user_flags(id)
	get_pcvar_string(pcvar_flagstop, str_stop, 31)	
	get_pcvar_string(pcvar_kick_access, str_kick_access, 31)	
	get_pcvar_string(pcvar_kick_immunity, str_kick_immunity, 31)
	get_pcvar_string(pcvar_ban_access, str_ban_access, 31)	
	get_pcvar_string(pcvar_ban_immunity, str_ban_immunity, 31)
	
	if(strlen(str_stop) && (iflags & read_flags(str_stop)))
	{
		g_iAdmins++
		g_Player_Data[id][Padmin] = 1
	}

	if(strlen(str_kick_immunity) && (iflags & read_flags(str_kick_immunity)))
	{
		g_Player_Data[id][Pkickimmunity] = 1
	}

	if(strlen(str_ban_immunity) && (iflags & read_flags(str_ban_immunity)))
	{
		g_Player_Data[id][Pbanimmunity] = 1
	}

	if(strlen(str_kick_access) && (iflags & read_flags(str_kick_access)))
	{
		g_Player_Data[id][Pkickweight] = _:get_pcvar_float(pcvar_kick_voteweight)
		g_Player_Data[id][Pkickaccess] = 1
	}
	else
	{
		g_Player_Data[id][Pkickweight] = _:1.0
	}

	if(strlen(str_ban_access) && (iflags & read_flags(str_ban_access)))
	{
		g_Player_Data[id][Pbanweight] = _:get_pcvar_float(pcvar_ban_voteweight)
		g_Player_Data[id][Pbanaccess] = 1
	}
	else
	{
		g_Player_Data[id][Pbanweight] = _:1.0
	}

	get_user_info( id, "*myAC", g_Player_Data[id][Pmyac], 34 )
	get_user_name(id, g_Player_Data[id][Pname], 43)
	get_user_authid(id, g_Player_Data[id][Psteam], 34)
	get_user_ip(id, g_Player_Data[id][Pip], 21, 1)
	g_Player_Data[id][Plastvote] = 0
	
	return PLUGIN_CONTINUE
}

public client_infochanged(id)
{
	get_user_info(id, "name", g_Player_Data[id][Pname], 43)
	
	return PLUGIN_CONTINUE
}

public client_disconnected(id)
{
	if( g_Player_Data[id][Padmin] )
	{
		g_iAdmins--
		g_Player_Data[id][Padmin] = 0
	}

	if( g_Player_Data[id][Pkickimmunity] )
	{
		g_Player_Data[id][Pkickimmunity] = 0
	}

	if( g_Player_Data[id][Pbanimmunity] )
	{
		g_Player_Data[id][Pbanimmunity] = 0
	}

	if( g_Player_Data[id][Pkickaccess] )
	{
		g_Player_Data[id][Pkickaccess] = 0
	}

	if( g_Player_Data[id][Pbanaccess] )
	{
		g_Player_Data[id][Pbanaccess] = 0
	}
	
	return PLUGIN_CONTINUE
}

public VotePreStart(id)
{
	new args[64], action
	read_args(args, charsmax(args))
	remove_quotes(args)

	if( equal(args, "/voteban", 8) )
		action = VBK_ACTION_BAN
	else if( equal(args, "/votekick", 9) )
		action = VBK_ACTION_KICK

	if( !get_pcvar_num(pcvar_voteban) && action == VBK_ACTION_BAN )
	{
		client_print_color(id, print_team_red, "^3[VBK] ^1%L", id, "VBK_BAN_DISABLED")
		return PLUGIN_CONTINUE
	}
	else if( !get_pcvar_num(pcvar_votekick) && action == VBK_ACTION_KICK )
	{
		client_print_color(id, print_team_red, "^3[VBK] ^1%L", id, "VBK_KICK_DISABLED")
		return PLUGIN_CONTINUE
	}
	else if( !PreCheckVote(id, action) )
	{
		return PLUGIN_CONTINUE
	}

	ShowPlayerMenu(id, action)

	return PLUGIN_CONTINUE
}

ShowPlayerMenu(iPlayer, iDo, iPage = 0)
{
	iPage = clamp( iPage, 0, ( get_playersnum() - 1 ) / 7 )

    	new szItem[64], szNum[3], szTitle[64], str_temp[15], hMenu
	formatex( szTitle, charsmax(szTitle), "\r[VBK]\y %L", iPlayer, (iDo == VBK_ACTION_BAN) ? "VBK_MENU_PLAYERS_BAN" : "VBK_MENU_PLAYERS_KICK")

	hMenu = menu_create( szTitle, "MenuActionFunc" )

	new players[32], playersNum, protected
	get_players(players, playersNum, "ch")

    	for( new i = 0; i < playersNum; i++ )
    	{
		if( players[i] == iPlayer )
		{
	        	formatex( str_temp, charsmax(str_temp), "(you)")
			protected = true
		}
		else if( (iDo == VBK_ACTION_BAN && g_Player_Data[players[i]][Pbanimmunity]) || (iDo == VBK_ACTION_KICK && g_Player_Data[players[i]][Pkickimmunity]) )
		{
	        	formatex( str_temp, charsmax(str_temp), "(protected)")
			protected = true
		}
		else if( (iDo == VBK_ACTION_BAN && !get_pcvar_num(pcvar_ban_steam)) || (iDo == VBK_ACTION_KICK && !get_pcvar_num(pcvar_kick_steam)) )
		{
			if( IsValidAuthid(g_Player_Data[players[i]][Psteam]) )
			{
		        	formatex( str_temp, charsmax(str_temp), "(protected)")
				protected = true
			}
			else
			{
		        	formatex( str_temp, charsmax(str_temp), "")
				protected = false
			}
		}
		else if( !get_pcvar_num(pcvar_myac) )
		{
			if( strlen(g_Player_Data[players[i]][Pmyac]) )
			{
		        	formatex( str_temp, charsmax(str_temp), "(protected)")
				protected = true
			}
			else
			{
		        	formatex( str_temp, charsmax(str_temp), "")
				protected = false
			}
		}
		else
		{
	        	formatex( str_temp, charsmax(str_temp), "")
			protected = false
		}

	        formatex( szItem, charsmax(szItem), "%s\R\y%s", g_Player_Data[players[i]][Pname], str_temp )
        	num_to_str( players[i], szNum, charsmax(szNum) )

        	menu_additem( hMenu, szItem, szNum, _, (protected) ? g_Callback[1] : g_Callback[0] )
    	}

	g_Player_Data[iPlayer][Pvoteaction] = iDo 

	menu_setprop( hMenu, MPROP_EXIT, MEXIT_ALL )  
    	menu_display( iPlayer, hMenu, iPage )
}

public MenuActionFunc( iPlayer, hMenu, iItem )
{
    	if( iItem == MENU_EXIT )
    	{
        	menu_destroy( hMenu );
        	return PLUGIN_HANDLED;
    	}
    
    	new iAccess, szNum[ 3 ], hCallback
    	menu_item_getinfo( hMenu, iItem, iAccess, szNum, charsmax( szNum ), _, _, hCallback )
    
    	new iItemIndex = str_to_num( szNum )

    	if( iItem == MENU_BACK || iItem == MENU_MORE )
    	{
	    	menu_destroy( hMenu )
        	ShowPlayerMenu(iPlayer, 1, (iItemIndex/7))
        	return PLUGIN_HANDLED;
    	}
	else
	{
		g_Player_Data[iPlayer][Pvoteuserid] = iItemIndex;
	}

	if( (g_Player_Data[iPlayer][Pvoteaction] == VBK_ACTION_BAN && get_pcvar_num(pcvar_ban_reasonmenu)) || (g_Player_Data[iPlayer][Pvoteaction] == VBK_ACTION_KICK && get_pcvar_num(pcvar_kick_reasonmenu)) )
	{
	    	ShowReasonMenu(iPlayer)
	}
	else
	{
		menu_destroy( hMenu )

		if( g_Player_Data[iPlayer][Pvoteaction] == VBK_ACTION_BAN )
		{
			get_pcvar_string(pcvar_ban_reason, g_Player_Data[iPlayer][Pvotereason], 127)
			g_Player_Data[iPlayer][Pvotetime] = get_pcvar_num(pcvar_ban_time)
		}
		else
			get_pcvar_string(pcvar_kick_reason, g_Player_Data[iPlayer][Pvotereason], 127)


		ShowVoteMenu( iPlayer, g_Player_Data[iPlayer][Pvoteaction] )

		return PLUGIN_HANDLED;
	}

    	menu_destroy( hMenu )
	return PLUGIN_HANDLED;
}

public ShowReasonMenu(id)
{
    	new szItem[64], szNum[3], szTitle[64], action = g_Player_Data[id][Pvoteaction]
	formatex( szTitle, charsmax(szTitle), "\r[VBK]\y %L", id, (action == VBK_ACTION_BAN) ? "VBK_MENU_REASONS_BAN" : "VBK_MENU_REASONS_KICK" )
	new sMenu = menu_create( szTitle, "MenuReasonFunc" )
 
    	for( new i = 0; i < 5; i++ )
    	{
		if( (action == VBK_ACTION_BAN && strlen(g_banReasons[i])) || (action == VBK_ACTION_KICK && strlen(g_kickReasons[i])) )
		{
		        formatex( szItem, charsmax(szItem), "%s", (action == VBK_ACTION_BAN) ? g_banReasons[i] : g_kickReasons[i] )
	        	num_to_str( i, szNum, charsmax(szNum) )
	
	        	menu_additem( sMenu, szItem, szNum, 0 )
		}
    	}

	menu_setprop(sMenu, MPROP_EXIT, MEXIT_ALL)
	menu_display(id, sMenu, 0)
}

public MenuReasonFunc( iPlayer, sMenu, iItem )
{
    	if( iItem == MENU_EXIT )
    	{
        	menu_destroy( sMenu );
		if (is_user_connected(iPlayer))
			ShowPlayerMenu( iPlayer, g_Player_Data[iPlayer][Pvoteaction] )
        	return PLUGIN_HANDLED;
    	}
    
    	new iAccess, szNum[ 3 ], hCallback
    	menu_item_getinfo( sMenu, iItem, iAccess, szNum, charsmax( szNum ), _, _, hCallback )
    	menu_destroy( sMenu )    

    	new iItemIndex = str_to_num( szNum )

	formatex( g_Player_Data[iPlayer][Pvotereason], 127, "%s", (g_Player_Data[iPlayer][Pvoteaction] == VBK_ACTION_BAN) ? g_banReasons[iItemIndex] : g_kickReasons[iItemIndex] )
	if( g_Player_Data[iPlayer][Pvoteaction] == VBK_ACTION_BAN )
		g_Player_Data[iPlayer][Pvotetime] = g_banTimes[iItemIndex]

	ShowVoteMenu( iPlayer, g_Player_Data[iPlayer][Pvoteaction] )

	return PLUGIN_HANDLED;
}

public ShowVoteMenu(id, iDo)
{
	if( !PreCheckVote(id, iDo) )
		return PLUGIN_HANDLED;

    	new szItemYes[7], szItemNo[7], szTitle[192], userid = g_Player_Data[id][Pvoteuserid]
	g_Vote_Data[Vuserid] = id
	g_Vote_Data[Vnomid] = userid

	formatex( szTitle, charsmax(szTitle), "%L", id, (iDo == VBK_ACTION_BAN) ? "VBK_MENU_TITLE_BAN" : "VBK_MENU_TITLE_KICK", g_Player_Data[id][Pname], g_Player_Data[id][Pvotereason], g_Player_Data[userid][Pname], get_user_frags(userid), get_user_deaths(userid) )

	new g_VoteMenu = menu_create( szTitle, "VoteMenuFunc" )
	formatex( szItemYes, charsmax(szItemYes), "%L", id, "VBK_YES" )
	formatex( szItemNo, charsmax(szItemNo), "%L", id, "VBK_NO" )

	menu_additem(g_VoteMenu, szItemYes, VBK_NUMBER_YES, 0)
	menu_additem(g_VoteMenu, szItemNo, VBK_NUMBER_NO, 0)
	menu_setprop(g_VoteMenu, MPROP_PERPAGE, 0)

	new players[32], playersNum
	get_players(players, playersNum, "ch")
	g_voteFinish = false;

    	for( new i = 0; i < playersNum; i++ )
    	{
		if( !get_pcvar_num(pcvar_shownom) && userid == players[i] )
			continue;

		g_Vote_Data[Vall]++
		menu_display(players[i], g_VoteMenu, 0)
	}

	new tmp[1]
	tmp[0] = id
	set_task(get_pcvar_float(pcvar_votetime), "VoteMenuEnd", 0, tmp, 1)

	return PLUGIN_HANDLED;
}

public VoteMenuFunc( iPlayer, sMenu, iItem )
{
    	if( iItem == MENU_EXIT || !g_Vote_Data[Vall] )
        	return PLUGIN_HANDLED;

	if( !g_voteFinish )
	{
	    	new iAccess, szNum[ 3 ], msg[128], hCallback
	    	menu_item_getinfo( sMenu, iItem, iAccess, szNum, charsmax( szNum ), _, _, hCallback )
	
		if( equal(szNum, VBK_NUMBER_YES) )
			g_Vote_Data[Vyes] += (g_Player_Data[g_Vote_Data[Vuserid]][Pvoteaction] == VBK_ACTION_BAN) ? g_Player_Data[iPlayer][Pbanweight] : g_Player_Data[iPlayer][Pkickweight]
	
		formatex( msg, 127, "[VBK] ^1%L", LANG_PLAYER, (equal(szNum, VBK_NUMBER_YES)) ? "VBK_VOTED_YES" : "VBK_VOTED_NO", g_Player_Data[iPlayer][Pname] )
		PrintColorInfo( iPlayer, msg )
	}

	return PLUGIN_HANDLED;
}

public VoteMenuEnd(param[])
{
	g_voteFinish = true;

	new id = g_Player_Data[param[0]][Pvoteuserid]
	new yes = floatround(g_Vote_Data[Vyes], floatround_ceil)
	new needed = floatround(float(g_Vote_Data[Vall]) * get_pcvar_float(pcvar_voteratio), floatround_ceil)
	
	if( !needed )
		needed++

	new msg[128], vote_type[5], iResult = 0
	
	if( yes >= needed)
	{
		iResult = 1

		if( g_Player_Data[param[0]][Pvoteaction] == VBK_ACTION_BAN )
		{
			copy(vote_type, sizeof(vote_type) - 1, "ban")

			new ban_cmd[128], str_prefix[32], str_reason[128], g_ban_length[15]
			get_pcvar_string(pcvar_ban_prefix, str_prefix, 31)
			formatex( str_reason, 127, "%s%s", str_prefix, g_Player_Data[param[0]][Pvotereason] )
			num_to_str( g_Player_Data[param[0]][Pvotetime], g_ban_length, charsmax(g_ban_length) )

			if( get_pcvar_num(pcvar_ban_type) == 2 )
			{
				new tempstr[36]
				get_pcvar_string(pcvar_ban_cmd, ban_cmd, 127)
	
				formatex(tempstr, 33, "#%d", get_user_userid(id))
				replace_all(ban_cmd, 127, "%userid%", tempstr)
	
				formatex(tempstr, 35, "^"%s^"", g_Player_Data[id][Psteam])
				replace_all(ban_cmd, 127, "%steamid%", tempstr)
				
				formatex(tempstr, 35, "^"%s^"", g_Player_Data[id][Pname])
				replace_all(ban_cmd, 127, "%name%", tempstr)
				
				replace_all(ban_cmd, 127, "%ip%", g_Player_Data[id][Pip])
	
				replace_all(ban_cmd, 127, "%reason%", str_reason)	

				replace_all(ban_cmd, 127, "%time%", g_ban_length)
			}
			else
			{
				new bool:banbysteam = true, auth[32]
				get_user_authid(id, auth, 31)

				if( !IsValidAuthid(auth) )
				{
					banbysteam = false
					get_user_ip(id, auth, 31, 1)
				}		
			
				if (banbysteam)
				{	
					formatex(ban_cmd, 127, "amx_ban %s %s %s", (get_pcvar_num(pcvar_ban_type) == 1) ? g_ban_length : auth, (get_pcvar_num(pcvar_ban_type) == 1) ? auth : g_ban_length, str_reason)	
				}
				else
				{
					formatex(ban_cmd, 127, "amx_banip %s %s %s", (get_pcvar_num(pcvar_ban_type) == 1) ? g_ban_length : auth, (get_pcvar_num(pcvar_ban_type) == 1) ? auth : g_ban_length, str_reason)	
				}
			}
			
			server_cmd(ban_cmd)

			formatex( msg, 127, "[VBK] ^1%L", LANG_PLAYER, "VBK_SUCCESS_BAN", g_Player_Data[id][Pname] )
		}
		else
		{
			copy(vote_type, sizeof(vote_type) - 1, "kick")

			new userid = get_user_userid(id)							
		
			server_cmd("kick #%d ^"%s^"", userid, g_Player_Data[param[0]][Pvotereason])

			formatex( msg, 127, "[VBK] ^1%L", LANG_PLAYER, "VBK_SUCCESS_KICK", g_Player_Data[id][Pname] )
		}
	}
	else
	{	
		if( g_Player_Data[param[0]][Pvoteaction] == VBK_ACTION_BAN )
		{	
			copy(vote_type, sizeof(vote_type) - 1, "ban")
			formatex( msg, 127, "[VBK] ^1%L", LANG_PLAYER, "VBK_FAILED_BAN", g_Player_Data[id][Pname], yes, needed )
		}
		else
		{
			copy(vote_type, sizeof(vote_type) - 1, "kick")
			formatex( msg, 127, "[VBK] ^1%L", LANG_PLAYER, "VBK_FAILED_KICK", g_Player_Data[id][Pname], yes, needed )
		}	
	}

	PrintColorInfo( id, msg )	

	if( get_pcvar_num(pcvar_savelogs) )
	{
		new szTime[15], vote_id[32], vote_name[64], vote_ip[16], nom_id[32], nom_name[64], nom_ip[16], g_serverip[32], timestamp = get_systime(0)
	
		get_user_authid(g_Vote_Data[Vuserid], vote_id, sizeof(vote_id) - 1)  
		get_user_name(g_Vote_Data[Vuserid], vote_name, sizeof(vote_name) - 1)  
		get_user_ip(g_Vote_Data[Vuserid], vote_ip, sizeof(vote_ip) - 1, 1)
		get_user_authid(g_Vote_Data[Vnomid], nom_id, sizeof(nom_id) - 1)  
		get_user_name(g_Vote_Data[Vnomid], nom_name, sizeof(nom_name) - 1)  
		get_user_ip(g_Vote_Data[Vnomid], nom_ip, sizeof(nom_ip) - 1, 1)
		get_user_ip(0, g_serverip, 31)
		replace_all(vote_name, sizeof(vote_name) - 1, "'", "\'")
		replace_all(nom_name, sizeof(nom_name) - 1, "'", "\'")
		num_to_str(g_Player_Data[param[0]][Pvotetime], szTime, charsmax(szTime))
	
		new query[1001]
		format(query, 1000, "INSERT INTO `acp_vbk_logs` (`timestamp`, `vote_type`, `vote_result`, `vote_all`, `vote_yes`, `vote_need`, \
				`vote_player_ip`, `vote_player_id`, `vote_player_nick`, \
				`nom_player_ip`, `nom_player_id`, `nom_player_nick`, \
				`vote_reason`, `ban_length`, `server_ip`) VALUES ('%d', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
				timestamp, vote_type, iResult, g_Vote_Data[Vall], yes, needed, vote_ip, vote_id, vote_name, nom_ip, nom_id, nom_name,
				g_Player_Data[param[0]][Pvotereason], g_Player_Data[param[0]][Pvoteaction] == VBK_ACTION_BAN ? szTime : "", g_serverip);
	
		SQL_ThreadQuery(info, "QueryHandle", query)
	}

	g_Vote_Data[Vlastvote] = get_systime(0)
	g_Vote_Data[Vuserid] = 0
	g_Vote_Data[Vnomid] = 0
	g_Vote_Data[Vall] = 0
	g_Vote_Data[Vyes] = _:0.0
}

stock PreCheckVote(id, action)
{
	if( (!g_Player_Data[id][Pkickaccess] && action == VBK_ACTION_KICK) || (!g_Player_Data[id][Pbanaccess] && action == VBK_ACTION_BAN) )
	{
		client_print_color(id, print_team_red, "^3[VBK] ^1%L", id, "VBK_NOT_ACCESS")
		return 0
	}
	else if( g_iAdmins )
	{
		client_print_color(id, print_team_red, "^3[VBK] ^1%L", id, "VBK_NOT_NOW")
		return 0
	}
	else if( g_Vote_Data[Vall] )
	{
		client_print_color(id, print_team_red, "^3[VBK] ^1%L", id, "VBK_ALREADY_RUN")
		return 0
	}
	else if( get_playersnum() < get_pcvar_num(pcvar_minplayers) )
	{
		client_print_color(id, print_team_red, "^3[VBK] ^1%L", id, "VBK_MINPLAYERS_ERROR", get_pcvar_num(pcvar_minplayers))
		return 0
	}
	else
	{	
		new voteElapsed = get_systime(0) - g_Vote_Data[Vlastvote]
		new voteElapsedPlayer = get_systime(0) - g_Player_Data[id][Plastvote]
		new voteTimeout = get_pcvar_num(pcvar_votetimeout)
		new voteDelay = get_pcvar_num(pcvar_votedelay)

		if( voteTimeout > voteElapsed )
		{
			new seconds = voteTimeout - voteElapsed
			client_print_color(id, print_team_red, "^3[VBK] ^1%L", id, "VBK_TIMEOUT", seconds)
			return 0
		}
		else if( voteDelay > voteElapsedPlayer )
		{
			new seconds = voteDelay - voteElapsed
			client_print_color(id, print_team_red, "^3[VBK] ^1%L", id, "VBK_DELAY", seconds)
			return 0
		}
	}

	return 1
}

public QueryHandle(FailState, Handle:hQuery, Error[], Errcode, Data[], DataSize) 
{ 
	if(FailState != TQUERY_SUCCESS)                       
	{
		log_amx("[VBK] SQL Error #%d - %s", Errcode, Error)        
	}
	return PLUGIN_CONTINUE
}

public PrintColorInfo( id, message[] )
{
	for( new i = 1; i <= g_MaxClients; i++ ) 
	{ 
		if( !is_user_connected(i) || (!get_pcvar_num(pcvar_shownom) && g_Vote_Data[Vnomid] == i) ) continue;

		client_print_color(i, print_team_red, message)
	} 
}

public MenuCallbackEnabled( id, menu, item )
{
	return ITEM_ENABLED
}

public MenuCallbackDisabled( id, menu, item )
{
	return ITEM_DISABLED
}