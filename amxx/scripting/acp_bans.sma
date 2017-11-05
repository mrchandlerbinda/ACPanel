
	#include < amxmodx >
	#include < amxmisc >
	#include < acp >
	#include < fakemeta >
	#include < regex >
	#include < sqlx >
	#include < time >

	#include "gameBans/acp_vars.inl"
	#include "gameBans/acp_init.inl"
	#include "gameBans/acp_subnet.inl"
	#include "gameBans/acp_player.inl"
	#include "gameBans/acp_menu.inl"
	#include "gameBans/acp_cmd.inl"
	#include "gameBans/acp_search.inl"

	public plugin_init( )
	{
		register_plugin( "ACP Bans", "0.8", "Evo" )

		register_dictionary( "common.txt" )
		register_dictionary( "acp_bans.txt" )
		register_dictionary( "time.txt" )

		register_concmd( "amx_reloadreasons", "reasonReload", ADMIN_IMMUNITY, "- reload reasons" )

		register_clcmd( "amx_banmenu", "cmdBanMenu", ADMIN_BAN, "- displays banmenu" )
		register_clcmd( "amx_custombanreason", "setCustomBanReason", ADMIN_BAN, "- configures custom ban message" )
		register_clcmd( "amx_banhistorymenu", "cmdBanhistoryMenu", ADMIN_BAN, "- displays banhistorymenu" )
		register_clcmd( "amx_screenmenu", "cmdScreenMenu", ADMIN_BAN, "- displays screenshotmenu" )

		register_menucmd( register_menuid( "Ban Menu" ), 1023, "actionBanMenu" )
		register_menucmd( register_menuid( "Ban Reason Menu" ), 1023, "actionBanMenuReason" )
		register_menucmd( register_menuid( "Banhistory Menu" ), 1023, "actionBanhistoryMenu" )
		register_menucmd( register_menuid( "Screenshot Menu" ), 1023, "actionScreenshotMenu" )

		g_coloredMenus = colored_menus( )
		g_MyMsgSync = CreateHudSyncObj( )

		pcvar_steam_validated = register_cvar( "acp_steam_validated", "0" )
		pcvar_server_nick = register_cvar( "acp_servernick", "", FCVAR_PROTECTED )
		pcvar_complainurl = register_cvar( "acp_complain_url", "localhost", FCVAR_PROTECTED )
		pcvar_banhistmotd_url = register_cvar( "acp_banhistmotd_url", "http://localhost/findex.php?player=%s", FCVAR_PROTECTED )
		pcvar_show_name_evenif_mole = register_cvar( "acp_show_name_evenif_mole", "1" )
		pcvar_firstBanmenuValue = register_cvar( "acp_first_banmenu_value", "5" )
		pcvar_consoleBanMax = register_cvar( "acp_consolebanmax", "1440" )
		pcvar_higher_ban_time_admin = register_cvar( "acp_higher_ban_time_admin", "n" )
		pcvar_admin_mole_access = register_cvar( "acp_admin_mole_access", "r" )
		pcvar_show_in_hlsw = register_cvar( "acp_show_in_hlsw", "1" )
		pcvar_show_hud_messages = register_cvar( "acp_show_hud_messages", "1" )
		pcvar_add_mapname_in_srvname = register_cvar( "acp_add_mapname_in_servername", "1" )
		pcvar_steam_immune = register_cvar( "acp_steam_immune", "1" )
		pcvar_check_url = register_cvar( "acp_check_url", "", FCVAR_PROTECTED )
		pcvar_check_wait = register_cvar( "acp_check_wait", "8.0" )
		pcvar_delay_screen = register_cvar( "acp_delay_screen", "1.0" )
		pcvar_count_screen = register_cvar( "acp_count_screen", "1" )
		pcvar_message_screen = register_cvar( "acp_message_screen", "0" )
		pcvar_ban_modt = register_cvar( "acp_ban_modt", "1", FCVAR_PROTECTED )

		pcvar_sql_host = register_cvar( "acp_sql_host", "127.0.0.1", FCVAR_PROTECTED )
		pcvar_sql_user = register_cvar( "acp_sql_user", "acp", FCVAR_PROTECTED )
		pcvar_sql_pass = register_cvar( "acp_sql_pass", "acp", FCVAR_PROTECTED )
		pcvar_sql_db = register_cvar( "acp_sql_db", "acp", FCVAR_PROTECTED )

		register_concmd( "amx_ban", "cmdBan", ADMIN_BAN, "< time in mins > < steamID, nickname, #authid > < reason >" )
		register_srvcmd( "amx_ban", "cmdBan", -1, "< time in min > < steamID, nickname, #authid > < reason >" )
		register_concmd( "amx_banip", "cmdBan", ADMIN_BAN, "< time in mins > < steamID, nickname, #authid > < reason >" )
		register_srvcmd( "amx_banip", "cmdBan", -1, "< time in mins > < steamID, nickname, #authid > < reason >" )
		register_concmd( "amx_screen", "cmdScreen", ADMIN_BAN, "< count 1 - 5 > < steamID, nickname, #authid >" )
		register_srvcmd( "amx_screen", "cmdScreen", -1, "< count 1 - 5 > < steamID, nickname, #authid >" )
		register_concmd( "amx_find", "amx_find", ADMIN_BAN, "< steamID or IP >" )
		register_srvcmd( "amx_find", "amx_find", -1, "< steamID or IP >" )
		register_concmd( "amx_findex", "amx_findex", ADMIN_BAN, "< steamID or IP >" )
		register_srvcmd( "amx_findex", "amx_findex", -1, "< steamID or IP >" )
		register_srvcmd( "amx_list", "cmdLst", -1, "Displays playerinfo" )
		register_srvcmd( "amx_sethighbantimes", "setHighBantimes" )
		register_srvcmd( "amx_setlowbantimes", "setLowBantimes" )

		register_forward( FM_Voice_SetClientListening, "fwd_voice_setclientlistening" )

		new configsDir[ 64 ], configfile[ 128 ]
		get_configsdir( configsDir, 63 )

		server_cmd( "exec %s/acp/sql.cfg", configsDir )	
		server_cmd( "exec %s/acp/general.cfg", configsDir )

		formatex( configfile, 127, "%s/acp/bans.cfg", configsDir )

		if( file_exists( configfile ) )
		{
			server_cmd( "exec %s", configfile )
		}
		else
		{
			loadDefaultBantimes( 0 )

			log_amx( "Could not find general.cfg, loading default bantimes" )
		}

		new regerror[ 2 ]

		if( get_pcvar_num( pcvar_steam_validated ) )
		{
			g_SteamID_pattern = regex_compile( REGEX_STEAMID_PATTERN_LEGAL, g_regex_return, regerror, sizeof( regerror ) - 1 )
		}
		else
		{
			g_SteamID_pattern = regex_compile( REGEX_STEAMID_PATTERN_ALL, g_regex_return, regerror, sizeof( regerror ) - 1 )
		}
	}

	public SetMotd( )
	{
		new url[ 128 ]
		get_pcvar_string( pcvar_check_url, url, 127 )

		server_cmd( "motdfile motd.txt" )
		server_cmd( "motd_write <html><meta http-equiv=^"Refresh^" content=^"0; URL=%s^"><head><title>CStrike MOTD</title></head><body bgcolor=^"black^" scroll=^"yes^"></body></html>", url )

		return PLUGIN_HANDLED;
	}

	public plugin_cfg( )
	{
		set_task( 0.5, "SetMotd" )
	}

	public acp_sql_initialized( Handle:sqlTuple )
	{
		if( g_SqlX != Empty_Handle )
		{
			log_amx( "DB Info Tuple from acp_core initialized twice!" )

			return PLUGIN_HANDLED;
		}

		g_SqlX = sqlTuple

		log_amx( "Received DB Info Tuple from acp_core: %d", sqlTuple )

		if( g_SqlX == Empty_Handle )
		{
			log_amx( "DB Info Tuple from acp_bans is empty! Trying to get a valid one" )

			new host[ 32 ], user[ 32 ], pass[ 32 ], db[ 32 ]
			get_pcvar_string( pcvar_sql_host, host, 31 )
			get_pcvar_string( pcvar_sql_user, user, 31 )
			get_pcvar_string( pcvar_sql_pass, pass, 31 )
			get_pcvar_string( pcvar_sql_db, db, 31 )

			g_SqlX = SQL_MakeDbTuple( host, user, pass, db )
		}

		set_task( 0.1, "banmod_online" )
		set_task( 0.2, "fetchReasons" )

		return PLUGIN_CONTINUE;
	}

	public client_connect( id )
	{
		if( ( id > 0 || id < 32 ) && is_user_connected( id ) )
		{
			isallowed[ id ] = false;
			g_lastCustom[ id ][ 0 ] = '^0'
			g_inCustomReason[ id ] = 0
			g_player_flagged[ id ] = false;
			g_being_banned[ id ] = false;
		}
	}

	public client_disconnected( id )
	{
		g_lastCustom[ id ][ 0 ] = '^0'
		g_inCustomReason[ id ] = 0
		g_player_flagged[ id ] = false;
		g_being_banned[ id ] = false;
		isallowed[ id ] = false;
	}

	public client_authorized( id )
	{
		if( g_SqlX == Empty_Handle )
		{
			set_task( 2.0, "client_authorized", id )

			return PLUGIN_HANDLED;
		}

		set_task( get_pcvar_float( pcvar_check_wait ), "check_player", id )
		set_task( get_pcvar_float( pcvar_check_wait ), "mic_allowed", id )

		return PLUGIN_CONTINUE;
	}

	public mic_allowed( id )
	{
		isallowed[ id ] = true;
	}

	public fwd_voice_setclientlistening( receiver, id )
	{
		if( !is_user_connected( receiver ) || !is_user_connected( id ) || receiver == id )
		{
			return FMRES_IGNORED;
		}

		if( !isallowed[ id ] )
		{
			engfunc( EngFunc_SetClientListening, receiver, id, 0 )

			return FMRES_SUPERCEDE;
		}

		return FMRES_IGNORED;
	}

	public client_putinserver( id )
	{
		if( g_SqlX == Empty_Handle )
		{
			set_task( 5.0, "client_putinserver", id )

			return PLUGIN_HANDLED;
		}

		return PLUGIN_CONTINUE;
	}

	public delayed_kick( id_str[ ] )
	{
		new player_id = str_to_num( id_str )
		new userid = get_user_userid( player_id )
		new kick_message[ 128 ]
		format( kick_message, 127, "%L", LANG_PLAYER, "KICK_MESSAGE" )

		log_amx( "Delayed Kick ID: <%s>", id_str )

		server_cmd( "kick #%d  %s", userid, kick_message )

		return PLUGIN_CONTINUE;
	}

	public cmdLst( id, level, cid )
	{
		new players[ 32 ], inum, authid[ 32 ], name[ 32 ], ip[ 50 ], flags, sflags[ 32 ]
		get_players( players, inum )
		console_print( id, "playerinfo" )

		for( new a = 0; a < inum; a++ )
		{
			get_user_ip( players[ a ], ip, 49, 1 )
			get_user_authid( players[ a ], authid, 31 )
			get_user_name( players[ a ], name, 31 )
			flags = get_user_flags( players[ a ] )
			get_flags( flags, sflags, 31 )

			console_print( id, "#WM#%s#WMW#%s#WMW#%s#WMW#%s#WMW#%i#WMW#", name, authid, ip, sflags, acp_player_auth( players[ a ] ) )
		}

		return PLUGIN_HANDLED;
	}

	public get_higher_ban_time_admin_flag( )
	{
		new flags[ 24 ]
		get_pcvar_string( pcvar_higher_ban_time_admin, flags, 23 )

		return( read_flags( flags ) )
	}

	public get_admin_mole_access_flag( )
	{
		new flags[ 24 ]
		get_pcvar_string( pcvar_admin_mole_access, flags, 23 )

		return( read_flags( flags ) )
	}

	public locate_player( id, identifier[ ] )
	{
		g_ban_type = "S"

		// Check based on steam ID
		new player = find_player( "c", identifier )

		// Check based on a partial non - case sensitive name
		if( !player )
		{
			player = find_player( "bl", identifier )

			if( player )
			{
				g_ban_type = "N"
			}
			else
			{
				// Check based on IP address
				player = find_player( "d", identifier )

				if( player )
				{
					g_ban_type = "SI"
				}
				else
				{
					// Check based on user ID
					if( identifier[ 0 ]=='#' && identifier[ 1 ] )
					{
						player = find_player( "k", str_to_num( identifier[ 1 ] ) )
					}
				}
			}
		}

		if( player )
		{
			/* Check for immunity */
			if( get_user_flags( player ) & ADMIN_IMMUNITY )
			{
				new name[ 32 ]
				get_user_name( player, name, 31 )

				if( id == 0 )
				{
					server_print( "* Client ^"%s^" has immunity", name )
				}
				else
				{
					console_print( id, "* ^"%s^" has immunity", name )
				}

				return -1;
			}

			/* Check for a bot */
			else if( is_user_bot( player ) )
			{
				new name[ 32 ]
				get_user_name( player, name, 31 )

				if( id == 0 )
				{
					server_print( "* Client ^"%s^" is a bot", name )
				}
				else
				{
					console_print( id, "* Client ^"%s^" is a bot", name )
				}

				return -1;
			}
		}

		return player;
	}

	public setHighBantimes( )
	{
		new arg[ 32 ]
		new argc = read_argc( ) - 1
		g_highbantimesnum = argc

		if( argc < 1 || argc > 12 )
		{
			log_amx( "You have more than 12 or less than 1 bantimes set in amx_sethighbantimes" )
			log_amx( "Loading default bantimes" )

			loadDefaultBantimes( 1 )

			return PLUGIN_HANDLED;
		}

		new i = 0
		new num[ 32 ], flag[ 32 ]

		while( i < argc )
		{
			read_argv( i + 1, arg, 31 )
			parse( arg, num, 31, flag, 31 )

			if( equali( flag, "m" ) )
			{
				g_HighBanMenuValues[ i ] = str_to_num( num )
			}
			else if( equali( flag, "h" ) )
			{
				g_HighBanMenuValues[ i ] = ( str_to_num( num ) * 60 )
			}
			else if( equali( flag, "d" ) )
			{
				g_HighBanMenuValues[ i ] = ( str_to_num( num ) * 1440 )
			}
			else if( equali( flag, "w" ) )
			{
				g_HighBanMenuValues[ i ] = ( str_to_num( num ) * 10080 )
			}

			i++;
		}

		return PLUGIN_HANDLED;
	}

	public setLowBantimes( )
	{
		new arg[ 32 ]
		new argc = read_argc( ) - 1
		g_lowbantimesnum = argc

		if( argc < 1 || argc > 12 )
		{
			log_amx( "You have more than 12 or less than 1 bantimes set in amx_setlowbantimes" )
			log_amx( "Loading default bantimes" )

			loadDefaultBantimes( 2 )

			return PLUGIN_HANDLED;
		}

		new i = 0
		new num[ 32 ], flag[ 32 ]

		while( i < argc )
		{
			read_argv( i + 1, arg, 31 )
			parse( arg, num, 31, flag, 31 )

			if( equali( flag, "m" ) )
			{
				g_LowBanMenuValues[ i ] = str_to_num( num )
			}
			else if( equali( flag, "h" ) )
			{
				g_LowBanMenuValues[ i ] = ( str_to_num( num ) * 60 )
			}
			else if( equali( flag, "d" ) )
			{
				g_LowBanMenuValues[ i ] = ( str_to_num( num ) * 1440 )
			}
			else if( equali( flag, "w" ) )
			{
				g_LowBanMenuValues[ i ] = ( str_to_num( num ) * 10080 )
			}

			i++;
		}

		return PLUGIN_HANDLED;
	}

	loadDefaultBantimes( num )
	{
		if( num == 1 || num == 0 )
		{
			server_cmd( "amx_sethighbantimes 5 60 240 600 6000 0 -1" )
		}

		if( num == 2 || num == 0 )
		{
			server_cmd( "amx_setlowbantimes 5 30 60 480 600 1440 -1" )
		}
	}

	MySqlX_ThreadError( szQuery[ ], error[ ], errnum, failstate, id )
	{
		if( failstate == TQUERY_CONNECT_FAILED )
		{
			log_amx( "%L", LANG_SERVER, "TCONNECTION_FAILED" )
		}
		else if( failstate == TQUERY_QUERY_FAILED )
		{
			log_amx( "%L", LANG_SERVER, "TQUERY_FAILED" )
		}

		log_amx( "%L", LANG_SERVER, "TQUERY_ERROR", id )
		log_amx( "%L", LANG_SERVER, "TQUERY_MSG", error, errnum )
		log_amx( "%L", LANG_SERVER, "TQUERY_STATEMENT", szQuery )
	}
