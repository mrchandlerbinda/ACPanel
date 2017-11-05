// ================================================================================================
//
//	Plugin: [ACP] Hud Manager
//	Authors: droper, Hafner
//	Version: 1.2
//	Last update: 30.03.2012
//
//	© Ohuma Dev Team, Hafner, 2012
//
//	Support: http://www.a114games.com/community/threads/acp-hud-manager.1705/
//
// ================================================================================================

#include < amxmodx >
#include < amxmisc >
#include < sqlx >

new const g_constHudMenu[] = "Hud Manager:";

new Array:g_Array;

new g_iArraySize;

enum _:data
{
	szText[ 64 ],
	iFlags
}

new Handle:g_hSqlTuple;

new Handle:g_hSqlConnect;


new g_menuPosition[ 33 ];

new g_szServerAddress[ 22 ];

new g_bitsPlayerHud[ 33 ];

new g_msgHideWeapon;


new pcvar_sql_host;

new pcvar_sql_user;

new pcvar_sql_pass;

new pcvar_sql_db;


public plugin_init ()
{
	register_plugin ( "[ACP] HUD Manager", "1.2", "droper & Hafner" );

	register_menucmd ( register_menuid ( g_constHudMenu ), 1023, "actionHudMenu" );

	register_clcmd ( "say /hud", "ClCmd_Hud" );
	
	register_clcmd ( "say_team /hud", "ClCmd_Hud" )

	register_concmd ( "acp_hud_manager_reload", "ConCmd_Reload", ADMIN_CFG );

	register_message ( g_msgHideWeapon = get_user_msgid ( "HideWeapon" ), "Message_HideWeapon" );

	pcvar_sql_host = register_cvar ( "acp_sql_host", "localhost" );

	pcvar_sql_user = register_cvar ( "acp_sql_user", "root" );

	pcvar_sql_pass = register_cvar ( "acp_sql_pass", "" );

	pcvar_sql_db = register_cvar ( "acp_sql_db", "acpanel" );

	register_cvar ( "acp_hud_manager_ip", "" );

	g_Array = ArrayCreate ( data );

	register_dictionary ( "acp_hud_manager.txt" );
}

public plugin_cfg ()
{
	new BaseInfo[ 4 ][ 32 ], szConfigsDir[ 64 ];

	get_configsdir ( szConfigsDir, charsmax( szConfigsDir ) );

	server_cmd ( "exec %s/acp/sql.cfg", szConfigsDir );
	server_exec ();

	get_pcvar_string ( pcvar_sql_host, BaseInfo[ 0 ], charsmax( BaseInfo[] ) );

	get_pcvar_string ( pcvar_sql_user, BaseInfo[ 1 ], charsmax( BaseInfo[] ) );

	get_pcvar_string ( pcvar_sql_pass, BaseInfo[ 2 ], charsmax( BaseInfo[] ) );

	get_pcvar_string (pcvar_sql_db, BaseInfo[ 3 ], charsmax( BaseInfo[] ) );

	g_hSqlTuple = SQL_MakeDbTuple ( BaseInfo[0], BaseInfo[1], BaseInfo[2], BaseInfo[3] );

	set_task ( 0.1, "InitServer" );

	set_task ( 0.3, "LoadMenu" );
}

public InitServer ()
{
	get_cvar_string ( "acp_hud_manager_ip", g_szServerAddress, charsmax( g_szServerAddress ) );

	if ( !strlen ( g_szServerAddress ) )
	{
		get_user_ip ( 0, g_szServerAddress, charsmax( g_szServerAddress ) );
	}

	new szHostname[ 32 ], szQuery[ 512 ];

	get_cvar_string ( "hostname", szHostname, charsmax( szHostname ) );

	formatex ( szQuery, charsmax( szQuery ), "INSERT INTO `acp_servers` (`address`, `hostname`) VALUES ('%s','%s') ON DUPLICATE KEY UPDATE hostname = '%s'", g_szServerAddress, szHostname, szHostname );

	SQL_ThreadQuery ( g_hSqlTuple, "QueryHandle", szQuery );
}

public LoadMenu ()
{
	new szError[ 192 ];

	if ( g_hSqlConnect == Empty_Handle )
	{
		new iErrCode;

		g_hSqlConnect = SQL_Connect ( g_hSqlTuple, iErrCode, szError, charsmax( szError ) );

		if ( g_hSqlConnect == Empty_Handle )
		{
			server_print ( "[Hud Manager] SQL Error #%d: %s", iErrCode, szError );

			return -1;
		}
	}

	new Handle:hQuery = SQL_PrepareQuery ( g_hSqlConnect, "SELECT name, flags FROM `acp_hud_manager` LEFT JOIN `acp_servers` ON acp_servers.address='%s' WHERE acp_servers.opt_hudmanager=1 ORDER BY priority", g_szServerAddress );
	new itemsCount = 0;

	if ( !SQL_Execute ( hQuery ) )
	{
		SQL_QueryError ( hQuery, szError, charsmax( szError ) );

		server_print ( "[Hud Manager] SQL Error: %s", szError );

		SQL_FreeHandle ( hQuery );

		return -1;
	}
	else if ( !SQL_NumResults ( hQuery ) )
	{
		server_print ( "[Hud Manager] Items not found" );
	}
	else
	{
		ArrayClear ( g_Array );

		new aItem[ data ];

		while ( SQL_MoreResults ( hQuery ) )
		{
			SQL_ReadResult ( hQuery, 0, aItem[ szText ], 63 );

			aItem[ iFlags ] = SQL_ReadResult ( hQuery, 1 );

			ArrayPushArray ( g_Array, aItem );

			itemsCount++;

			SQL_NextRow ( hQuery );
		}

		if ( itemsCount == 1 )
		{
			server_print ( "[Hud Manager] Loaded 1 item" );
		}
		else
		{
			server_print ( "[Hud Manager] Loaded %d items", itemsCount );
		}
	}

	SQL_FreeHandle ( hQuery );

	return itemsCount;
}

