
	public amx_find( id, level, cid )
	{
		if( !cmd_access( id, level, cid, 2 ) )
		{
			return PLUGIN_HANDLED;
		}

		read_args( g_search_player_steamid, 50 )
		trim( g_search_player_steamid )

		new query[ 512 ], data[ 2 ]

		if( IsValidAuthid( g_search_player_steamid ) )
		{
			format( query, 511, "SELECT bid, ban_created, ban_length, ban_reason, admin_nick, admin_id, player_nick, player_ip \
				FROM `acp_bans` WHERE player_nick='%s'", g_search_player_steamid )
		}
		else
		{
			format( query, 511, "SELECT bid, ban_created, ban_length, ban_reason, admin_nick, admin_id, player_nick, player_ip \
				FROM `acp_bans` WHERE player_id='%s'", g_search_player_steamid )
		}

		data[ 0 ] = id
		SQL_ThreadQuery( g_SqlX, "amx_find_", query, data, 1 )

		return PLUGIN_HANDLED;
	}

	public amx_find_( failstate, Handle:query, error[ ], errnum, data[ ], size )
	{
		new id = data[ 0 ]
		new bool:serverCmd = false;

		/* Determine if this was a server command or a command issued by a player in the game */
		if( id == 0 )
		{
			serverCmd = true;
		}

		if( failstate )
		{
			new szQuery[ 256 ]
			MySqlX_ThreadError( szQuery, error, errnum, failstate, 14 )
		}
		else
		{
			new bid[ 20 ], ban_created[ 50 ], ban_length[ 50 ], ban_reason[ 255 ], admin_nick[ 100 ], admin_steamid[ 50 ], player_nick[ 100 ], remaining[ 128 ], player_ip[ 50 ]
			new ban_created_int, ban_length_int, current_time_int, ban_left

			if( !SQL_NumResults( query ) )
			{
				if( serverCmd )
				{
					log_message( "%L", LANG_PLAYER, "AMX_FIND_NORESULT", g_search_player_steamid )
				}
				else
				{
					console_print( id, "* %L", LANG_PLAYER, "AMX_FIND_NORESULT", g_search_player_steamid )
				}

				return PLUGIN_HANDLED;
			}
			else
			{
				if( serverCmd )
				{
					log_message( "%L", LANG_PLAYER, "AMX_FIND_RESULT_1", g_search_player_steamid )
				}
				else
				{
					client_print( id, print_console, "* %L", LANG_PLAYER, "AMX_FIND_RESULT_1", g_search_player_steamid )
				}

				while( SQL_MoreResults( query ) )
				{
					SQL_ReadResult( query, 0, bid, 20 )
					SQL_ReadResult( query, 1, ban_created, 50 )
					SQL_ReadResult( query, 2, ban_length, 50 )
					SQL_ReadResult( query, 3, ban_reason, 255 )
					SQL_ReadResult( query, 4, admin_nick, 50 )
					SQL_ReadResult( query, 5, admin_steamid, 50 )
					SQL_ReadResult( query, 6, player_nick, 50 )
					SQL_ReadResult( query, 7, player_ip, 50 )

					current_time_int = get_systime( 0 )
					ban_created_int = str_to_num( ban_created )
					ban_length_int = str_to_num( ban_length ) * 60

					if( ( ban_length_int == 0 ) || ( ban_created_int == 0 ) )
					{
						format( remaining, 127, "%L", LANG_PLAYER, "MSG_10" )
					}
					else
					{
						ban_left = ( ban_created_int + ban_length_int - current_time_int )

						if( ban_left <= 0 )
						{
							format( remaining, 127, "%L", LANG_PLAYER, "AMX_FIND_RESULT_5" )
						}
						else
						{
							get_time_length( id, ban_left, timeunit_seconds, remaining, 127 )
						}
					}

					if( serverCmd )
					{
						log_message( " " )
						log_message( "********************" )
						log_message( "%L", LANG_SERVER, "AMX_FIND_RESULT_2", bid, player_nick, player_ip )
						log_message( "%L", LANG_SERVER, "AMX_FIND_RESULT_3", admin_nick, admin_steamid, ban_reason )
						log_message( "%L", LANG_SERVER, "AMX_FIND_RESULT_4", ban_length, remaining )
						log_message( "********************" )
						log_message( " " )
					}
					else
					{
						client_print( id, print_console, " " )
						client_print( id, print_console, "********************" )
						client_print( id, print_console, "%L", LANG_PLAYER, "AMX_FIND_RESULT_2", bid, player_nick, player_ip )
						client_print( id, print_console, "%L", LANG_PLAYER, "AMX_FIND_RESULT_3", admin_nick, admin_steamid, ban_reason )
						client_print( id, print_console, "%L", LANG_PLAYER, "AMX_FIND_RESULT_4", ban_length, remaining )
						client_print( id, print_console, "********************" )
						client_print( id, print_console, " " )
					}

					SQL_NextRow( query )
				}
			}
		}

		return PLUGIN_HANDLED;
	}

	public amx_findex( id, level, cid )
	{
		if( !cmd_access( id, level, cid, 2 ) )
		{
			return PLUGIN_HANDLED;
		}

		new steamidorusername[ 50 ], player_steamid[ 50 ]
		read_args( steamidorusername, 50 )
		trim( steamidorusername )

		new player = find_player( "c", steamidorusername )

		if( !player )
		{
			player = find_player( "bl", steamidorusername )
		}

		if( player )
		{
			if( get_user_flags( player ) & ADMIN_IMMUNITY )
			{
				client_print( id, print_console, "* %L", LANG_PLAYER, "HAS_IMMUNITY" )

				return PLUGIN_HANDLED;
			}
			else if( is_user_bot( player ) )
			{
				client_print( id, print_console, "* %L", LANG_PLAYER, "IS_BOT" )

				return PLUGIN_HANDLED;
			}
			else if( is_user_hltv( player ) )
			{
				client_print( id, print_console, "* %L", LANG_PLAYER, "IS_HLTV" )

				return PLUGIN_HANDLED;
			}

			get_user_authid( player, player_steamid, 50 )
		}
		else
		{
			format( player_steamid, 50, "%s", steamidorusername )
		}

		new query[ 512 ], data[ 2 ]

		if( IsValidAuthid( player_steamid ) )
		{
			format( query, 511, "SELECT bhid, ban_created, ban_length, ban_reason, admin_nick, admin_id, player_nick \
				FROM `acp_bans_history` WHERE player_ip='%s' ORDER BY ban_created DESC LIMIT 0,10", player_steamid )
		}
		else
		{
			format( query, 511, "SELECT bhid, ban_created, ban_length, ban_reason, admin_nick, admin_id, player_nick \
				FROM `acp_bans_history` WHERE player_id='%s' ORDER BY ban_created DESC LIMIT 0,10", player_steamid )
		}

		data[ 0 ] = id
		data[ 1 ] = player
		SQL_ThreadQuery( g_SqlX, "amx_findex_", query, data, 2 )

		return PLUGIN_CONTINUE;
	}

	public amx_findex_( failstate, Handle:query, error[ ], errnum, data[ ], size )
	{
		new id = data[ 0 ]
		new player = data[ 1 ]
		new bool:serverCmd = false;

		/* Determine if this was a server command or a command issued by a player in the game */
		if( id == 0 )
		{
			serverCmd = true;
		}

		if( failstate )
		{
			new szQuery[ 256 ]
			MySqlX_ThreadError( szQuery, error, errnum, failstate, 15 )
		}
		else
		{
			new player_steamid[ 50 ]
			get_user_authid( player, player_steamid, 50 )

			new bid[ 20 ], ban_created[ 50 ], ban_length[ 50 ], ban_reason[ 255 ], admin_nick[ 100 ], admin_steamid[ 50 ], player_nick[ 100 ], remaining[ 128 ], player_ip[ 50 ]
			new ban_created_int, ban_length_int, current_time_int, ban_left

			if( !SQL_NumResults( query ) )
			{
				if( serverCmd )
				{
					log_message( "%L", LANG_PLAYER, "AMX_FIND_NORESULT", player_steamid )
				}
				else
				{
					client_print( id, print_console, "* %L", LANG_PLAYER, "AMX_FIND_NORESULT", player_steamid )
				}

				return PLUGIN_HANDLED;
			}
			else
			{
				if( serverCmd )
				{
					log_message( "%L", LANG_PLAYER, "AMX_FIND_RESULT_1", player_steamid )
				}
				else
				{
					client_print( id, print_console, "* %L", LANG_PLAYER, "AMX_FIND_RESULT_1", player_steamid )
				}

				while( SQL_MoreResults( query ) )
				{
					SQL_ReadResult( query, 0, bid, 20 )
					SQL_ReadResult( query, 1, ban_created, 50 )
					SQL_ReadResult( query, 2, ban_length, 50 )
					SQL_ReadResult( query, 3, ban_reason, 255 )
					SQL_ReadResult( query, 4, admin_nick, 50 )
					SQL_ReadResult( query, 5, admin_steamid, 50 )
					SQL_ReadResult( query, 6, player_nick, 50 )
					SQL_ReadResult( query, 7, player_ip, 50 )

					current_time_int = get_systime( 0 )
					ban_created_int = str_to_num( ban_created )
					ban_length_int = str_to_num( ban_length ) * 60
		
					if( ( ban_length_int == 0 ) || ( ban_created_int == 0 ) )
					{
						remaining = "eternity!"
					}
					else
					{
						ban_left = ( ban_created_int + ban_length_int - current_time_int )

						if( ban_left <= 0 )
						{
							format( remaining, 127, "none", ban_left )
						}
						else
						{
							get_time_length( id, ban_left, timeunit_seconds, remaining, 127 )
						}
					}

					if( serverCmd )
					{
						log_message( " " )
						log_message( "********************" )
						log_message( "%L", LANG_SERVER, "AMX_FIND_RESULT_2", bid, player_nick, player_ip )
						log_message( "%L", LANG_SERVER, "AMX_FIND_RESULT_3", admin_nick, admin_steamid, ban_reason )
						log_message( "%L", LANG_SERVER, "AMX_FIND_RESULT_4", ban_length, remaining )
						log_message( "********************" )
						log_message( " " )
					}
					else
					{
						client_print( id, print_console, " " )
						client_print( id, print_console, "********************" )
						client_print( id, print_console, "%L", LANG_PLAYER, "AMX_FIND_RESULT_2", bid, player_nick, player_ip )
						client_print( id, print_console, "%L", LANG_PLAYER, "AMX_FIND_RESULT_3", admin_nick, admin_steamid, ban_reason )
						client_print( id, print_console, "%L", LANG_PLAYER, "AMX_FIND_RESULT_4", ban_length, remaining )
						client_print( id, print_console, "********************" )
						client_print( id, print_console, " " )
					}

					SQL_NextRow( query )
				}
			}
		}

		return PLUGIN_HANDLED;
	}
