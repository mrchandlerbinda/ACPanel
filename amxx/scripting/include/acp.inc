#if defined _acp_included
	#endinput
#endif

#define _acp_included

#pragma library acp

/**
 * Returns a player authtype.
 *
 * @param id			Player index.
 * @return				0 - noauth, 1 - nick, 2 - ip, 3 - steam
 */
native acp_player_auth( id );

/**
 * Returns a player user DB_ID.
 *
 * @param id			Player index.
 * @return				Internal user DB_ID, or -1 if not yet chosen.
 */
native acp_player_dbid( id );

/**
 * Returns a player points.
 *
 * @param id			Player index.
 * @return				Current player points.
 */
native acp_player_points( id );

/**
 * Returns a player points.
 *
 * @param id			Player index.
 * @return				Current player points.
 */
native acp_take_points( id, cnt );

/**
 * Returns a player points.
 *
 * @param id			Player index.
 * @return				Current player points.
 */
native acp_give_points( id, cnt );

/**
 * Returns a current server id or false.
 */
native acp_server_id( );

// executed if the db is initialized
forward acp_sql_initialized( Handle:sqlTuple );

// executed if plugin_end function
forward acp_endmap_func( );