public client_connect ( id ) g_bitsPlayerHud[ id ] = 0;

public ConCmd_Reload ( id, level, cid )
{
	if ( cmd_access ( id, level, cid , 1 ) )
	{
		new count = LoadMenu ();

		if ( is_user_connected ( id ) )
		{
			switch ( count )
			{
				case -1: console_print ( id, "[Hud Manager] SQL Error."  );
				case 0:  console_print ( id, "[Hud Manager] Items not found" );
				case 1:  console_print ( id, "[Hud Manager] Loaded 1 item" );
				default: console_print ( id, "[Hud Manager] Loaded %d items", count );
			}
		}
	}

	return PLUGIN_HANDLED;
}

public ClCmd_Hud ( id )
{
	if ( ( g_iArraySize = ArraySize ( g_Array ) ) )
	{
		displayMenu ( id, g_menuPosition[ id ] = 0 );
	}
	else
	{
		client_print ( id, print_chat, "[Hud Manager] %L", id, "MANAGER_DISABLED" );
	}

	return PLUGIN_HANDLED;
}

public displayMenu ( id, pos )
{
	if ( pos < 0 )
	{
		return 0;
	}

	new menuBody[ 512 ], b = 0, aItem[ data ];

	new start = pos * 8;

	if ( start >= g_iArraySize )
	{
		start = pos = g_menuPosition[ id ] = 0;
	}

	new iLen = format ( menuBody, charsmax( menuBody ), "\yHud Manager:\R\y%d/%d^n^n", pos + 1, ( g_iArraySize - 1 ) / 8 + 1 );

	new iEnd = start +8;

	new _iEnd = iEnd;

	new keys = MENU_KEY_0;

	if ( iEnd > g_iArraySize )
	{
		iEnd = g_iArraySize;
	}

	for ( new a = start; a < _iEnd; a++ )
	{
		if ( a >= iEnd )
		{
			iLen += format ( menuBody[ iLen ], charsmax( menuBody ) - iLen, "^n" );

			continue;
		}

		keys |= ( 1 << b++ );

		ArrayGetArray ( g_Array, a, aItem );

		iLen += format ( menuBody[ iLen ], charsmax( menuBody ) - iLen, "\r%d\w. %s \R\r%s^n", b, aItem[ szText ], ( g_bitsPlayerHud[ id ] & aItem[ iFlags ] ) == aItem[ iFlags ] ? "YES" : "NO" );
	}

	if ( iEnd != g_iArraySize )
	{
		format ( menuBody[ iLen ], charsmax( menuBody ) - iLen, "\r9\w. %L^n\r0\w. %L", id, "MORE", id, pos ? "BACK" : "EXIT" );

		keys |= MENU_KEY_9;
	}
	else
		format ( menuBody[ iLen ], charsmax( menuBody ) - iLen, "^n\r0\w. %L", id, pos ? "BACK" : "EXIT"  );

	show_menu ( id, keys, menuBody, -1, g_constHudMenu );

	return 1;
}

public actionHudMenu ( id, key )
{
	switch ( key )
	{
		case 8: displayMenu ( id, ++g_menuPosition[ id ] );
		case 9: displayMenu ( id, --g_menuPosition[ id ] );
		default:
		{
			new aItem[ data ], iItem = g_menuPosition[ id ] * 8 + key;

			ArrayGetArray ( g_Array, iItem, aItem );

			new bitsPlayer = g_bitsPlayerHud[ id ], bitsFlags = aItem[ iFlags ];

			if ( bitsPlayer & bitsFlags == bitsFlags )
			{
				bitsPlayer &= ~bitsFlags;
			}
			else
			{
				bitsPlayer |= bitsFlags;
			}

			message_begin ( MSG_ONE_UNRELIABLE, g_msgHideWeapon, .player = id );
			{
				write_byte ( bitsPlayer );
			}
			message_end ();

			g_bitsPlayerHud[ id ] = bitsPlayer;

			displayMenu ( id, g_menuPosition[ id ]);
		}
	}
}

public Message_HideWeapon ( msg_id, msg_dest, msg_entity )
{
	set_msg_arg_int ( 1, ARG_BYTE, g_bitsPlayerHud[ msg_entity ] );
}

public QueryHandle ( FailState, Handle:hQuery, Error[], Errcode, Data[], DataSize )
{
	if ( FailState )
	{
		log_amx("[Hud Manager] SQL Error #%d - %s", Errcode, Error);
	}
}