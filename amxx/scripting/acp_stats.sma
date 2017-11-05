
#include < amxmisc >
#include < fakemeta >
#include < hamsandwich >
#include < acp >
#include < sqlx >

#define DMG_GRENADE			(1<<24)
#define HUD_MIN_DURATION	0.2

enum _:MapWins
{
	MAPWIN_T, 
	MAPWIN_CT
}

enum _:PlayerStats
{
	PLR_USERID, 
	PLR_KILLS, 
	PLR_FFKILLS, 
	PLR_STRKILLS, 
	PLR_DEATHS, 
	PLR_FFDEATHS, 
	PLR_STRDEATHS, 
	PLR_SUICIDES, 
	PLR_HSKILLS, 
	PLR_T, 
	PLR_CT, 
	PLR_WINS
}

enum _:KillerStats
{
	KILLER_ID, 
	KILLER_HP, 
	KILLER_ARMOR
}

enum _:MeStats
{
	ME_DMG, 
	ME_HIT, 
	ME_COUNT_HITS, 
	ME_KILLS, 
	ME_HS
}

enum _:VictimData
{
	V_HITS, 
	V_DMG, 
	V_WEAPON[ 8 ], 
	V_NAME[ 44 ]
}

enum _:AttackerData
{
	V_HITS, 
	V_DMG, 
	V_WEAPON[ 8 ], 
	V_NAME[ 44 ]
}

new const g_HitsName[ 8 ][ ] = { "HIT_NONE", "HIT_HEAD", "HIT_CHEST", "HIT_STOMACH", "HIT_LEFTARM", "HIT_RIGHTARM", "HIT_LEFTLEG", "HIT_RIGHTLEG" }
new const g_WeaponName[ ][ 8 ] = { "", "p228", "", "scout", "grenade", "xm1014", "", "mac10", "aug", "", "elites", "fn57", "ump45", "sg550", "galil", "famas", "usp", "glock", "awp", "mp5", "m249", "m3", "m4a1", "tmp", "g3sg1", "", "deagle", "sg552", "ak47", "knife", "p90" }

// Variables
new Handle:info
new g_Cache[ 4096 ]
new g_UserStats[ 33 ][ PlayerStats ]
new g_VictimList[ 33 ][ 33 ][ VictimData ], g_AttackerList[ 33 ][ 33 ][ AttackerData ]
new g_mapname[ 32 ], g_serverip[ 32 ], g_connections, g_current_players, g_iHealthValue
new g_Wins[ MapWins ], g_Killers[ 33 ][ KillerStats ], g_Me[ 33 ][ MeStats ]
new g_Hits[ 33 ][ 31 ][ 9 ], g_WeaponKills[ 33 ][ 31 ][ 2 ], g_Damage[ 33 ][ 31 ], g_StreakKills[ 33 ], g_StreakDeaths[ 33 ]
new g_Weapon[ 33 ], g_OldWeapon[ 33 ], g_OldAmmo[ 33 ], g_sBuffer[ 2048 ]
new bool:g_ShowHud[ 33 ], bool:g_haveShots[ 33 ]
new g_mapTime

// Some Vars
new pcvar_sql_host, pcvar_sql_user, pcvar_sql_pass, pcvar_sql_db
new pcvar_points_planted, pcvar_points_defused, pcvar_points_ffdeaths, pcvar_points_suicide, pcvar_points_ffkills, pcvar_points_head, pcvar_points_grenade,	pcvar_points_deaths
new	pcvar_points_deaths_knife, pcvar_points_deaths_glock18,	pcvar_points_deaths_usp, pcvar_points_deaths_awp, pcvar_points_deaths_p228, pcvar_points_deaths_scout, pcvar_points_deaths_xm1014, pcvar_points_deaths_mac10
new pcvar_points_deaths_aug, pcvar_points_deaths_elite, pcvar_points_deaths_fiveseven, pcvar_points_deaths_ump45, pcvar_points_deaths_sg550, pcvar_points_deaths_galil, pcvar_points_deaths_famas, pcvar_points_deaths_mp5navy
new pcvar_points_deaths_m249, pcvar_points_deaths_m3, pcvar_points_deaths_m4a1, pcvar_points_deaths_tmp, pcvar_points_deaths_g3sg1, pcvar_points_deaths_deagle, pcvar_points_deaths_sg552, pcvar_points_deaths_ak47, pcvar_points_deaths_p90
new pcvar_points_weapons, pcvar_points_weapons_knife, pcvar_points_weapons_glock18, pcvar_points_weapons_usp, pcvar_points_weapons_awp, pcvar_points_weapons_p228, pcvar_points_weapons_scout, pcvar_points_weapons_xm1014,	pcvar_points_weapons_mac10
new	pcvar_points_weapons_aug, pcvar_points_weapons_elite, pcvar_points_weapons_fiveseven, pcvar_points_weapons_ump45, pcvar_points_weapons_sg550, pcvar_points_weapons_galil, pcvar_points_weapons_famas, pcvar_points_weapons_mp5navy,	pcvar_points_weapons_m249
new	pcvar_points_weapons_m3, pcvar_points_weapons_m4a1,	pcvar_points_weapons_tmp, pcvar_points_weapons_g3sg1, pcvar_points_weapons_deagle, pcvar_points_weapons_sg552, pcvar_points_weapons_ak47, pcvar_points_weapons_p90
new pcvar_showbest, pcvar_show_mostdamage, pcvar_allowvictims, pcvar_minplayers, pcvar_metype, pcvar_hudduration, pcvar_debug

new const g_allplayerscmds[ ][ 64 ] = 
{
	"hp", "show_hp", "- show hp.", 
	"me", "show_me", "- show me."
}

