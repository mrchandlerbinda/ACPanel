
	public check_player( id )
	{
		new player_steamid[ 32 ], player_ip[ 16 ], player_nick[ 64 ], isAuth = acp_player_auth( id )
		get_user_name( id, player_nick, 63 )
		replace_all( player_nick, sizeof( player_nick ) - 1, "'", "\'" )
		get_user_authid( id, player_steamid, 31 )
		get_user_ip( id, player_ip, 15, 1 )

		if( !SubnetBanDelete && !isAuth )
		{
			check_subnets( id, player_steamid )
		}

		new query[ 4096 ], data[ 1 ], timestamp = get_systime( 0 )

		if( !isAuth )
		{
			if( IsValidAuthid( player_steamid ) )
			{
				isAuth = 3
			}
			else
			{
				isAuth = 2
			}
		}

		switch( isAuth )
		{
			case 1:
			{
				format( query, 4095, "SELECT bid, ban_created, ban_length, ban_reason, admin_nick, \
					player_nick, player_id, player_ip, server_name \
					FROM `acp_bans` WHERE ( player_nick = '%s' AND ban_type = 'N' AND (%i < (ban_created+(ban_length*60)) OR ban_length = '0') ) \
					ORDER BY bid DESC LIMIT 1", player_nick, timestamp )
			}

			case 2:
			{
				format( query, 4095, "SELECT bid, ban_created, ban_length, ban_reason, admin_nick, \
					player_nick, player_id, player_ip, server_name \
					FROM `acp_bans` WHERE ( (player_ip = '%s' OR cookie_ip = '%s') AND ban_type = 'SI' AND (%i < (ban_created+(ban_length*60)) OR ban_length = '0') ) \
					ORDER BY bid DESC LIMIT 1", player_ip, player_ip, timestamp )
			}

			case 3:
			{
				format( query, 4095, "SELECT bid, ban_created, ban_length, ban_reason, admin_nick, \
					player_nick, player_id, player_ip, server_name \
					FROM `acp_bans` WHERE ( player_id = '%s' AND ban_type = 'S' AND (%i < (ban_created+(ban_length*60)) OR ban_length = '0') ) \
					ORDER BY bid DESC LIMIT 1", player_steamid, timestamp )
			}
		}

		data[ 0 ] = id
		SQL_ThreadQuery( g_SqlX, "check_player_", query, data, 1 )

		return PLUGIN_HANDLED;
	}

	public check_player_( failstate, Handle:query, error[ ], errnum, data[ ], size )
	{
		new id = data[ 0 ]

		if( failstate )
		{
			new szQuery[ 256 ]
			MySqlX_ThreadError( szQuery, error, errnum, failstate, 17 )
		}
		else
		{
			if( SQL_NumResults( query ) )
			{
				new ban_length[ 50 ], ban_reason[ 255 ], admin_nick[ 100 ], server_name[ 100 ]
				new player_nick[ 50 ], player_steamid[ 50 ], player_ip[ 30 ]
				new bid = SQL_ReadResult( query, 0 )
				new ban_created = SQL_ReadResult( query, 1 )
				SQL_ReadResult( query, 2, ban_length, 49 )
				SQL_ReadResult( query, 3, ban_reason, 254 )
				SQL_ReadResult( query, 4, admin_nick, 99 )
				SQL_ReadResult( query, 5, player_nick, 49 )
				SQL_ReadResult( query, 6, player_steamid, 49 )
				SQL_ReadResult( query, 7, player_ip, 29 )
				SQL_ReadResult( query, 8, server_name, 99 )

				new current_time_int = get_systime( 0 )
				new ban_length_int = str_to_num( ban_length ) * 60
				new complain_url[ 256 ]
				get_pcvar_string( pcvar_complainurl, complain_url, 255 )

				client_cmd( id, "echo **************************************************" )

				new show_activity = get_cvar_num( "amx_show_activity" )

				if( id == 0 )
				{
					show_activity = 1
				}

				switch( show_activity )
				{
					case 1: client_cmd( id, "echo %L", LANG_PLAYER, "MSG_9" )
					case 2: client_cmd( id, "echo %L", LANG_PLAYER, "MSG_8", admin_nick )
				}

				if( ban_length_int == 0 )
				{
					client_cmd( id, "echo %L", LANG_PLAYER, "MSG_10" )
				}
				else
				{
					new cTimeLength[ 128 ]
					new iSecondsLeft = ( ban_created + ban_length_int - current_time_int )
					get_time_length( id, iSecondsLeft, timeunit_seconds, cTimeLength, 127 )

					client_cmd( id, "echo %L", LANG_PLAYER, "MSG_12", cTimeLength )
				}

				client_cmd( id, "echo %L", LANG_PLAYER, "MSG_11", server_name )
				client_cmd( id, "echo %L", LANG_PLAYER, "MSG_13", player_nick )
				client_cmd( id, "echo %L", LANG_PLAYER, "MSG_2", ban_reason )
				client_cmd( id, "echo %L", LANG_PLAYER, "MSG_7", complain_url )
				client_cmd( id, "echo %L", LANG_PLAYER, "MSG_4", player_steamid )
				client_cmd( id, "echo %L", LANG_PLAYER, "MSG_5", player_ip )
				client_cmd( id, "echo **************************************************" )

				log_amx( "BID:<%d> Player:<%s> <%s> connected and got kicked, because of an active ban", bid, player_nick, player_steamid )

				new id_str[ 3 ]
				num_to_str( id, id_str, 3 )

				log_amx( "Delayed Kick - TASK ID1: <%d> ID2: <%s>", id, id_str )

				set_task( 3.5, "delayed_kick", 0, id_str, 3 )
			}
		}

		return PLUGIN_HANDLED;
	}
