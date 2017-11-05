
	check_subnets( id, steamid[ ] )
	{
		if( is_user_bot( id ) || is_user_hltv( id ) || ( ( get_pcvar_num( pcvar_steam_immune ) == 1 ) && IsValidAuthid( steamid ) ) )
		{
			return;
		}
		else
		{
			new query[ 4096 ], data[ 1 ]
			format( query, 4095, "SELECT subipaddr, bitmask FROM `acp_bans_subnets` WHERE (approved = 1)" )

			data[ 0 ] = id
			SQL_ThreadQuery( g_SqlX, "check_subnets_", query, data, 1 )
		}
	}

	public check_subnets_( failstate, Handle:query, error[ ], errnum, data[ ], size )
	{
		new ip[ 16 ], id = data[ 0 ]
		get_user_ip( id, ip, 15, 1 )

		if( failstate )
		{
			new szQuery[ 256 ]
			MySqlX_ThreadError( szQuery, error, errnum, failstate, 17 )
		}
		else
		{
			if( !SQL_NumResults( query ) )
			{
				return PLUGIN_HANDLED;
			}
			else
			{
				new colSubipaddr = SQL_FieldNameToNum( query, "subipaddr" )
				new colBitmask = SQL_FieldNameToNum( query, "bitmask" )

				while( SQL_MoreResults( query ) )
				{
					SQL_ReadResult( query, colSubipaddr, Subipaddr, 16 )
					SQL_ReadResult( query, colBitmask, Bitmask, 16 )

					SubnetFound[ id ] = false;

					if( strlen( Bitmask ) > 2 )
					{
						Subipmask = Bitmask
					}
					else
					{
						new num = str_to_num( Bitmask )

						switch( num )
						{
							case 1: format( Subipmask, 15, "128.0.0.0" )
							case 2: format( Subipmask, 15, "192.0.0.0" )
							case 3: format( Subipmask, 15, "224.0.0.0" )
							case 4: format( Subipmask, 15, "240.0.0.0" )
							case 5: format( Subipmask, 15, "248.0.0.0" )
							case 6: format( Subipmask, 15, "252.0.0.0" )
							case 7: format( Subipmask, 15, "254.0.0.0" )
							case 8: format( Subipmask, 15, "255.0.0.0" )
							case 9: format( Subipmask, 15, "255.128.0.0" )
							case 10: format( Subipmask, 15, "255.192.0.0" )
							case 11: format( Subipmask, 15, "255.224.0.0" )
							case 12: format( Subipmask, 15, "255.240.0.0" )
							case 13: format( Subipmask, 15, "255.248.0.0" )
							case 14: format( Subipmask, 15, "255.252.0.0" )
							case 15: format( Subipmask, 15, "255.254.0.0" )
							case 16: format( Subipmask, 15, "255.255.0.0" )
							case 17: format( Subipmask, 15, "255.255.128.0" )
							case 18: format( Subipmask, 15, "255.255.192.0" )
							case 19: format( Subipmask, 15, "255.255.224.0" )
							case 20: format( Subipmask, 15, "255.255.240.0" )
							case 21: format( Subipmask, 15, "255.255.248.0" )
							case 22: format( Subipmask, 15, "255.255.252.0" )
							case 23: format( Subipmask, 15, "255.255.254.0" )
							case 24: format( Subipmask, 15, "255.255.255.0" )
							case 25: format( Subipmask, 15, "255.255.255.128" )
							case 26: format( Subipmask, 15, "255.255.255.192" )
							case 27: format( Subipmask, 15, "255.255.255.224" )
							case 28: format( Subipmask, 15, "255.255.255.240" )
							case 29: format( Subipmask, 15, "255.255.255.248" )
							case 30: format( Subipmask, 15, "255.255.255.252" )
							case 31: format( Subipmask, 15, "255.255.255.254" )
						}
					}

					if( net_belongs( ip_to_number( ip ), ip_to_number( "255.255.255.255" ), ip_to_number( Subipaddr ), ip_to_number( Subipmask ) ) == 1 )
					{
						SubnetFound[ id ] = true;

						if( SubnetFound[ id ] == true )
						{
							new complain_url[ 80 ], complain_name[ 50 ], complain_authid[ 50 ], complain_ip[ 30 ]
							get_pcvar_string( pcvar_complainurl, complain_url, 79 )
							get_user_name( id, complain_name, 49 )
							get_user_authid( id, complain_authid, 49 )
							get_user_ip( id, complain_ip, 29, 1 )

							client_print( id, print_console, "****************************************" )
							client_print( id, print_console, "%L", LANG_PLAYER, "SUBNET_MSG_1" )
							client_print( id, print_console, "%L", LANG_PLAYER, "SUBNET_MSG_2", complain_name )
							client_print( id, print_console, "%L", LANG_PLAYER, "SUBNET_MSG_3", complain_authid )
							client_print( id, print_console, "%L", LANG_PLAYER, "SUBNET_MSG_4", complain_ip )
							client_print( id, print_console, "%L", LANG_PLAYER, "SUBNET_MSG_5", complain_url )
							client_print( id, print_console, "****************************************" )

							server_cmd( "kick #%d %L", get_user_userid( id ), LANG_SERVER, "YOUR_SUBNET_BANNED" )

							break;
						}
					}

					SQL_NextRow( query )
				}
			}
		}

		return PLUGIN_HANDLED;
	}

	stock ip_to_number( userip[ 16 ] )
	{
		new ipb1[ 12 ], ipb2[ 12 ], ipb3[ 12 ], ipb4[ 12 ], ip, nipb1, nipb2, nipb3, nipb4, uip[ 16 ]
		copy( uip, 16, userip )

		while( replace( uip, 16, ".", " " ) ) { }
		parse( uip, ipb1, 12, ipb2, 12, ipb3, 12, ipb4, 12 )
		nipb1 = str_to_num( ipb1 )
		nipb2 = str_to_num( ipb2 )
		nipb3 = str_to_num( ipb3 )
		nipb4 = str_to_num( ipb4 )
		ip = ( ( ( ( nipb1 * 256 ) + nipb2 ) * 256 ) + nipb3 ) + ( ( ( ( ( ( nipb1 * 256 ) + nipb2 ) * 256 ) + nipb3 ) * 255 ) + nipb4 )

		return ip;
	}

	stock net_hi_num( addr, mask )
	{
		return addr + 4294967296 - mask
	}

	stock net_belongs( ipaddr, ipmask, ipaddrin, ipmaskin )
	{
		if( ipaddr >= ipaddrin && net_hi_num( ipaddr, ipmask ) <= net_hi_num( ipaddrin, ipmaskin ) )
		{
			return 1;
		}

		return 0;
	}