public plugin_init( )
{
	register_plugin( "StatsX", "2.7", "Evo" )

	register_dictionary( "common.txt" )
	register_dictionary( "acp_stats.txt" )

	for( new i = 0; i < sizeof( g_allplayerscmds ); i = i + 3 )
	{
		register_saycmd( g_allplayerscmds[ i ], g_allplayerscmds[ i + 1 ], ADMIN_ALL, g_allplayerscmds[ i + 2 ] )
	}

	pcvar_debug = register_cvar( "acp_debug", "1" )
	pcvar_showbest = register_cvar( "acp_show_best", "1" )
	pcvar_show_mostdamage = register_cvar( "acp_most_damage", "1" )
	pcvar_allowvictims = register_cvar( "acp_allow_victims", "1" )
	pcvar_minplayers = register_cvar( "acp_min_players", "4" )
	pcvar_metype = register_cvar( "acp_me_type", "1" )
	pcvar_hudduration = register_cvar( "acp_hud_duration", "9.0" )

	pcvar_points_planted = register_cvar( "acp_points_planted", "3" )
	pcvar_points_defused = register_cvar( "acp_points_defused", "3" )
	pcvar_points_ffdeaths = register_cvar( "acp_points_ffdeaths", "2" )
	pcvar_points_suicide = register_cvar( "acp_points_suicide", "6" )
	pcvar_points_ffkills = register_cvar( "acp_points_ffkills", "3" )
	pcvar_points_head = register_cvar( "acp_points_head", "8" )

	pcvar_points_grenade = register_cvar( "acp_points_grenade", "4" )

	pcvar_points_deaths = register_cvar( "acp_points_deaths", "1" )
	pcvar_points_deaths_knife = register_cvar( "acp_points_deaths_knife", "20" )
	pcvar_points_deaths_glock18 = register_cvar( "acp_points_deaths_glock18", "14" )
	pcvar_points_deaths_usp = register_cvar( "acp_points_deaths_usp", "12" )
	pcvar_points_deaths_awp = register_cvar( "acp_points_deaths_awp", "6" )
	pcvar_points_deaths_p228 = register_cvar( "acp_points_deaths_p228", "16" )
	pcvar_points_deaths_scout = register_cvar( "acp_points_deaths_scout", "10" )
	pcvar_points_deaths_xm1014 = register_cvar( "acp_points_deaths_xm1014", "14" )
	pcvar_points_deaths_mac10 = register_cvar( "acp_points_deaths_mac10", "15" )
	pcvar_points_deaths_aug = register_cvar( "acp_points_deaths_aug", "13" )
	pcvar_points_deaths_elite = register_cvar( "acp_points_deaths_elite", "16" )
	pcvar_points_deaths_fiveseven = register_cvar( "acp_points_deaths_fiveseven", "16" )
	pcvar_points_deaths_ump45 = register_cvar( "acp_points_deaths_ump45", "9" )
	pcvar_points_deaths_sg550 = register_cvar( "acp_points_deaths_sg550", "8" )
	pcvar_points_deaths_galil = register_cvar( "acp_points_deaths_galil", "4" )
	pcvar_points_deaths_famas = register_cvar( "acp_points_deaths_famas", "4" )
	pcvar_points_deaths_mp5navy = register_cvar( "acp_points_deaths_mp5navy", "9" )
	pcvar_points_deaths_m249 = register_cvar( "acp_points_deaths_m249", "20" )
	pcvar_points_deaths_m3 = register_cvar( "acp_points_deaths_m3", "14" )
	pcvar_points_deaths_m4a1 = register_cvar( "acp_points_deaths_m4a1", "5" )
	pcvar_points_deaths_tmp = register_cvar( "acp_points_deaths_tmp", "15" )
	pcvar_points_deaths_g3sg1 = register_cvar( "acp_points_deaths_g3sg1", "8" )
	pcvar_points_deaths_deagle = register_cvar( "acp_points_deaths_deagle", "3" )
	pcvar_points_deaths_sg552 = register_cvar( "acp_points_deaths_sg552", "13" )
	pcvar_points_deaths_ak47 = register_cvar( "acp_points_deaths_ak47", "5" )
	pcvar_points_deaths_p90 = register_cvar( "acp_points_deaths_p90", "12" )

	pcvar_points_weapons = register_cvar( "acp_points_weapons", "1" )
	pcvar_points_weapons_knife = register_cvar( "acp_points_weapons_knife", "26" )
	pcvar_points_weapons_glock18 = register_cvar( "acp_points_weapons_glock18", "8" )
	pcvar_points_weapons_usp = register_cvar( "acp_points_weapons_usp", "10" )
	pcvar_points_weapons_awp = register_cvar( "acp_points_weapons_awp", "22" )
	pcvar_points_weapons_p228 = register_cvar( "acp_points_weapons_p228", "5" )
	pcvar_points_weapons_scout = register_cvar( "acp_points_weapons_scout", "9" )
	pcvar_points_weapons_xm1014 = register_cvar( "acp_points_weapons_xm1014", "7" )
	pcvar_points_weapons_mac10 = register_cvar( "acp_points_weapons_mac10", "6" )
	pcvar_points_weapons_aug = register_cvar( "acp_points_weapons_aug", "9" )
	pcvar_points_weapons_elite = register_cvar( "acp_points_weapons_elite", "5" )
	pcvar_points_weapons_fiveseven = register_cvar( "acp_points_weapons_fiveseven", "5" )
	pcvar_points_weapons_ump45 = register_cvar( "acp_points_weapons_ump45", "9" )
	pcvar_points_weapons_sg550 = register_cvar( "acp_points_weapons_sg550", "3" )
	pcvar_points_weapons_galil = register_cvar( "acp_points_weapons_galil", "17" )
	pcvar_points_weapons_famas = register_cvar( "acp_points_weapons_famas", "18" )
	pcvar_points_weapons_mp5navy = register_cvar( "acp_points_weapons_mp5navy", "11" )
	pcvar_points_weapons_m249 = register_cvar( "acp_points_weapons_m249", "7" )
	pcvar_points_weapons_m3 = register_cvar( "acp_points_weapons_m3", "7" )
	pcvar_points_weapons_m4a1 = register_cvar( "acp_points_weapons_m4a1", "20" )
	pcvar_points_weapons_tmp = register_cvar( "acp_points_weapons_tmp", "6" )
	pcvar_points_weapons_g3sg1 = register_cvar( "acp_points_weapons_g3sg1", "3" )
	pcvar_points_weapons_deagle = register_cvar( "acp_points_weapons_deagle", "12" )
	pcvar_points_weapons_sg552 = register_cvar( "acp_points_weapons_sg552", "9" )
	pcvar_points_weapons_ak47 = register_cvar( "acp_points_weapons_ak47", "20" )
	pcvar_points_weapons_p90 = register_cvar( "acp_points_weapons_p90", "6" )

	pcvar_sql_host = register_cvar( "acp_sql_host", "127.0.0.1", FCVAR_PROTECTED )
	pcvar_sql_user = register_cvar( "acp_sql_user", "acp", FCVAR_PROTECTED )
	pcvar_sql_pass = register_cvar( "acp_sql_pass", "acp", FCVAR_PROTECTED )
	pcvar_sql_db = register_cvar( "acp_sql_db", "acp", FCVAR_PROTECTED )

	RegisterHam( Ham_Killed, "player", "fw_HamKilled" )
	RegisterHam( Ham_TraceAttack, "player", "fw_TraceAttack", 1 )
	RegisterHam( Ham_TakeDamage, "player", "fw_TakeDamage_Pre" )
	RegisterHam( Ham_TakeDamage, "player", "fw_TakeDamage_Post", 1 )
	RegisterHam( Ham_CS_RoundRespawn, "player", "fw_CS_RoundRespawn", 1 )
	RegisterHam( Ham_Spawn, "player", "fw_Player_Spawn", 1 )

	register_logevent( "Team_Win", 6, "0=Team" )
	register_logevent( "BombPlantEvent", 3, "2=Planted_The_Bomb" )
	register_logevent( "BombDefusedEvent", 3, "2=Defused_The_Bomb" )
	register_message( get_user_msgid( "CurWeapon" ), "msgCurWeapon" )
}

