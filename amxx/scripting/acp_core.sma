
	#include < amxmodx >
	#include < amxmisc >
	#include < fakemeta >
	#include < regex >
	#include < sqlx >

	#define SQL_QUERY_DELAYED

	#define column(%1)						SQL_FieldNameToNum(query, %1)
	#define EncodeText(%1)					engfunc( EngFunc_AllocString, %1 )
	#define DecodeText(%1,%2,%3)			global_get( glb_pStringBase, %1, %2, %3 )

	#define REGEX_IP_PATTERN				"\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b"
	#define REGEX_STEAMID_PATTERN_LEGAL 	"^^STEAM_0:(0|1):\d{4,8}$"
	#define REGEX_STEAMID_PATTERN_ALL		"^^(STEAM|VALVE)_(0|1|2|3|4|5|6|7|8|9):(0|1|2|3|4|5|6|7|8|9):\d+$"

	new Regex:g_IP_pattern
	new Regex:g_rgxRes, g_regex_return
	new Regex:g_SteamID_pattern

	#define IsValidIP(%1)					( regex_match_c( %1, g_IP_pattern, g_regex_return ) > 0 )
	#define IsValidAuthid(%1)				( regex_match_c( %1, g_SteamID_pattern, g_regex_return ) > 0 )

	new const blocked_action_list[ 2 ][ ] = 
	{
		"whitelist", 
		"renamelist"
	}

	new const g_name[ ] = "name"

	enum _:AuthType
	{
		AUTH_NAME = 1, 
		AUTH_IP, 
		AUTH_STEAM
	}

	enum _:PlayersList
	{
		UID, 
		Upoints, 
		Uapproved[ 4 ], 
		Uaccess[ 128 ], 
		Uname[ 44 ], 
		Usteam[ 35 ], 
		Uip[ 33 ], 
		Upass[ 33 ]
	}

	enum PlayerData
	{
		PL_id, 
		PL_auth, 
		PL_points, 
		PL_name[ 44 ], 
		PL_steam[ 35 ], 
		PL_ip[ 33 ], 
		PL_key
	}

	new g_name_change[ ] = "#Cstrike_Name_Change"

	new g_Player_Data[ 33 ][ PlayerData ]
	new g_serverID, g_accounts_load = true;

	new g_cmdNickReserv[ 16 ]
	new g_cmdNickInactive[ 16 ]
	new g_serverip[ 22 ]

	new g_Cache[ 555 ]
	new error[ 128 ]

	new Sql_Initialized
	new End_Plugin_Forward
	new g_sqlLoadPatterns
	new g_msgid_saytext

	new bool:g_bSqlInitialized
	new Handle:info
	new Handle:sql
	new Array:g_apData
	new Trie:g_tpType[ AuthType ]
	new Trie:g_tbAction
	new Trie:allNames

	// Some Vars
	new amx_mode, amx_password_field, amx_default_access
	new pcvar_steam_validated, pcvar_sensivity, pcvar_reserv
	new pcvar_announce, pcvar_savelogs, pcvar_changenick, pcvar_user_immunity, pcvar_default_nick
	new pcvar_serverip, pcvar_sql_host, pcvar_sql_user, pcvar_sql_pass, pcvar_sql_db

	public plugin_natives( )
	{
		register_library( "acp" ) // register module

		register_native( "acp_player_auth", "native_player_auth", 1 )
		register_native( "acp_player_dbid", "native_player_dbid", 1 )
		register_native( "acp_player_points", "native_player_points", 1 )
		register_native( "acp_server_id", "native_server_id", 1 )
		register_native( "acp_take_points", "native_take_points", 1 )
		register_native( "acp_give_points", "native_give_points", 1 )
	}

	public plugin_init( )
	{
		register_plugin( "ACP Core", "0.4", "Evo" )

		register_dictionary( "common.txt" )
		register_dictionary( "acp_core.txt" )

		register_cvar( "amx_vote_ratio", "0.02" )
		register_cvar( "amx_vote_time", "10" )
		register_cvar( "amx_vote_answers", "1" )
		register_cvar( "amx_vote_delay", "60" )
		register_cvar( "amx_last_voting", "0" )
		register_cvar( "amx_show_activity", "2", FCVAR_PROTECTED )
		register_cvar( "amx_votekick_ratio", "0.40" )
		register_cvar( "amx_voteban_ratio", "0.40" )
		register_cvar( "amx_votemap_ratio", "0.40" )

		set_cvar_float( "amx_last_voting", 0.0 )

		amx_mode = register_cvar( "amx_mode", "1", FCVAR_PROTECTED )
		amx_password_field = register_cvar( "amx_password_field", "_pw", FCVAR_PROTECTED )
		amx_default_access = register_cvar( "amx_default_access", "", FCVAR_PROTECTED )

		pcvar_steam_validated = register_cvar( "amx_steam_validated", "0" )
		pcvar_user_immunity = register_cvar( "amx_user_immunity", "a" )
		pcvar_announce = register_cvar( "amx_announce", "1" )
		pcvar_savelogs = register_cvar( "amx_savelogs", "1" )
		pcvar_sensivity = register_cvar( "amx_case_sensivity", "0" )
		pcvar_reserv = register_cvar( "amx_nick_reserv", "0" )
		pcvar_changenick = register_cvar( "amx_changenick", "1" )
		pcvar_default_nick = register_cvar( "amx_default_nick", "CHANGE NICKNAME", FCVAR_PROTECTED )
		pcvar_serverip = register_cvar( "amx_serverip", "", FCVAR_PROTECTED )

		pcvar_sql_host = register_cvar( "acp_sql_host", "127.0.0.1", FCVAR_PROTECTED )
		pcvar_sql_user = register_cvar( "acp_sql_user", "acp", FCVAR_PROTECTED )
		pcvar_sql_pass = register_cvar( "acp_sql_pass", "acp", FCVAR_PROTECTED )
		pcvar_sql_db = register_cvar( "acp_sql_db", "acp", FCVAR_PROTECTED )

		register_concmd( "amx_reloadadmins", "cmdReload", ADMIN_IMMUNITY, "- reload account and pattern list." )

		format( g_cmdNickReserv, 15, "amxauth%c%c%c%c", random_num( 'A', 'Z' ), random_num( 'A', 'Z' ), random_num( 'A', 'Z' ), random_num( 'A', 'Z' ) )
		format( g_cmdNickInactive, 15, "amxauth%c%c%c%c", random_num( 'A', 'Z' ), random_num( 'A', 'Z' ), random_num( 'A', 'Z' ), random_num( 'A', 'Z' ) )

		register_clcmd( g_cmdNickReserv, "ackSignalReserv" )
		register_clcmd( g_cmdNickInactive, "ackSignalInactive" )

		register_forward( FM_ClientUserInfoChanged, "forward_client_userinfochanged" )

		g_msgid_saytext = get_user_msgid( "SayText" )

		remove_user_flags( 0, read_flags( "z" ) )
	}

	public plugin_cfg( )
	{
		new configsDir[ 64 ]
		get_configsdir( configsDir, 63 )

		server_cmd( "exec %s/acp/sql.cfg", configsDir )	
		server_cmd( "exec %s/acp/general.cfg", configsDir )

		for( new ib = 1; ib < AuthType; ib++ )
		{
			g_tpType[ ib ] = TrieCreate( )
			g_tbAction = TrieCreate( )
		}

		g_apData = ArrayCreate( PlayersList )
		allNames = TrieCreate( )

		new regerror[ 2 ]
		g_IP_pattern = regex_compile( REGEX_IP_PATTERN, g_regex_return, regerror, sizeof( regerror ) - 1 )

		if( get_pcvar_num( pcvar_steam_validated ) )
		{
			g_SteamID_pattern = regex_compile( REGEX_STEAMID_PATTERN_LEGAL, g_regex_return, regerror, sizeof( regerror ) - 1 )
		}
		else
		{
			g_SteamID_pattern = regex_compile( REGEX_STEAMID_PATTERN_ALL, g_regex_return, regerror, sizeof( regerror ) - 1 )
		}

		Sql_Initialized = CreateMultiForward( "acp_sql_initialized", ET_IGNORE, FP_CELL )
		End_Plugin_Forward = CreateMultiForward( "acp_endmap_func", ET_IGNORE )

		set_task( 0.1, "sql_init" )
	}

	public plugin_end( )
	{
		for( new ib = 1; ib < AuthType; ib++ )
		{
			TrieDestroy( g_tpType[ ib ] )
			TrieDestroy( g_tbAction )
		}

		ArrayDestroy( g_apData )

		new ret
		ExecuteForward( End_Plugin_Forward, ret )

		if( info != Empty_Handle ) // fix bug in connect
		{
			set_task( 0.5, "plugin_sql_handle" )
		}
	}

	public plugin_sql_handle( )
	{
		SQL_FreeHandle( info )
	}

	public sql_init( )
	{
		new host[ 64 ], user[ 64 ], pass[ 64 ], dbname[ 64 ]
		get_pcvar_string( pcvar_sql_host, host, 31 )
		get_pcvar_string( pcvar_sql_user, user, 31 )
		get_pcvar_string( pcvar_sql_pass, pass, 31 )
		get_pcvar_string( pcvar_sql_db, dbname, 31 )

		info = SQL_MakeDbTuple( host, user, pass, dbname )

		GetServerID( )
		ServerInitialized( )
	}

	public ServerInitialized( )
	{
		get_pcvar_string( pcvar_serverip, g_serverip, 21 )

		if( !strlen( g_serverip ) )
		{
			get_user_ip( 0, g_serverip, 21 )
		}

		new errno, query[ 512 ]

		if( info )
		{
			sql = SQL_Connect( info, errno, error, 127 )
		}

		if( sql == Empty_Handle )
		{
			g_sqlLoadPatterns = true;

			log_amx( "%L", LANG_SERVER, "ADMIN_SQL_CANT_CON", error )
		}
		else
		{
			formatex( query, 511, "SELECT `id` FROM `acp_servers` WHERE ( address = '%s' ) LIMIT 1", g_serverip )

			SQL_FreeHandle( sql )
			SQL_ThreadQuery( info, "ServerInitialized_Post", query )
		}
	}

	public ServerInitialized_Post( FailState, Handle:query, error[ ], errcode, Data[ ], DataSize )
	{
		if( FailState != TQUERY_SUCCESS )
		{
			g_sqlLoadPatterns = true;

			log_amx( "SQL Error #%d - %s", errcode, error )
		}
		else if( !SQL_NumResults( query ) )
		{
			new servername[ 100 ], query_insert[ 512 ]
			get_cvar_string( "hostname", servername, 99 )

			formatex( query_insert, 511, "INSERT INTO `acp_servers` ( `address`, `hostname` ) VALUES ( '%s','%s' )", g_serverip, servername )
			SQL_ThreadQuery( info, "QueryHandle", query_insert )
		}
		else
		{
			LoadPatterns( )
		}
	}

	public GetServerID( )
	{
		for( new ib = 1; ib < AuthType; ib++ )
		{
			TrieClear( g_tpType[ ib ] )
		}

		new errno
		ArrayClear( g_apData )

		if( info )
		{
			sql = SQL_Connect( info, errno, error, 127 )
		}

		if( sql == Empty_Handle )
		{
			log_amx( "%L", LANG_SERVER, "ADMIN_SQL_CANT_CON", error )
		}
		else
		{
			new temp_srvip[ 32 ]
			get_pcvar_string( pcvar_serverip, temp_srvip, 31 )

			if( !strlen( temp_srvip ) )
			{
				get_user_ip( 0, temp_srvip, 31 )
			}

			formatex( g_Cache, 554, "SELECT `id`, `opt_accounts` FROM `acp_servers` WHERE ( address = '%s' ) LIMIT 1", temp_srvip )

	#if !defined SQL_QUERY_DELAYED
			new Handle:query = SQL_PrepareQuery( sql, g_Cache )

			if( !SQL_Execute( query ) )
			{
	#else
			SQL_FreeHandle( sql )
			SQL_ThreadQuery( info, "GetServerID_Post", g_Cache )
		}
	}

	public GetServerID_Post( FailState, Handle:query, error[ ], errcode, Data[ ], DataSize )
	{
			if( FailState != TQUERY_SUCCESS )
			{
	#endif
				log_amx( "%L", LANG_SERVER, "ADMIN_SQL_CANT_LOAD_SERVERS", error )
			}
			else if( !SQL_NumResults( query ) )
			{
				log_amx( "%L", LANG_SERVER, "ADMIN_NO_SERVERINFO" )
			}
			else
			{
				g_serverID = SQL_ReadResult( query, 0 )

				if( SQL_ReadResult( query, 1 ) != 1 )
				{
					g_accounts_load = false;
				}

				log_amx( "LOAD SERVER INFO - ID: %i | ACCOUNTS: %s", g_serverID, ( g_accounts_load ) ? "YES" : "NO" )
			}

	#if !defined SQL_QUERY_DELAYED
			SQL_FreeHandle( query )
	#endif

			LoadPlayersList( )

	#if !defined SQL_QUERY_DELAYED
			SQL_FreeHandle( sql )
		}
	#endif
	}

	public LoadPlayersList( )
	{
		if( g_serverID )
		{
			if( !g_accounts_load )
			{
				log_amx( "%L", LANG_SERVER, "ADMIN_AUTH_DISABLED" )
			}
			else
			{
				if( g_bSqlInitialized )
				{
					new errno

					if( info )
					{
						sql = SQL_Connect( info, errno, error, 127 )
					}

					if( sql == Empty_Handle )
					{
						log_amx( "%L", LANG_SERVER, "ADMIN_SQL_CANT_CON", error )

						return PLUGIN_HANDLED;
					}

	#if defined SQL_QUERY_DELAYED
					SQL_FreeHandle( sql )
	#endif
				}

				new len, timestamp = get_systime( 0 )

				len = format( g_Cache, charsmax( g_Cache ), "SELECT pl.userid, pl.player_nick, pl.password, \
					pl.points, pl.player_ip, pl.steamid, pl.flag," )
				len += format( g_Cache[ len ], charsmax( g_Cache ) - len, " GROUP_CONCAT(m.access_flags SEPARATOR '') AS access, pl.approved FROM `acp_players` pl \
					LEFT JOIN `acp_access_mask_players` m_pl ON m_pl.userid = pl.userid" )
				len += format( g_Cache[ len ], charsmax( g_Cache ) - len, " LEFT JOIN `acp_access_mask` m ON m.mask_id = m_pl.mask_id \
					LEFT JOIN `acp_access_mask_servers` m_srv ON m_srv.mask_id = m_pl.mask_id" )
				len += format( g_Cache[ len ], charsmax( g_Cache ) - len, " WHERE (m_srv.server_id = %d OR m_srv.server_id = 0) AND (m_pl.access_expired > %d OR m_pl.access_expired = 0) \
					GROUP BY pl.userid", g_serverID, timestamp )

	#if !defined SQL_QUERY_DELAYED
				new Handle:query = SQL_PrepareQuery( sql, g_Cache )

				if( !SQL_Execute( query ) )
				{
	#else
				SQL_ThreadQuery( info, "LoadPlayersList_Post", g_Cache )
			}
		}
		else
		{
			log_amx( "%L", LANG_SERVER, "ADMIN_SQL_CANT_LOAD_SERVERS", error )
		}

		return PLUGIN_CONTINUE;
	}

	public LoadPlayersList_Post( FailState, Handle:query, error[ ], errcode, Data[ ], DataSize )
	{
				if( FailState != TQUERY_SUCCESS )
				{
	#endif
					log_amx( "%L", LANG_SERVER, "ADMIN_SQL_CANT_LOAD_PLAYERS", error )
				}
				else if( !SQL_NumResults( query ) )
				{
					log_amx( "%L", LANG_SERVER, "ADMIN_NO_PLAYERS" )
				}
				else
				{
					new playersCount

					new qcolUID = column( "userid" )
					new qcolUname = column( "player_nick" )
					new qcolUpass = column( "password" )
					new qcolUaccess = column( "access" )
					new qcolUflag = column( "flag" )
					new qcolUsteam = column( "steamid" )
					new qcolUip = column( "player_ip" )
					new qcolUpoints = column( "points" )
					new qcolUapproved = column( "approved" )

					new data[ PlayersList ], Uflag, iPos

					while( SQL_MoreResults( query ) )
					{
						data[ UID ] = SQL_ReadResult( query, qcolUID )
						data[ Upoints ] = SQL_ReadResult( query, qcolUpoints )
						SQL_ReadResult( query, qcolUapproved, data[ Uapproved ], sizeof( data[ Uapproved ] ) - 1 )
						SQL_ReadResult( query, qcolUname, data[ Uname ], sizeof( data[ Uname ] ) - 1 )
						SQL_ReadResult( query, qcolUpass, data[ Upass ], sizeof( data[ Upass ] ) - 1 )
						SQL_ReadResult( query, qcolUaccess, data[ Uaccess ], sizeof( data[ Uaccess ] ) - 1 )
						delete_duplicate(data[ Uaccess], sizeof( data[ Uaccess ] ) - 1 )
						SQL_ReadResult( query, qcolUsteam, data[ Usteam], sizeof( data[ Usteam ] ) - 1 )
						SQL_ReadResult( query, qcolUip, data[ Uip ], sizeof( data[ Uip ] ) - 1 )
						Uflag = SQL_ReadResult( query, qcolUflag )

						if( !get_pcvar_num( pcvar_sensivity ) )
						{
							strtolower( data[ Uname ] )
						}

						if( get_pcvar_num( pcvar_reserv ) )
						{
							if( data[ Uname ][ 0 ] )
							{
								TrieSetCell( allNames, data[ Uname ], iPos )
							}
						}

						switch( Uflag )
						{
							case AUTH_NAME:
							{
								if( !data[ Uname ][ 0 ] || !data[ Upass ][ 0 ] )
								{
									SQL_NextRow( query )

									continue;
								}

								TrieSetCell( g_tpType[ Uflag ], data[ Uname ], iPos )
							}

							case AUTH_IP:
							{
								if( !IsValidIP( data[ Uip ] ) )
								{
									SQL_NextRow( query )

									continue;
								}

								TrieSetCell( g_tpType[ Uflag ], data[ Uip ], iPos )
							}

							case AUTH_STEAM:
							{
								if( !IsValidAuthid( data[ Usteam ] ) )
								{
									SQL_NextRow( query )

									continue;
								}

								TrieSetCell( g_tpType[ Uflag ], data[ Usteam ], iPos )
							}
						}

						log_amx( "Loading data from database - iPos: %i | UID: %i | Flag: %i | Access: %s", iPos, data[ UID ], Uflag, data[ Uaccess ] )

						ArrayPushArray( g_apData, data )

						++iPos;
						++playersCount;

						SQL_NextRow( query )
					}

					if( playersCount == 1 )
					{
						log_amx( "%L", LANG_SERVER, "ADMIN_SQL_LOADED_PLAYER" )
					}
					else
					{
						log_amx( "%L", LANG_SERVER, "ADMIN_SQL_LOADED_PLAYERS", playersCount )
					}
				}

				if( !g_bSqlInitialized )
				{
					new ret
					ExecuteForward( Sql_Initialized, ret, info )

					g_bSqlInitialized = true;
				}
				else
				{
	#if defined SQL_QUERY_DELAYED
					new players[ MAX_PLAYERS ], num
					get_players( players, num )

					for( new i = 0; i < num; i++ )
					{
						new param[ 2 ], name[ 44 ]
						param[ 0 ] = players[ i ]
						get_user_name( param[ 0 ], name, 43 )
						param[ 1 ] = EncodeText( name )
						accessUser( param )
					}
				}
	#else
					SQL_FreeHandle( sql )
				}
			}
		}
		else
		{
			log_amx( "%L", LANG_SERVER, "ADMIN_SQL_CANT_LOAD_SERVERS", error )
		}

		return PLUGIN_CONTINUE;
	#endif
	}

	public LoadPatterns( )
	{
		new query[ 512 ]

		formatex( query, 511, "SELECT `pattern`,`action` FROM `acp_nick_patterns`" )
		SQL_ThreadQuery( info, "LoadPatterns_Post", query )

		return PLUGIN_HANDLED;
	}

	public LoadPatterns_Post( FailState, Handle:query,error[ ], errcode, Data[ ], DataSize )
	{
		if( FailState != TQUERY_SUCCESS )
		{
			g_sqlLoadPatterns = true;

			log_amx( "%L", LANG_SERVER, "ADMIN_SQL_CANT_LOAD_PATTERNS", error )
		}
		else if( !SQL_NumResults( query ) )
		{
			g_sqlLoadPatterns = true;

			log_amx( "%L", LANG_SERVER, "ADMIN_NAME_NOT_FOUND" )
		}
		else
		{
			new nameData[ 64 ], blockedCount, g_BlockedAction

			while( SQL_MoreResults( query ) )
			{
				SQL_ReadResult( query, 0, nameData, sizeof( nameData ) - 1 )
				g_BlockedAction = SQL_ReadResult( query, 1 )

				new Array:nicksData

				if( !TrieGetCell( g_tbAction, blocked_action_list[ g_BlockedAction ], nicksData ) )
				{
					nicksData = ArrayCreate( 64 )
				}

				ArrayPushArray( nicksData,nameData )
				TrieSetCell( g_tbAction, blocked_action_list[ g_BlockedAction ], nicksData )

				++blockedCount;

				SQL_NextRow( query )
			}

			if( blockedCount == 1 )
			{
				log_amx( "%L", LANG_SERVER, "ADMIN_SQL_LOADED_PATTERN" )
			}
			else
			{
				log_amx( "%L", LANG_SERVER, "ADMIN_SQL_LOADED_PATTERNS", blockedCount )
			}

			g_sqlLoadPatterns = true;
		}
	}

	public cmdReload( id, level, cid )
	{
		if( !cmd_access( id, level, cid, 1 ) )
		{
			return PLUGIN_HANDLED;
		}

		remove_user_flags( 0, read_flags( "z" ) )

		for( new ib = 1; ib < AuthType; ib++ )
		{
			TrieClear( g_tpType[ ib ] )
		}

		ArrayClear( g_apData )

		LoadPlayersList( )
		LoadPatterns( )

	#if !defined SQL_QUERY_DELAYED
		new players[ MAX_PLAYERS ], num
		get_players( players, num )

		for( new i = 0; i < num; i++ )
		{
			new param[ 2 ], name[ 44 ]
			param[ 0 ] = players[ i ]
			get_user_name(param[ 0 ], name, 43 )
			param[ 1 ] = EncodeText( name )
			accessUser( param )
		}
	#endif

		return PLUGIN_HANDLED;
	}

	getAccess( id, name[ ], authid[ ], ip[ ], password[ ] )
	{
		new index = -1, result = 0, iPos, data[ PlayersList ], tname[ 44 ]
		copy( tname, sizeof( tname ) - 1, name )

		if( !get_pcvar_num( pcvar_sensivity ) )
		{
			strtolower( tname )
		}

		log_amx( "Player Authorized - Name: %s | IP: %s | STEAM: %s", name, ip, authid )

		for( new i = 1; i < AuthType; i++ )
		{
			switch( i )
			{
				case AUTH_NAME:
				{
					if( TrieGetCell( g_tpType[ i ], tname, iPos ) )
					{
						ArrayGetArray( g_apData, iPos, data )

						if( equal( password, data[ Upass ] ) )
						{
							result |= 12

							if( !equal( data[ Uapproved ], "yes" ) )
							{
								result |= 16

								break;
							}

							index = i

							break;
						}
						else
						{
							result |= 3

							break;
						}
					}
				}

				case AUTH_IP:
				{
					if( TrieGetCell( g_tpType[ i ], ip, iPos ) )
					{
						ArrayGetArray( g_apData, iPos, data )

						result |= 8

						if( !equal( data[ Uapproved ], "yes" ) )
						{
							result |= 16

							break;
						}

						index = i

						break;
					}
				}

				case AUTH_STEAM:
				{
					if( TrieGetCell( g_tpType[ i ], authid, iPos ) )
					{
						ArrayGetArray( g_apData, iPos, data )

						result |= 8

						if( !equal( data[ Uapproved ], "yes" ) )
						{
							result |= 16

							break;
						}

						index = i
					}
				}
			}
		}

		if( index != -1 )
		{
			set_user_flags( id, read_flags( data[ Uaccess] ) )

			g_Player_Data[ id ][ PL_id ] = data[ UID ]
			g_Player_Data[ id ][ PL_auth ] = index
			g_Player_Data[ id ][ PL_points ] = data[ Upoints ]
			g_Player_Data[ id ][ PL_key ] = iPos
			copy( g_Player_Data[ id ][ PL_name ], 43, name )
			copy( g_Player_Data[ id ][ PL_ip ], 32, ip )
			copy( g_Player_Data[ id ][ PL_steam ], 34, authid )
		}
		else if( get_pcvar_float( amx_mode ) == 2.0 )
		{
			result |= 2
		}
		else
		{
			if( get_pcvar_num( pcvar_reserv ) && TrieKeyExists( allNames, tname ) )
			{
				result |= 2
			}
			else
			{
				new defaccess[ 32 ]
				get_pcvar_string( amx_default_access, defaccess, 31 )

				if( !strlen( defaccess ) )
				{
					copy( defaccess, 32, "z" )
				}

				new idefaccess = read_flags( defaccess )

				if( idefaccess )
				{
					result |= 8
					set_user_flags( id, idefaccess )
				}
			}
		}

		return result;
	}

	public accessUser( param[ ] )
	{
		new id = param[ 0 ], name[ 44 ]
		DecodeText( param[ 1 ], name, sizeof( name ) - 1 )

		if( !g_bSqlInitialized && g_accounts_load )
		{
			new tmp[ 2 ]
			tmp[ 0 ] = id
			tmp[ 1 ] = EncodeText( name )
			set_task( 0.1, "accessUser", id, tmp, sizeof( tmp ) )

			return PLUGIN_CONTINUE;
		}

		remove_user_flags( id )

		ClearPlayerData( id )

		new userip[ 33 ], usersteam[ 35 ], password[ 33 ], md5password[ 34 ], passfield[ 32 ], username[ 44 ]
		get_user_ip( id, userip, 32, 1 )
		get_user_authid( id, usersteam, 34 )

		if( name[ 0 ] )
		{
			copy( username, 43, name )
		}
		else
		{
			get_user_name( id, username, 43 )
		}

		get_pcvar_string( amx_password_field, passfield, 31 )
		get_user_info( id, passfield, password, 32 )
		md5( password, md5password )

		new result = getAccess( id, username, usersteam, userip, md5password )

		if( result & 1 )
		{
			engclient_print( id, engprint_console, "* %L", id, "ADMIN_INV_PAS" )
		}

		if( result & 2 )
		{
			client_cmd( id, "%s", g_cmdNickReserv )

			return PLUGIN_HANDLED;
		}

		if( result & 4 )
		{
			engclient_print( id, engprint_console, "* %L", id, "ADMIN_PAS_ACC" )
		}

		if( result & 8 )
		{
			engclient_print( id, engprint_console, "* %L", id, "ADMIN_PRIV_SET" )
		}

		if( result & 16 )
		{
			client_cmd( id, "%s", g_cmdNickInactive )

			return PLUGIN_HANDLED;
		}

		return PLUGIN_CONTINUE;
	}

	public ClearPlayerData( id )
	{
		g_Player_Data[ id ][ PL_id ] = 0
		g_Player_Data[ id ][ PL_auth ] = 0
		g_Player_Data[ id ][ PL_points ] = 0
		g_Player_Data[ id ][ PL_name ][ 0 ] = 0
		g_Player_Data[ id ][ PL_ip ][ 0 ] = 0
		g_Player_Data[ id ][ PL_steam ][ 0 ] = 0
	}

	public client_infochanged( id )
	{
		if( !is_user_connected( id ) || !get_pcvar_num( amx_mode ) )
		{
			return PLUGIN_CONTINUE;
		}

		new newname[ MAX_NAME_LENGTH ], oldname[ MAX_NAME_LENGTH ]
		get_user_name( id, oldname, 31 )
		get_user_info( id, "name", newname, 31 )

		if( !equali( newname, oldname ) )
		{
			updateDatabasePlayerAccount( id )

			new param[ 2 ]
			param[ 0 ] = id
			param[ 1 ] = EncodeText( newname )
			accessUser( param )
		}

		return PLUGIN_CONTINUE;
	}

	public client_authorized( id )
	{
		new param[ 2 ]
		param[ 0 ] = id
		param[ 1 ] = EncodeText( "" )

		return get_pcvar_num( amx_mode ) ? accessUser( param ) : PLUGIN_CONTINUE
	}

	public checkPlayerConnected( param[ ] )
	{
		new id = param[ 0 ]

		if( !is_user_connected( id ) )
		{
			return PLUGIN_HANDLED;
		}

		if( !g_sqlLoadPatterns )
		{
			new tmp[ 1 ]
			tmp[ 0 ] = id
			set_task( 0.1, "checkPlayerConnected", id, tmp, sizeof( tmp ) )
		}
		else
		{
			static iflags, szflags[ 28 ]
			get_pcvar_string( pcvar_user_immunity, szflags, sizeof szflags - 1 )

			iflags = read_flags( szflags )

			if( get_user_flags( id ) & iflags )
			{
				return PLUGIN_CONTINUE;
			}

			new username[ 44 ]
			get_user_name( id, username, 43 )

			if( check_name( id, username, 1 ) == 1 )
			{
				new default_nick[ MAX_NAME_LENGTH ]
				get_pcvar_string( pcvar_default_nick, default_nick, charsmax( default_nick ) )

				set_user_info( id, "name", default_nick )
			}
		}

		return PLUGIN_CONTINUE;
	}

	public client_putinserver( id )
	{
		new param[ 1 ]
		param[ 0 ] = id

		return checkPlayerConnected( param )
	}

	public client_disconnected( id )
	{
		updateDatabasePlayerAccount( id )
		ClearPlayerData( id )

		return PLUGIN_HANDLED;
	}

	public forward_client_userinfochanged( id, buffer )
	{
		if( !is_user_connected( id ) )
		{
			return FMRES_IGNORED;
		}

		static oldname[ MAX_NAME_LENGTH ], newname[ MAX_NAME_LENGTH ]
		get_user_name( id, oldname, sizeof oldname - 1 )
		engfunc( EngFunc_InfoKeyValue, buffer, g_name, newname, sizeof newname - 1 )

		if( equal( newname, oldname ) )
		{
			return FMRES_IGNORED;
		}

		static iflags, szflags[ 28 ]
		get_pcvar_string( pcvar_user_immunity, szflags, sizeof szflags - 1 )
		iflags = read_flags( szflags )

		if( !( get_user_flags( id ) & iflags ) )
		{
			new bool:notchange = false;

			if( get_pcvar_num( pcvar_changenick ) )
			{
				if( check_name( id, newname, 2 ) == 1 )
				{
					client_print_color( id, print_team_default, "^x04[ACP]^x01 %L", LANG_PLAYER, "ADMIN_NAME_MESSAGE" )

					notchange = true;
				}
			}
			else
			{
				client_print_color( id, print_team_default, "^x04[ACP]^x01 %L", LANG_PLAYER, "ADMIN_NAME_CHANGE" )

				notchange = true;
			}

			if( notchange )
			{
				engfunc( EngFunc_SetClientKeyValue, id, buffer, g_name, oldname )
				client_cmd( id, "name ^"%s^"; setinfo name ^"%s^"", oldname, oldname )

				return FMRES_SUPERCEDE;
			}
		}

		if( get_pcvar_num( pcvar_announce ) )
		{
			msg_name_change( id, oldname, newname )
		}

		return FMRES_SUPERCEDE;
	}

	msg_name_change( id, oldname[ ], newname[ ] )
	{
		message_begin( MSG_BROADCAST, g_msgid_saytext )
		write_byte( id )
		write_string( g_name_change )
		write_string( oldname )
		write_string( newname )
		message_end( )
	}

	public updateDatabasePlayerAccount( id )
	{
		if( !g_Player_Data[ id ][ PL_auth ] )
		{
			return PLUGIN_HANDLED;
		}

		new user_online, timestamp, remove_points, data[ PlayersList ]
		timestamp = get_systime( 0 )
		user_online = get_user_time( id, 1 )
		ArrayGetArray( g_apData, g_Player_Data[ id ][ PL_key ], data )
		remove_points = g_Player_Data[ id][ PL_points ] - data[ Upoints ]

		if( remove_points != 0 )
		{
			data[ Upoints ] = g_Player_Data[ id ][ PL_points ]
			ArraySetArray( g_apData, g_Player_Data[ id ][ PL_key ], data)
		}

		if( g_Player_Data[ id ][ PL_auth ] == 3 )
		{
			new player_name[ 44 ]
			copy( player_name, 43, g_Player_Data[ id ][ PL_name ] )
			replace_all( player_name, 43, "\", "\\" )
			replace_all( player_name, 43, "'", "\'" )
			formatex( g_Cache, 554, "UPDATE `acp_players` SET last_time = '%i', online = online+%i, points = points+%i, player_nick = '%s', player_ip = '%s' WHERE userid = '%i'", timestamp, user_online, remove_points, player_name, g_Player_Data[ id ][ PL_ip ], g_Player_Data[ id ][ PL_id ] )
		}
		else if( g_Player_Data[ id ][ PL_auth ] == 2 )
		{
			new player_name[ 44 ]
			copy( player_name, 43, g_Player_Data[ id ][ PL_name ] )
			replace_all( player_name, 43, "\", "\\" )
			replace_all( player_name, 43, "'", "\'" )
			formatex( g_Cache, 554, "UPDATE `acp_players` SET last_time = '%i', online = online+%i, points = points+%i, player_nick = '%s', steamid = '%s' WHERE userid = '%i'", timestamp, user_online, remove_points, player_name, g_Player_Data[ id ][ PL_steam ], g_Player_Data[ id ][ PL_id ] )
		}
		else
		{
			formatex(g_Cache, 554, "UPDATE `acp_players` SET last_time = '%i', online = online+%i, points = points+%i, player_ip = '%s', steamid = '%s' WHERE userid = '%i'", timestamp, user_online, remove_points, g_Player_Data[ id ][ PL_ip ], g_Player_Data[ id ][ PL_steam ], g_Player_Data[ id ][ PL_id ] )
		}

		SQL_ThreadQuery( info, "QueryHandle", g_Cache )

		return PLUGIN_HANDLED;
	}

	public check_name( id, name[ ], action )
	{
		new username[ 44 ]
		copy( username, sizeof( username ) - 1, name )
		strtolower( username )

		for( new ib = 0; ib < sizeof( blocked_action_list ); ib++ )
		{
			new Array:nicksData

			if( TrieGetCell( g_tbAction, blocked_action_list[ ib ], nicksData ) )
			{
				for( new y = 0; y < ArraySize( nicksData ); y++ )
				{
					new buff[ 64 ]
					ArrayGetString( nicksData, y, buff, sizeof( buff ) - 1 )

					g_rgxRes = regex_match( username, buff, g_regex_return, error, 127 )

					if( g_rgxRes >= REGEX_OK )
					{
						regex_free( g_rgxRes )
						name_logs( id, name, ib, action )

						return ib;
					}
				}
			}
		}

		return 0;
	}

	public name_logs( id, name[ ], pattern, action )
	{
		if( is_user_bot( id ) || !is_user_connected( id ) || !get_pcvar_num( pcvar_savelogs ) )
		{
			return PLUGIN_HANDLED;
		}

		new authid[ 32 ], ip[ 16 ], username[ 44 ], timestamp = get_systime( 0 )
		get_user_authid( id, authid, sizeof( authid ) - 1 )
		get_user_ip( id, ip, sizeof( ip ) - 1, 1 )
		copy( username, sizeof( username ) - 1, name )
		replace_all( username, sizeof( username ) - 1, "'", "\'" )

		new query[ 1001 ]
		format( query, 1000, "INSERT into `acp_nick_logs` ( serverip, name, authid, ip, timestamp, pattern, action ) values ( '%s', '%s', '%s', '%s', '%i', '%d', '%d' )", g_serverip, username, authid, ip, timestamp, pattern, action )
		SQL_ThreadQuery( info, "QueryHandle", query )

		return PLUGIN_CONTINUE;
	}

	public QueryHandle( FailState, Handle:hQuery, Error[ ], Errcode, Data[ ], DataSize )
	{
		log_amx( "SQL Error #%d - %s", Errcode, Error )
	}

	public ackSignalReserv( id )
	{
		server_cmd( "kick #%d ^"%L^"", get_user_userid( id ), id, "ADMIN_NICK_ENTRY" )

		return PLUGIN_HANDLED;
	}

	public ackSignalInactive( id )
	{
		server_cmd( "kick #%d ^"%L^"", get_user_userid( id ), id, "ADMIN_NICK_INACTIVE" )

		return PLUGIN_HANDLED;
	}

	public native_take_points( id, cnt )
	{
		if( g_Player_Data[ id ][ PL_auth ] )
		{
			if( ( g_Player_Data[ id ][ PL_points ] - cnt ) < 0 )
			{
				g_Player_Data[ id ][ PL_points ] = 0
			}
			else
			{
				g_Player_Data[ id ][ PL_points ] -= cnt
			}

			return g_Player_Data[ id ][ PL_points ]
		}

		return false;
	}

	public native_give_points( id, cnt )
	{
		if( g_Player_Data[ id ][ PL_auth ] )
		{
			g_Player_Data[ id ][ PL_points ] += cnt

			return 1;
		}

		return 0;
	}

	public native_player_auth( id )
	{
		return g_Player_Data[ id ][ PL_auth ]
	}

	public native_player_dbid( id )
	{
		return g_Player_Data[ id ][ PL_id ]
	}

	public native_player_points( id )
	{
		return g_Player_Data[ id ][ PL_points ]
	}

	public native_server_id( )
	{
		return g_serverID
	}

	stock delete_duplicate( string[ ], len )
	{
		new symbol[ 2 ]

		for( new i = 0; i < strlen( string ) - 1; i++ )
		{
			copy( symbol, 1, string[ i ] )
			replace_all( string[ i + 1 ], len - i - 1, symbol, "" )
		}
	}
