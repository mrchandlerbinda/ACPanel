
	// 16k * 4 = 64k stack size
	#pragma dynamic								16384

	#define REGEX_STEAMID_PATTERN_LEGAL			"^^STEAM_0:(0|1):\d{4,8}$"
	#define REGEX_STEAMID_PATTERN_ALL			"^^(STEAM|VALVE)_(0|1|2|3|4|5|6|7|8|9):(0|1|2|3|4|5|6|7|8|9):\d+$"
	#define IsValidAuthid(%1)					( regex_match_c( %1, g_SteamID_pattern, g_regex_return ) > 0 )

	// Regex
	new Regex:g_SteamID_pattern
	new g_regex_return

	// For hudmessages
	new g_MyMsgSync

	// Variables for menus
	new g_LowBanMenuValues[ 12 ]
	new g_HighBanMenuValues[ 12 ]
	new g_coloredMenus
	new g_banReasons[ 8 ][ 128 ]
	new g_menuPlayers[ 33 ][ 32 ]
	new g_menuPlayersNum[ 33 ]
	new g_menuPosition[ 33 ]
	new g_menuOption[ 33 ]
	new g_menuSettings[ 33 ]
	new g_bannedPlayer
	new g_lastCustom[ 33 ][ 128 ]
	new g_inCustomReason[ 33 ]
	new bool:g_player_flagged[ 33 ]

	// Some Vars
	new pcvar_steam_validated
	new pcvar_server_nick
	new pcvar_complainurl
	new pcvar_banhistmotd_url
	new pcvar_show_name_evenif_mole
	new pcvar_firstBanmenuValue
	new pcvar_consoleBanMax
	new pcvar_higher_ban_time_admin
	new pcvar_admin_mole_access
	new pcvar_show_in_hlsw
	new pcvar_show_hud_messages
	new pcvar_add_mapname_in_srvname
	new pcvar_steam_immune
	new pcvar_check_wait
	new pcvar_delay_screen
	new pcvar_count_screen
	new pcvar_message_screen
	new pcvar_ban_modt
	new pcvar_sql_host
	new pcvar_sql_user
	new pcvar_sql_pass
	new pcvar_sql_db

	new Float:kick_delay = 5.0

	new bool:isallowed[ 33 ]
	new bool:g_being_banned[ 33 ]
	new bool:SubnetBanDelete = false;
	new bool:SubnetFound[ 33 ] = false;
	new Handle:g_SqlX

	new g_search_player_steamid[ 50 ]
	new g_steamidorusername[ 50 ]
	new g_ban_reason[ 256 ]
	new g_ban_type[ 4 ]

	new g_highbantimesnum
	new g_lowbantimesnum
	new ban_motd[ 4096 ]
	new Subipaddr[ 16 ]
	new Subipmask[ 16 ]
	new Bitmask[ 16 ]

	new g_aNum = 0
	new g_ip[ 32 ]
	new g_port[ 10 ]