public plugin_cfg( )
{
	for( new i = 0; i < MapWins; i++ )
	{
		g_Wins[ i ] = 0
	}

	g_connections = 0
	get_mapname( g_mapname, 31 )
	get_user_ip( 0, g_serverip, 31 )

	g_mapTime = get_systime( 0 )
}

public acp_endmap_func( )
{
	new g_date[ 14 ], len
	get_time( "%Y-%m-%d-%H", g_date, 13 )
	g_mapTime = get_systime( 0 ) - g_mapTime

	len = format( g_Cache, charsmax( g_Cache ), "INSERT INTO `acp_stats_maps` (`map`, `serverip`, `date`, `connections`, `games`, `ct_win`, `t_win`, `online`) \
		VALUES ('%s','%s','%s','%d','1','%d','%d','%d' )", g_mapname, g_serverip, g_date, g_connections, g_Wins[ MAPWIN_CT ], g_Wins[ MAPWIN_T ], g_mapTime )

	len += format( g_Cache[ len ], charsmax( g_Cache ) - len, " ON DUPLICATE KEY UPDATE `games` = `games` + 1, \
		`connections` = `connections` + VALUES(`connections`), `online` = `online` + VALUES(`online`), `ct_win` = `ct_win` + VALUES(`ct_win`), \
		`t_win` = `t_win` + VALUES(`t_win`)" )

	SQL_ThreadQuery( info, "QueryHandle", g_Cache )

	return PLUGIN_HANDLED;
}

public acp_sql_initialized( Handle:sqlTuple )
{
	if( info != Empty_Handle )
	{
		if( get_pcvar_num( pcvar_debug ) )
		{
			log_amx( "DB Info Tuple from admin.sma initialized twice!" )
		}

		return PLUGIN_HANDLED;
	}

	info = sqlTuple

	if( get_pcvar_num( pcvar_debug ) )
	{
		log_amx( "Received DB Info Tuple from admin.sma: %d", sqlTuple )
	}

	if( info == Empty_Handle )
	{
		if( get_pcvar_num( pcvar_debug ) )
		{
			log_amx( "DB Info Tuple from acp_stats is empty! Trying to get a valid one" )
		}

		new host[ 32 ], user[ 32 ], pass[ 32 ], dbname[ 32 ]
		get_pcvar_string( pcvar_sql_host, host, 31 )
		get_pcvar_string( pcvar_sql_user, user, 31 )
		get_pcvar_string( pcvar_sql_pass, pass, 31 )
		get_pcvar_string( pcvar_sql_db, dbname, 31 )

		info = SQL_MakeDbTuple( host, user, pass, dbname )
	}

	return PLUGIN_HANDLED;
}

UserAccess( id )
{
	if( acp_player_auth( id ) )
	{
		g_UserStats[ id ][ PLR_USERID ] = acp_player_dbid( id )
	}
	else
	{
		g_UserStats[ id ][ PLR_USERID ] = 0
	}
}

public client_connected( id )
{
	g_haveShots[ id ] = false;
}

public client_authorized( id )
{
	g_connections++;

	fw_CS_RoundRespawn( id )

	g_StreakKills[ id ] = 0
	g_StreakDeaths[ id ] = 0

	for( new i = 0; i < PlayerStats; i++ )
	{
		g_UserStats[ id ][ i ] = 0
	}

	for( new i = 0; i < 31; i++ )
	{
		arrayset( g_Hits[ id ][ i ], 0, 9 )
		arrayset( g_WeaponKills[ id ][ i ], 0, 2 )
	}

	UserAccess( id )
}

public client_putinserver( id )
{
	if( is_user_connected( id ) && !is_user_bot( id ) )
	{
		g_current_players++;
	}

	return PLUGIN_CONTINUE;
}

public client_infochanged( id )
{
	if( !is_user_connected( id ) )
	{
		return PLUGIN_CONTINUE;
	}

	new newname[ 32 ], oldname[ 32 ]
	get_user_name( id, oldname, 31 )
	get_user_info( id, "name", newname, 31 )

	if( !equali( newname, oldname ) )
	{
		UserAccess( id )
	}

	return PLUGIN_CONTINUE;
}

public client_disconnected( id )
{
	if( is_user_connected( id ) && !is_user_bot( id ) )
	{
		g_current_players--;
	}

	if( g_UserStats[ id ][ PLR_USERID ] != 0 )
	{
		SaveStats( id )
	}

	g_haveShots[ id ] = false;

	return PLUGIN_CONTINUE;
}

