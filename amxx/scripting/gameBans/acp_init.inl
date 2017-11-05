
	public banmod_online( id )
	{
		new ip_port[ 42 ]
		get_user_ip( 0, ip_port, 41 )
		strtok( ip_port, g_ip, 31, g_port, 9, ':' )

		log_amx( "The server IP:PORT is: %s:%s", g_ip, g_port )

		new query[ 512 ]
		new data[ 1 ]
		format( query, 511, "SELECT hostname, address, gametype, opt_motd, opt_bansubnets FROM `acp_servers` WHERE address = '%s:%s' LIMIT 1", g_ip, g_port )

		data[ 0 ] = id
		SQL_ThreadQuery( g_SqlX, "banmod_online_", query, data, 1 )
	}

	public banmod_online_( failstate, Handle:query, error[ ], errnum, data[ ], size )
	{
		new id = data[ 0 ]
		new servername[ 100 ]
		get_cvar_string( "hostname", servername, 99 )

		new modname[ 32 ]
		get_modname( modname, 31 )

		if( failstate )
		{
			new szQuery[ 256 ]
			MySqlX_ThreadError( szQuery, error, errnum, failstate, 1 )
		}
		else
		{
			replace_all( servername, 99, "\", "" )
			replace_all( servername, 99, "'", "" )

			if( !SQL_NumResults( query ) )
			{
				log_amx( "INSERT INTO `acp_servers` (hostname, address, gametype) VALUES ('%s', '%s:%s', '%s')", servername, g_ip, g_port, modname )

				new query[ 512 ]
				new data[ 1 ]
				format( query, 511, "INSERT INTO `acp_servers` (hostname, address, gametype) VALUES ('%s', '%s:%s', '%s')", servername, g_ip, g_port, modname )

				data[ 0 ] = id
				SQL_ThreadQuery( g_SqlX, "banmod_online_insert", query, data, 1 )
			}
			else
			{
				new ban_subnets_str = SQL_ReadResult( query, 4 )

				if( ban_subnets_str != 1 )
				{
					SubnetBanDelete = true;
				}

				log_amx( "UPDATE `acp_servers` SET hostname='%s',gametype='%s' WHERE address = '%s:%s'", servername, modname, g_ip, g_port )

				new query[ 512 ], data[ 1 ]
				format( query, 511, "UPDATE `acp_servers` SET hostname='%s',gametype='%s' WHERE address = '%s:%s'", servername, modname, g_ip, g_port )

				data[ 0 ] = id
				SQL_ThreadQuery( g_SqlX, "banmod_online_update", query, data, 1 )
			}
		}

		return PLUGIN_CONTINUE;
	}

	public banmod_online_insert( failstate, Handle:query, error[ ], errnum, data[ ], size )
	{
		if( failstate )
		{
			new szQuery[ 256 ]
			MySqlX_ThreadError( szQuery, error, errnum, failstate, 2 )
		}
	}

	public banmod_online_update( failstate, Handle:query, error[ ], errnum, data[ ], size )
	{
		if( failstate )
		{
			new szQuery[ 256 ]
			MySqlX_ThreadError( szQuery, error, errnum, failstate, 3 )
		}
	}

	public reasonReload( id, level, cid )
	{
		if( !cmd_access( id, level, cid, 1 ) )
		{
			return PLUGIN_HANDLED;
		}

		fetchReasons( id )

		if( id != 0 )
		{
			if( g_aNum == 1 )
			{
				console_print( id, "* %L", LANG_SERVER, "SQL_LOADED_REASON" )
			}
			else
			{
				console_print( id, "* %L", LANG_SERVER, "SQL_LOADED_REASONS", g_aNum )
			}
		}

		return PLUGIN_HANDLED;
	}

	public fetchReasons( id )
	{
		new query[ 512 ], data[ 1 ]
		format( query, 511, "SELECT reason FROM `acp_bans_reasons` WHERE address = '%s:%s'", g_ip, g_port )

		data[ 0 ] = id
		SQL_ThreadQuery( g_SqlX, "fetchReasons_", query, data, 1 )
	}

	public fetchReasons_( failstate, Handle:query, error[ ], errnum, data[ ], size )
	{
		if( failstate )
		{
			new szQuery[ 256 ]
			MySqlX_ThreadError( szQuery, error, errnum, failstate, 5 )
		}
		else
		{
			if( !SQL_NumResults( query ) )
			{
				server_print( "* %L", LANG_SERVER, "NO_REASONS" )

				format( g_banReasons[ 0 ], 127, "%L", LANG_SERVER, "REASON_1" )
				format( g_banReasons[ 1 ], 127, "%L", LANG_SERVER, "REASON_2" )
				format( g_banReasons[ 2 ], 127, "%L", LANG_SERVER, "REASON_3" )
				format( g_banReasons[ 3 ], 127, "%L", LANG_SERVER, "REASON_4" )
				format( g_banReasons[ 4 ], 127, "%L", LANG_SERVER, "REASON_5" )
				format( g_banReasons[ 5 ], 127, "%L", LANG_SERVER, "REASON_6" )
				format( g_banReasons[ 6 ], 127, "%L", LANG_SERVER, "REASON_7" )
				format( g_banReasons[ 7 ], 127, "%L", LANG_SERVER, "REASON_8" )

				server_print( "* %L", LANG_SERVER, "SQL_LOADED_STATIC_REASONS" )

				g_aNum = 8

				return PLUGIN_HANDLED;
			}
			else
			{
				g_aNum = 0

				while( SQL_MoreResults( query ) )
				{
					SQL_ReadResult( query, 0, g_banReasons[ g_aNum ], 127 )
					SQL_NextRow( query )

					g_aNum++;
				}
			}

			if( g_aNum == 1 )
			{
				server_print( "* %L", LANG_SERVER, "SQL_LOADED_REASON" )
			}
			else
			{
				server_print( "* %L", LANG_SERVER, "SQL_LOADED_REASONS", g_aNum )
			}
		}

		return PLUGIN_HANDLED;
	}