SaveStats( id )
{
	new g_date[ 14 ], len
	get_time( "%Y-%m-%d-%H", g_date, 13 )

	new userip[ 33 ], usersteam[ 35 ], username[ 44 ]
	get_user_ip( id, userip, 32, 1 )
	get_user_authid( id, usersteam, 34 )
	get_user_name( id, username, 43 )

	new useronline = get_user_time( id, 1 )
	new timestamp = get_systime( 0 )

	len = format( g_Cache, charsmax( g_Cache ), "INSERT INTO `acp_stats_players` ( \
		`date`, `serverip`, `dbid`, `map`, `connections`, `last_time`, `last_name`, `last_ip`, `last_steamid`, \
		`online`, `streak_deaths`, `streak_kills`, `deaths`, `ffdeaths`, `kills`, `ffkills`, `suicides`, \
		`headshotkills`, `t_team`, `ct_team`, `wins` )" )

	len += format( g_Cache[ len ], charsmax( g_Cache ) - len, " VALUES ( \
		'%s', '%s', '%i', '%s', '1', '%i', '%s', '%s', '%s', '%i', '%d', '%d', '%d', '%d', '%d', '%d', '%d', \
		'%d', '%d', '%d', '%d' )", g_date, g_serverip, g_UserStats[ id][ PLR_USERID ], g_mapname, timestamp, username, 
		userip, usersteam, useronline, g_UserStats[ id ][ 6 ], g_UserStats[ id ][ 3 ], g_UserStats[ id ][ 4 ], g_UserStats[ id ][ 5 ], 
		g_UserStats[ id ][ 1 ], g_UserStats[ id ][ 2 ], g_UserStats[ id ][ 7 ], g_UserStats[ id ][ 8 ], g_UserStats[ id ][ 9 ], g_UserStats[ id ][ 10 ], g_UserStats[ id ][ 11 ] )

	len += format( g_Cache[ len ], charsmax( g_Cache ) - len, " ON DUPLICATE KEY UPDATE \
		connections = connections + 1, last_time = VALUES(last_time), last_ip = VALUES(last_ip), \
		last_steamid = VALUES(last_steamid), online = online+VALUES(online), \
		streak_deaths = IF(streak_deaths < VALUES(streak_deaths), VALUES(streak_deaths), streak_deaths), " )

	len += format( g_Cache[len], charsmax( g_Cache ) - len, "streak_kills = IF(streak_kills < VALUES(streak_kills), \
		VALUES(streak_kills), streak_kills), deaths = deaths+VALUES(deaths), ffdeaths = ffdeaths+VALUES(ffdeaths), \
		kills = kills+VALUES(kills), ffkills = ffkills+VALUES(ffkills), suicides = suicides+VALUES(suicides), \
		headshotkills = headshotkills+VALUES(headshotkills), t_team = t_team+VALUES(t_team), \
		ct_team = ct_team+VALUES(ct_team), wins = wins+VALUES(wins)" )

	SQL_ThreadQuery( info, "QueryHandle", g_Cache )

	if( g_haveShots[ id ] )
	{
		len = format( g_Cache, charsmax( g_Cache ), "INSERT INTO `acp_stats_weapons_data` (`weaponid`, `dbid`, `date`, `serverip`, `shots`, `kills`, \
			`headshotkills`, `shot_head`, `shot_chest`, `shot_stomach`, `shot_leftarm`, `shot_rightarm`, `shot_leftleg`, \
			`shot_rightleg`, `damage`) VALUES" )

		for( new i = 1; i < 31; i++ )
		{
			if( i != 6 && i!= 9 && i != 25 )
			{
				if( g_Hits[ id ][ i ][ 0 ] )
				{
					len += format( g_Cache[ len ], charsmax( g_Cache ) - len,
						" ('%d', '%d', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d'),",
						i, 
						g_UserStats[ id ][ PLR_USERID ], 
						g_date, 
						g_serverip, 
						g_Hits[ id ][ i ][ 0 ], 
						g_WeaponKills[ id ][ i ][ 0 ], 
						g_WeaponKills[ id ][ i ][ 1 ], 
						g_Hits[ id ][ i ][ 1 ], 
						g_Hits[ id ][ i ][ 2 ], 
						g_Hits[ id ][ i ][ 3 ], 
						g_Hits[ id ][ i ][ 4 ], 
						g_Hits[ id ][ i ][ 5 ], 
						g_Hits[ id ][ i ][ 6 ], 
						g_Hits[ id ][ i ][ 7 ],
						g_Damage[ id ][ i ] )
				}
			}
		}	

		g_Cache[ --len ] = 0
		
		len += format( g_Cache[ len ], charsmax( g_Cache ) - len, " ON DUPLICATE KEY UPDATE \
			shots = shots + VALUES(shots), \
			kills = kills + VALUES(kills), \
			headshotkills = headshotkills + VALUES(headshotkills), \
			shot_head = shot_head + VALUES(shot_head), " )

		len += format( g_Cache[ len ], charsmax( g_Cache ) - len, "shot_chest = shot_chest + VALUES(shot_chest), \
			shot_stomach = shot_stomach + VALUES(shot_stomach), \
			shot_leftarm = shot_leftarm + VALUES(shot_leftarm), " )

		len += format( g_Cache[ len ], charsmax( g_Cache ) - len, "shot_rightarm = shot_rightarm + VALUES(shot_rightarm), \
			shot_leftleg = shot_leftleg + VALUES(shot_leftleg), \
			shot_rightleg = shot_rightleg + VALUES(shot_rightleg), \
			damage = damage + VALUES(damage)" )

		SQL_ThreadQuery( info, "QueryHandle", g_Cache )
	}
}

public fw_HamKilled( victim, attacker, shouldgib )
{
	if( is_user_alive( attacker ) )
	{
		if( is_user_connected( attacker ) )
		{
			g_Killers[ victim][ KILLER_ID ] = attacker
			g_Killers[ victim][ KILLER_HP ] = get_user_health( attacker )
			g_Killers[ victim][ KILLER_ARMOR ] = get_user_armor( attacker )
			g_Me[ attacker ][ ME_KILLS ]++;
		}
	}

	new type, minplayers = get_pcvar_num( pcvar_minplayers ), player = attacker

	if( minplayers <= 0 )
	{
		minplayers = 1
	}

	if( ( get_user_team( victim ) == get_user_team( attacker ) ) && ( victim != attacker ) )
	{
		type = 5

		new iPointsFFDeaths = get_pcvar_num( pcvar_points_ffdeaths )

		acp_take_points( attacker, iPointsFFDeaths )
	}
	else
	{
		type = 4

		new iPointsDeaths = get_pcvar_num( pcvar_points_deaths )

		if( equal( g_WeaponName[ g_Weapon[ victim ] ], "knife" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_knife )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "glock18" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_glock18 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "usp" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_usp )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "awp" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_awp )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "p228" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_p228 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "scout" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_scout )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "xm1014" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_xm1014 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "mac10" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_mac10 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "aug" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_aug )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "elite" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_elite )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "fiveseven" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_fiveseven )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "ump45" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_ump45 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "sg550" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_sg550 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "galil" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_galil )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "famas" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_famas )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "mp5navy" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_mp5navy )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "m249" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_m249 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "m3" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_m3 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "m4a1" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_m4a1 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "tmp" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_tmp )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "g3sg1" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_g3sg1 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "deagle" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_deagle )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "sg552" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_sg552 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "ak47" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_ak47 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ victim ] ], "p90" ) )
		{
			iPointsDeaths = get_pcvar_num( pcvar_points_deaths_p90 )
		}

		acp_take_points( victim, iPointsDeaths )
	}

	g_UserStats[ victim ][ 4 ]++;

	if( type == 5 )
	{
		g_UserStats[ victim ][ type ]++;
	}
	
	if( ( g_UserStats[ victim ][ PLR_USERID ] != 0 ) && ( g_current_players >= minplayers ) )
	{
		g_StreakKills[ victim ] = 0
		g_StreakDeaths[ victim ]++;

		if( g_UserStats[ victim ][ PLR_STRDEATHS ] < g_StreakDeaths[ victim ] )
		{
			g_UserStats[ victim ][ PLR_STRDEATHS ] = g_StreakDeaths[ victim ]
		}
	}

	if( victim == attacker || !is_user_connected( attacker ) )
	{
		type = 7
		player = victim

		new iPointsSuicide = get_pcvar_num( pcvar_points_suicide )

		acp_take_points( player, iPointsSuicide )
	}
	else if( get_user_team( attacker ) == get_user_team( victim ) )
	{
		type = 2

		new iPointsFFKills = get_pcvar_num( pcvar_points_ffkills )

		acp_take_points( attacker, iPointsFFKills )
	}
	else
	{
		type = 1

		new iPointsWeapons = get_pcvar_num( pcvar_points_weapons )

		if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "knife" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_knife )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "glock18" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_glock18 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "usp" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_usp )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "awp" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_awp )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "p228" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_p228 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "scout" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_scout )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "xm1014" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_xm1014 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "mac10" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_mac10 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "aug" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_aug )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "elite" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_elite )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "fiveseven" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_fiveseven )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "ump45" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_ump45 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "sg550" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_sg550 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "galil" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_galil )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "famas" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_famas )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "mp5navy" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_mp5navy )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "m249" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_m249 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "m3" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_m3 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "m4a1" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_m4a1 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "tmp" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_tmp )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "g3sg1" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_g3sg1 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "deagle" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_deagle )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "sg552" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_sg552 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "ak47" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_ak47 )
		}
		else if( equal( g_WeaponName[ g_Weapon[ attacker ] ], "p90" ) )
		{
			iPointsWeapons = get_pcvar_num( pcvar_points_weapons_p90 )
		}

		acp_give_points( attacker, iPointsWeapons )
	}

	g_UserStats[ player ][ 1 ]++;

	if( type != 1 )
	{
		g_UserStats[ player ][ type ]++;
	}

	if( type == 1 || type == 2 )
	{
		if( get_pdata_int( victim, 75 ) == HIT_HEAD )
		{
			g_WeaponKills[ attacker ][ g_Weapon[ attacker ] ][ 1 ]++;
			g_UserStats[ player ][ PLR_HSKILLS ]++;
			g_Me[ attacker ][ ME_HS ]++;

			new iPointsHead = get_pcvar_num( pcvar_points_head )

			acp_give_points( attacker, iPointsHead )
		}

		if( get_pdata_int( victim, 76 ) == DMG_GRENADE )
		{
			g_WeaponKills[ attacker ][ 4 ][ 0 ]++;

			new iPointsGrenade = get_pcvar_num( pcvar_points_grenade )

			acp_give_points( attacker, iPointsGrenade )
		}
		else
		{
			g_WeaponKills[ attacker ][ g_Weapon[ attacker ] ][ 0 ]++;
		}
	}

	if( ( g_UserStats[ player ][ PLR_USERID ] != 0 ) && ( g_current_players >= minplayers ) )
	{
		g_StreakDeaths[ player ] = 0
		g_StreakKills[ player ]++;

		if( g_UserStats[ player ][ PLR_STRKILLS ] < g_StreakKills[ player ] )
		{
			g_UserStats[ player ][ PLR_STRKILLS ] = g_StreakKills[ player ]
		}
	}

	if( get_pcvar_num( pcvar_allowvictims ) )
	{
		g_ShowHud[ victim ] = true;

		set_task( 1.0, "show_user_hudstats", victim )
	}
}

public fw_CS_RoundRespawn( id )
{
	for( new i = 0; i < KillerStats; i++ )
	{
		g_Killers[ id ][ i ] = 0
	}

	for( new i = 0; i < MeStats; i++ )
	{
		g_Me[ id ][ i ] = 0
	}

	for( new i = 1; i <= MaxClients; i++ )
	{
		arrayset( g_VictimList[ id ][ i ], 0, VictimData )
		arrayset( g_AttackerList[ id ][ i ], 0, AttackerData )
	}

	g_ShowHud[ id ] = false;
}

public fw_Player_Spawn( const player )
{
	if( is_user_alive( player ) && ( g_UserStats[ player ][ PLR_USERID ] != 0 ) )
	{
		new team

		if( get_user_team( player ) == 1 )
		{
			team = 9
		}
		else if( get_user_team( player ) == 2 )
		{
			team = 10
		}

		if( team )
		{
			g_UserStats[ player ][ team ]++;
		}
	}
}

public Team_Win( )
{
	static szTeam[ 10 ], winner
	read_logargv( 1, szTeam, 9 )

	if( szTeam[ 0 ] == 'T' )
	{
		winner = 0
	}
	else
	{
		winner = 1
	}

	if( g_current_players > 1 )
	{
		g_Wins[ winner ]++
	}

	if( get_playersnum( ) )
	{
		new players[ 32 ], playersNum, i, len, message[ 300 ]
		get_players( players, playersNum, "ch" )
		new maxKillsId = 0, maxKillsName[ 32 ]
		new maxDmgId = 0, maxDmgName[ 32 ]
		new allowbest = get_pcvar_num( pcvar_showbest )
		new allowvictims = get_pcvar_num( pcvar_allowvictims )
		new mostdamage = get_pcvar_num( pcvar_show_mostdamage )

		for( i = 0; i < playersNum; i++ )
		{
			if( mostdamage )
			{
				if( g_Me[ players[ i ] ][ ME_DMG ] > g_Me[ players[ maxDmgId ] ][ ME_DMG ] )
				{
					maxDmgId = i
				}
				else if( g_Me[ players[ i ] ][ ME_DMG ] == g_Me[ players[ maxDmgId ] ][ ME_DMG ] )
				{
					if( g_Me[ players[ i ] ][ ME_KILLS ] > g_Me[ players[ maxDmgId ] ][ ME_KILLS ] )
					{
						maxDmgId = i
					}
					else if( ( g_Me[ players[ i ] ][ ME_KILLS ] == g_Me[ players[ maxDmgId ] ][ ME_KILLS ]) && ( g_Me[ players[ i ] ][ ME_COUNT_HITS ] > g_Me[ players[ maxDmgId ] ][ ME_COUNT_HITS ] ) )
					{
						maxDmgId = i
					}
				}
			}

			if( allowbest )
			{
				if( g_Me[ players[ i ] ][ ME_KILLS ] > g_Me[ players[ maxKillsId ] ][ ME_KILLS ] )
				{
					maxKillsId = i	
				}
				else if( g_Me[ players[ i ] ][ ME_KILLS ] == g_Me[players[maxKillsId]][ME_KILLS] )
				{
					if( g_Me[ players[ i ] ] [ ME_HS ] > g_Me[ players[ maxKillsId ] ][ ME_HS ] )
					{
						maxKillsId = i
					}
					else if( ( g_Me[ players[ i ] ][ ME_HS ] == g_Me[ players[ maxKillsId ] ][ ME_HS ] ) && ( g_Me[ players[ i ] ][ ME_DMG ] > g_Me[ players[ maxKillsId ] ][ ME_DMG ] ) )
					{
						maxKillsId = i
					}
				}
			}

			if( ( g_UserStats[ players[ i ] ][ PLR_USERID ] != 0 ) && ( get_user_team( players[ i ] ) == ( winner + 1 ) ) )
			{
				g_UserStats[ players[ i ] ][ PLR_WINS ]++
			}
		}

		if( mostdamage )
		{
			if( g_Me[ players[ maxDmgId ] ][ ME_DMG ] )
			{
				get_user_name( players[ maxDmgId ], maxDmgName, 31 )

				len = format( message, charsmax( message ), "%L", LANG_SERVER, "ROUND_MOST_DAMAGE", maxDmgName )

				if( !g_Me[ players[ maxDmgId ] ][ ME_KILLS ] )
				{
					g_Me[ players[ maxDmgId ] ][ ME_KILLS ] = 0
				}

				if( !g_Me[ players[ maxDmgId ] ][ ME_COUNT_HITS ] )
				{
					g_Me[ players[ maxDmgId ] ][ ME_COUNT_HITS ] = 0
				}

				new Float:fGameEff = effec( players[ maxDmgId ] )
				new Float:fRndAcc = accuracy( players[ maxDmgId ] )

				len += format( message[ len ], charsmax( message ) - len, 
					"^n%d %L / %d %L / %d %L - %0.2f%% %L / %0.2f%% %L", 
					g_Me[ players[ maxDmgId ] ][ ME_DMG ], LANG_SERVER, "DMG", 
					g_Me[ players[ maxDmgId ] ][ ME_COUNT_HITS ], LANG_SERVER, "HIT_S", 
					g_Me[ players[ maxDmgId ] ][ ME_KILLS ], LANG_SERVER, "KILL_S", 
					fGameEff, LANG_SERVER, "EFF", fRndAcc, LANG_SERVER, "ACC" )
			}
			else
			{
				mostdamage = 0
			}			
		}

		if( allowbest )
		{
			if( g_Me[ players[ maxKillsId ] ][ ME_DMG ] )
			{
				get_user_name( players[ maxKillsId ], maxKillsName, 31 )

				if( mostdamage )
				{
					len += format( message[ len ], charsmax( message ) - len, "^n^n%L", LANG_SERVER, "ROUND_BEST_TITLE", maxKillsName )
				}
				else
				{
					len = format( message, charsmax( message ), "%L", LANG_SERVER, "ROUND_BEST_TITLE", maxKillsName )
				}

				if( !g_Me[ players[ maxKillsId ] ][ ME_KILLS ] )
				{
					g_Me[ players[ maxKillsId ] ][ ME_KILLS ] = 0
				}

				if( !g_Me[ players[ maxKillsId ] ][ ME_HS ] )
				{
					g_Me[ players[ maxKillsId ] ][ ME_HS ] = 0
				}

				new Float:fGameEff = effec( players[ maxKillsId ] )
				new Float:fRndAcc = accuracy( players[ maxKillsId ] )
		
				len += format( message[ len ], charsmax( message ) - len, 
					"^n%d %L / %d %L / %d %L - %0.2f%% %L / %0.2f%% %L", 
					g_Me[ players[ maxKillsId ] ][ ME_KILLS ], LANG_SERVER, "KILL_S", 
					g_Me[ players[ maxKillsId ] ][ ME_HS ], LANG_SERVER, "HS", 
					g_Me[ players[ maxKillsId ] ][ ME_DMG ], LANG_SERVER, "DMG", 
					fGameEff, LANG_SERVER, "EFF", fRndAcc, LANG_SERVER, "ACC" )
			}
			else
			{
				allowbest = 0
			}
		}

		if( allowbest || allowvictims || mostdamage )
		{
			for( i = 0; i < playersNum; i++ )
			{
				if( mostdamage || allowbest )
				{
					set_task( 1.0, "show_beststats", players[ i ], message, sizeof( message ) )
				}

				if( allowvictims && !g_ShowHud[ players[ i ] ] )
				{
					set_task( 1.0, "show_user_hudstats", players[ i ] )
				}
			}
		}
	}
}

public BombPlantEvent( )
{
	new iPlanted = get_loguser_index( )
	new iPointPlanted = get_pcvar_num( pcvar_points_planted )

	acp_give_points( iPlanted, iPointPlanted )
}

public BombDefusedEvent( )
{
	new iDefuser = get_loguser_index( )
	new iPointDefuser = get_pcvar_num( pcvar_points_defused )

	acp_give_points( iDefuser, iPointDefuser )
}

public msgCurWeapon( msgid, dest, id )
{
	if( get_msg_arg_int( 1 ) )
	{
		static wId, ammo
		wId = get_msg_arg_int( 2 )
		ammo = get_msg_arg_int( 3 )
		g_Weapon[ id ] = wId

		switch( wId )
		{
			case CSW_KNIFE:
			{
				g_OldWeapon[ id ] = wId

				return PLUGIN_CONTINUE;
			}
			case CSW_HEGRENADE, CSW_FLASHBANG, CSW_SMOKEGRENADE, CSW_C4:
			{
				return PLUGIN_CONTINUE;
			}
		}

		if( ( wId == g_OldWeapon[ id ] ) && ( g_OldAmmo[ id ] > ammo ) )
		{
			g_Hits[ id ][ wId ][ 0 ]++
		}

		g_OldWeapon[ id ] = wId
		g_OldAmmo[ id ] = ammo
	}

	return PLUGIN_CONTINUE;
}

public fw_TraceAttack( id, idattacker, Float:damage, Float:direction[ 3 ], traceresult, damagebits )
{
	if( !is_user_alive( idattacker ) )
	{
		return;
	}

	new hit = get_tr2( traceresult, TR_iHitgroup )
	g_Hits[ idattacker ][ g_Weapon[ idattacker ] ][ hit ]++
	g_Me[ idattacker ][ ME_COUNT_HITS ]++
	g_Me[ idattacker ][ ME_HIT ] = hit
	g_haveShots[ idattacker ] = true;
}

public fw_TakeDamage_Pre( victim, inflictor, attacker, Float:damage, damage_type )
{
	if( !is_user_connected( attacker ) )
	{
		return HAM_IGNORED;
	}

	g_iHealthValue = pev( victim, pev_health )
	get_user_name( victim, g_VictimList[ attacker ][ victim ][ V_NAME ], 43 )
	get_user_name( attacker, g_AttackerList[ victim ][ attacker ][ V_NAME ], 43 )
	g_VictimList[ attacker ][ victim ][ V_HITS ]++
	g_AttackerList[ victim ][ attacker ][ V_HITS ]++

	return HAM_IGNORED;
}

public fw_TakeDamage_Post( victim, inflictor, attacker, Float:damage, damage_type )
{
	if( !is_user_connected( attacker ) )
	{
		return HAM_IGNORED;
	}

	new iHealth = pev( victim, pev_health )
	new iDiff = g_iHealthValue - iHealth

	if( damage_type & DMG_GRENADE ) 
	{
		g_Hits[ attacker ][ 4 ][ 0 ]++
		g_Damage[ attacker ][ 4 ] += iDiff

		if( iHealth <= 0 )
		{
			copy( g_VictimList[ attacker ][ victim ][ V_WEAPON ], 7, g_WeaponName[ 4 ] )
			copy( g_AttackerList[ victim ][ attacker ][ V_WEAPON ], 7, g_WeaponName[ 4 ] )
		}
	}
	else
	{
		g_Hits[ attacker ][ g_Weapon[ attacker ] ][ 0 ]++
		g_Damage[ attacker ][ g_Weapon[ attacker ] ] += iDiff

		if( iHealth <= 0 )
		{
			copy( g_VictimList[ attacker ][ victim ][ V_WEAPON ], 7, g_WeaponName[ g_Weapon[ attacker ] ] )
			copy( g_AttackerList[ victim ][ attacker ][ V_WEAPON ], 7, g_WeaponName[ g_Weapon[ attacker ] ] )
		}
	}

	g_Me[ attacker ][ ME_DMG ] += iDiff
	g_VictimList[ attacker ][ victim ][ V_DMG ] += iDiff
	g_AttackerList[ victim ][ attacker ][V_DMG ] += iDiff

	return HAM_IGNORED;
}

public show_hp( id )
{
	if( !acp_player_auth( id ) )
	{
		client_print_color( id, print_team_default, "%L", LANG_PLAYER, "NO_ACC_COM" )
	
		return PLUGIN_HANDLED;
	}

	if( g_Killers[ id ][ KILLER_ID ] )
	{
		new name[ 32 ]
		get_user_name( g_Killers[ id ][ KILLER_ID ], name, 31 )

		client_print_color( id, print_team_red, "%L", id, "HP_MESSAGE", name, g_Killers[ id ][ KILLER_HP ], g_Killers[ id ][ KILLER_ARMOR ] )
	}
	else
	{
		client_print_color( id, print_team_red, "%L", id, "HP_NO_KILLER" )
	}

	return PLUGIN_HANDLED;
}

public show_me( id )
{
	if( !acp_player_auth( id ) )
	{
		client_print_color( id, print_team_default, "%L", LANG_PLAYER, "NO_ACC_COM" )
	
		return PLUGIN_HANDLED;
	}

	if( is_user_alive( id ) && ( get_pcvar_num( pcvar_metype ) != 0 ) )
	{
		return PLUGIN_HANDLED;
	}

	if( g_Me[ id ][ ME_DMG ] || g_Me[ id ][ ME_KILLS ] )
	{
		new hit[ 32 ]
		format( hit, 31, "%L", id, g_HitsName[ g_Me[ id ][ ME_HIT ] ] )

		client_print_color( id, print_team_default, "%L", id, "ME_MESSAGE", g_Me[ id ][ ME_KILLS ], g_Me[ id ][ ME_DMG ], hit )
	}
	else
	{
		client_print_color( id, print_team_default, "%L", id, "HIT_NOT" )
	}

	return PLUGIN_HANDLED;
}

// stats formulas

Float:accuracy( player )
{
	new i, iShots, iHits

	for( i = 1; i < 31; i++ )
	{
		if( g_Hits[ player ][ i ][ 0 ] )
		{
			iShots += g_Hits[ player ][ i ][ 0 ]
			iHits += g_Hits[ player ][ i ][ 1 ] + g_Hits[ player ][ i ][ 2 ] + g_Hits[ player ][ i ][ 3 ] + g_Hits[ player ][ i ][ 4 ]+g_Hits[ player][ i ][ 5 ] + g_Hits[ player ][ i ][ 6 ] + g_Hits[ player ][ i ][ 7 ]
		}
	}

	if( !iShots )
	{
		return ( 0.0 );
	}

	return ( 100.0 * float( iHits ) / float( iShots ) );
}

Float:effec( player )
{
	if( !g_UserStats[ player ][ 1 ] )
	{
		return ( 0.0 );
	}

	return ( 100.0 * float( g_UserStats[ player ][ 1 ] ) / float( g_UserStats[ player ][ 1 ] + g_UserStats[ player ][ 4 ] ) );
}

public show_user_hudstats( id )
{
	new Float:fDuration = get_pcvar_float( pcvar_hudduration )

	if( fDuration >= HUD_MIN_DURATION )
	{
		get_victims( id, g_sBuffer )
		set_hudtype_victim( fDuration )
		show_hudmessage( id, "%s", g_sBuffer )

		get_attackers( id, g_sBuffer )
		set_hudtype_attacker( fDuration )
		show_hudmessage( id, "%s", g_sBuffer )
	}
}

public show_beststats( message[ ], id )
{
	set_hudmessage( 100, 200, 0, 0.05, 0.55, 0, 0.02, 6.0, 0.0, 1.0, -1 )
	show_hudmessage( id, message )
}

set_hudtype_attacker( Float:fDuration )
{
	set_hudmessage( 220, 80, 0, 0.55, 0.35, 0, 6.0, fDuration, 1.0, 1.0, -1 )
}

set_hudtype_victim( Float:fDuration )
{
	set_hudmessage( 0, 80, 220, 0.55, 0.60, 0, 6.0, fDuration, 1.0, 1.0, -1 )
}

get_victims( id, g_sBuffer[ 2048 ] )
{
	new iVictim, iLen, iFound = 0

	g_sBuffer[ 0 ] = 0
	iLen = format( g_sBuffer, 2047, "%L:^n", id, "VICTIMS" )

	for( iVictim = 1; iVictim <= MaxClients; iVictim++ )
	{
		if( g_VictimList[ id ][ iVictim ][ V_DMG ] )
		{
			iFound = 1

			if( !g_VictimList[ id ][ iVictim ][ V_WEAPON ] )
			{
				iLen += format( g_sBuffer[ iLen ], 2047 - iLen, "%s -- %d %L / %d %L^n", g_VictimList[ id ][ iVictim ][ V_NAME ], g_VictimList[ id ][ iVictim ][ V_HITS ], id, "HIT_S", g_VictimList[ id ][ iVictim ][ V_DMG ], id, "DMG" )
			}
			else
			{
				iLen += format( g_sBuffer[ iLen ], 2047 - iLen, "%s -- %d %L / %d %L / %s^n", g_VictimList[ id ][ iVictim ][ V_NAME ], g_VictimList[ id ][ iVictim ][ V_HITS ], id, "HIT_S", g_VictimList[ id ][ iVictim ][ V_DMG ], id, "DMG", g_VictimList[ id ][ iVictim ][ V_WEAPON ] )
			}
		}
	}

	if( !iFound )
	{
		g_sBuffer[ 0 ] = 0
	}

	return iFound;
}

get_attackers( id, g_sBuffer[ 2048 ] )
{
	new iAttacker, iLen, iFound = 0
	g_sBuffer[ 0 ] = 0

	iLen = format( g_sBuffer, 2047, "%L:^n", id, "ATTACKERS" )

	for( iAttacker = 1; iAttacker <= MaxClients; iAttacker++ )
	{
		if( g_AttackerList[ id ][ iAttacker ][ V_DMG ] )
		{
			iFound = 1

			if( !g_AttackerList[ id ][ iAttacker ][ V_WEAPON ] )
			{
				iLen += format( g_sBuffer[ iLen ], 2047 - iLen, "%s -- %d %L / %d %L^n", g_AttackerList[ id ][ iAttacker ][ V_NAME ], g_AttackerList[ id ][ iAttacker ][ V_HITS ], id, "HIT_S", g_AttackerList[ id ][ iAttacker ][ V_DMG ], id, "DMG" )
			}
			else
			{
				iLen += format( g_sBuffer[ iLen ], 2047 - iLen, "%s -- %d %L / %d %L / %s^n", g_AttackerList[ id ][ iAttacker ][ V_NAME ], g_AttackerList[ id ][ iAttacker ][ V_HITS ], id, "HIT_S", g_AttackerList[ id ][ iAttacker ][ V_DMG ], id, "DMG", g_AttackerList[ id ][ iAttacker ][ V_WEAPON ] )
			}
		}
	}

	if( !iFound )
	{
		g_sBuffer[ 0 ] = 0
	}

	return iFound;
}

get_loguser_index( )
{
	new szLogUser[ 80 ], szName[ 32 ]

	read_logargv( 0, szLogUser, charsmax( szLogUser ) )
	parse_loguser( szLogUser, szName, charsmax( szName ) )

	return get_user_index( szName );
}

public QueryHandle( FailState, Handle:hQuery, Error[ ], Errcode, Data[ ], DataSize )
{
	if( FailState != TQUERY_SUCCESS && get_pcvar_num( pcvar_debug ) )
	{
		log_amx( "SQL Error #%d - %s", Errcode, Error )
	}
}

stock register_saycmd( const command[ ], const function[ ], flags, const info[ ] )
{
	new temp[ 64 ]
	formatex( temp, 63, "say /%s", command )
	register_clcmd( temp, function, flags, info )
	formatex( temp, 63, "say_team /%s", command )
	register_clcmd( temp, function, flags, info )
}
