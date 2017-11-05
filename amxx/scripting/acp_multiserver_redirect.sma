/*
*********************************************************************************
*
*   Multiserver Redirect v1.6 (updated)
*   Last Update: 14/04/2013
*   © a114 Team
*
*   Original plugin by Sho0ter: http://amx-x.ru/viewtopic.php?f=12&t=3033
*   Modified by Hafner: http://www.a114games.com/community/threads/1657/
*
*
*********************************************************************************
*/

#include <amxmodx>
#include <amxmisc> 
#include <sqlx>

#define ACP_GENERAL

#if defined ACP_GENERAL
	#include <acp>
#endif

#define PLUGIN "Multiserver Redirect"
#define VERSION "1.6"
#define AUTHOR "Sho0ter & Modified by Hafner"

#define CONNECT_CHECK 0
#define MENU_CHECK 1

#define REDIRECT_FALSE 0
#define REDIRECT_IGNORE 1
#define REDIRECT_TRUE 2

#define UPDATE_TASK 21387

#define column(%1) SQL_FieldNameToNum(query, %1)

new Handle:tuple

new Array:retries_ids
new Array:retries_servers

new Array:server_name
new Array:server_ip
new Array:server_map
new Array:server_players
new Array:server_maxplayers
new Array:server_viewplayers
new Array:server_admins
new Array:server_password
new Array:server_online
new Array:server_slots

new query_cache[2048]

new menu_position[33]
new sub_data[33]

new current_server, g_serverID, g_redirect

new pcvar_sql_host, pcvar_sql_user, pcvar_sql_pass, pcvar_sql_db
new pcvar_join, pcvar_retry, pcvar_update, pcvar_admins, pcvar_flag, pcvar_ip, pcvar_erase, pcvar_hide

new maxplayers, admins, g_serverip[22], g_password[32], g_reservation, g_hideslots

public plugin_init()
{
	register_plugin(PLUGIN, VERSION, AUTHOR)
	
	register_dictionary("acp_multiserver_redirect.txt")
	
	server_name = ArrayCreate(64)
	server_ip = ArrayCreate(32)
	server_map = ArrayCreate(32)
	server_password = ArrayCreate(32)
	server_players = ArrayCreate(1)
	server_maxplayers = ArrayCreate(1)
	server_viewplayers = ArrayCreate(1)
	server_admins = ArrayCreate(1)
	server_online = ArrayCreate(1)
	server_slots = ArrayCreate(1)
	
	retries_ids = ArrayCreate(1)
	retries_servers = ArrayCreate(1)

	pcvar_sql_host = register_cvar("acp_sql_host", "localhost")
	pcvar_sql_user = register_cvar("acp_sql_user", "root")
	pcvar_sql_pass = register_cvar("acp_sql_pass", "")
	pcvar_sql_db = register_cvar("acp_sql_db", "amx")

	pcvar_join = register_cvar("msr_join", "1")
	pcvar_retry = register_cvar("msr_retry", "0")
	pcvar_admins = register_cvar("msr_show_admins", "0")
	pcvar_update = register_cvar("msr_updaterate", "1.0")
	pcvar_flag = register_cvar("msr_admin_flag", "b")
	pcvar_ip = register_cvar("msr_serverip", "")
	pcvar_erase = register_cvar("msr_erase_hostname", "")
	pcvar_hide = register_cvar("msr_hide_offline", "1")

	register_clcmd("say /server", "cmd_server")
	register_clcmd("say_team /server", "cmd_server")
	
	register_menucmd(register_menuid("redirect menu"), 1023, "main_menu")
	register_menucmd(register_menuid("redirect sub menu"), 1023, "sub_menu")
	
	maxplayers = get_maxplayers()
	
	return PLUGIN_CONTINUE
}

public plugin_cfg()
{
	new configsDir[64]
	get_configsdir(configsDir, 63)

	server_cmd("exec %s/acp/sql.cfg", configsDir)	
	server_cmd("exec %s/acp/multiserver_redirect.cfg", configsDir)

	get_pcvar_string(get_cvar_pointer("sv_password"), g_password, 31)

	g_reservation = get_cvar_num("amx_reservation")
	if(get_cvar_num("amx_hideslots") > 0)
	{
		g_hideslots = g_reservation
	}
	else
	{
		g_hideslots = 0
	}
	
	get_pcvar_string(pcvar_ip, g_serverip, 21)
	if(!strlen(g_serverip))
	{
		get_user_ip(0, g_serverip, 21)
	}

	#if !defined ACP_GENERAL
		set_task(1.0, "sql_init")
	#endif
	
	return PLUGIN_CONTINUE
}

#if defined ACP_GENERAL
	public acp_sql_initialized(Handle:sqlTuple)
	{
		if( tuple != Empty_Handle )
		{
			log_amx("[MSR] DB Info Tuple from acp_general initialized twice!")
			return PLUGIN_HANDLED
		}
		
		tuple = sqlTuple
	
		if ( tuple == Empty_Handle )
		{
			log_amx("[MSR] DB Info Tuple from acp_multiserver_redirect is empty! Trying to get a valid one")
	
			new host[32], user[32], pass[32], db[32]
			get_pcvar_string(pcvar_sql_host, host, 31);
			get_pcvar_string(pcvar_sql_user, user, 31);
			get_pcvar_string(pcvar_sql_pass, pass, 31);
			get_pcvar_string(pcvar_sql_db, db, 31);
			
			tuple = SQL_MakeDbTuple(host, user, pass, db)
		}

		g_serverID = acp_get_server_id()
		if( g_serverID )
		{
			g_redirect = true
			set_task(get_pcvar_float(pcvar_update), "update_data", UPDATE_TASK, _, _, "b")
		}
		else
		{
			log_amx("[MSR] %L", LANG_SERVER, "MSR_NO_SERVERINFO")
		}
	
		return PLUGIN_HANDLED
	}

#else

	public sql_init()
	{
		new host[64], user[64], pass[64], dbname[64]
		
		get_pcvar_string(pcvar_sql_host,host,31);
		get_pcvar_string(pcvar_sql_user,user,31);
		get_pcvar_string(pcvar_sql_pass,pass,31);
		get_pcvar_string(pcvar_sql_db,dbname,31);
	
		tuple = SQL_MakeDbTuple(host, user, pass, dbname)
	
		new error[1024], errornum
		
		new Handle:connect = SQL_Connect(tuple, errornum, error, 1023)
		if(connect == Empty_Handle)
		{
			log_amx("[MSR] %L", LANG_SERVER, "MSR_SQL_CANT_CON", errornum, error)
		}
		else
		{
			new Handle:query
		
			query = SQL_PrepareQuery(connect, "SELECT `id` FROM `acp_servers` WHERE address = '%s' LIMIT 1", g_serverip)
		
			if (!SQL_Execute(query))
			{
				SQL_QueryError(query, error, 127)
				log_amx("[MSR] %L", LANG_SERVER, "MSR_CANT_LOAD_SERVERS", error)
			}
			else if (!SQL_NumResults(query))
			{
				log_amx("[MSR] %L", LANG_SERVER, "MSR_NO_SERVERINFO")
			}
			else
			{		
				g_serverID = SQL_ReadResult(query, 0);
				g_redirect = true
				set_task(get_pcvar_float(pcvar_update), "update_data", UPDATE_TASK, _, _, "b")
			}
	
			SQL_FreeHandle(connect)
		}
	}
#endif

public client_authorized(id)
{
	new redirect_server[32], redirect_password[32]
	switch(is_can_redirect(id, _, redirect_server, 31, redirect_password, 31, CONNECT_CHECK))
	{
		case REDIRECT_FALSE: server_cmd("kick #%d  %L", get_user_userid(id), id, "MSR_NO_FREE_SLOTS")
		case REDIRECT_IGNORE: return PLUGIN_CONTINUE
		case REDIRECT_TRUE: 
		{
			if(strlen(redirect_password))
			{
				return client_cmd(id, "^"connect^"%s;^"password^"%s", redirect_server, redirect_password)
			}
			return client_cmd(id,"^"connect^"%s", redirect_server)
		}
	}
	return PLUGIN_CONTINUE
}

public client_putinserver(id)
{
	if(get_user_flags(id) & ADMIN_IMMUNITY)
	{
		admins++
	}
	return PLUGIN_CONTINUE
}

public client_disconnected(id)
{
	if(get_user_flags(id) & ADMIN_IMMUNITY)
	{
		admins--
	}
	
	retry_check_and_delete(id)
	
	return PLUGIN_CONTINUE
}

public cmd_server(id)
{
	if( g_redirect )
		show_main_menu(id, menu_position[id] = 0)

	return PLUGIN_CONTINUE
}

public show_main_menu(id, position)
{
	if(position < 0)
	{
		return PLUGIN_HANDLED
	}
	
	new menu_body[1024]
	new menu_start = position * 7
	new menu_end = menu_start + 7
	new menu_key = 1
	new menu_keys = 0
	new menu_len
	
	if(menu_end > ArraySize(server_name))
	{
		menu_end = ArraySize(server_name)
	}
	
	menu_len = format(menu_body, 1023, "\yRedirect to the server:^n^n")
	
	new temp2[64], temp[32]
	
	for(new i = menu_start; i < menu_end; i++)
	{
		if(is_can_redirect(id, i, _, _, _, _, MENU_CHECK))
		{
			menu_keys |= (1 << (menu_key - 1))
			ArrayGetString(server_name, i, temp2, 64)
			
			menu_len += format(menu_body[menu_len], 1023 - menu_len, "\r%d. \w%s ", menu_key, temp2)
			ArrayGetString(server_map, i, temp, 31)
			
			menu_len += format(menu_body[menu_len], 1023 - menu_len, "\y[\w%s\y] (\w%d/%d\y)", temp, ArrayGetCell(server_players, i), ArrayGetCell(server_viewplayers, i))
			
			if((get_user_flags(id) & ADMIN_IMMUNITY) && get_pcvar_num(pcvar_admins))
			{
				menu_len += format(menu_body[menu_len], 1023 - menu_len, " \y<= \w%d A", ArrayGetCell(server_admins, i))
			}
			
			menu_len += format(menu_body[menu_len], 1023 - menu_len, "^n")
		}
		else
		{
			ArrayGetString(server_name, i, temp2, 64)
			menu_len += format(menu_body[menu_len], 1023 - menu_len, "\r%d. \d%s ", menu_key, temp2)
			
			if(!ArrayGetCell(server_online, i))
			{
				menu_len += format(menu_body[menu_len], 1023 - menu_len, "(\r%L\d)^n", id, "MSR_DOWN")
			}
			else if(i == current_server)
			{
				menu_len += format(menu_body[menu_len], 1023 - menu_len, "(\y%L\d)^n", id, "MSR_CURRENT")
			}
			else
			{
				ArrayGetString(server_map, i, temp, 31)
				menu_len += format(menu_body[menu_len], 1023 - menu_len, "[\w%s\d] (%s%d/%d\d)", temp, "\r", ArrayGetCell(server_players, i), ArrayGetCell(server_viewplayers, i))
				
				if((get_user_flags(id) & ADMIN_IMMUNITY) && get_pcvar_num(pcvar_admins))
				{
					menu_len += format(menu_body[menu_len], 1023 - menu_len, " \d<= \w%d A", ArrayGetCell(server_admins, i))
				}
				
				menu_len += format(menu_body[menu_len], 1023 - menu_len, "^n")
			}
		}
		menu_key++
	}
	
	menu_keys |= (1 << 7)
	
	if(menu_end != ArraySize(server_name))
	{
		menu_keys |= (1 << 8)
	}
	
	menu_keys |= (1 << 9)
	menu_len += format(menu_body[menu_len], 1023 - menu_len, "^n\r8. \w%L^n^n\r9. %s%L^n\r0. \w%L", id, "MSR_REFRESH", (menu_end == ArraySize(server_name)) ? "\d" : "\w", id, "MSR_MORE", id, position ? "MSR_BACK" : "MSR_EXIT")

	return show_menu(id, menu_keys, menu_body, -1, "redirect menu")
}

public main_menu(id, key)
{
	switch(key)
	{
		case 7: show_main_menu(id, menu_position[id])
		case 8: show_main_menu(id, ++menu_position[id])
		case 9: show_main_menu(id, --menu_position[id])
		default:
		{
			new menu_choosed = (menu_position[id] * 7) + key
			
			new redirect_server[32], redirect_password[32]
			
			if(is_can_redirect(id, menu_choosed, redirect_server, 31, redirect_password, 31, MENU_CHECK))
			{
				if(strlen(redirect_password))
				{
					return client_cmd(id, "^"connect^"%s;^"password^"%s", redirect_server, redirect_password)
				}
				else
				{
					return client_cmd(id,"^"connect^"%s", redirect_server)
				}
			}
			else if(get_pcvar_num(pcvar_retry))
			{
				return show_sub_menu(id, menu_choosed)
			}
			else
			{
				client_print(id, print_chat, "[Multiserver Redirect] %L", id, "MSR_CHANGED")
			}
		}
	}
	
	return PLUGIN_HANDLED
}

public show_sub_menu(id, server_id)
{
	sub_data[id] = server_id
	
	new body[512], len, keys
	len = format(body[len], 511 - len, "\r%L\w^n", id, "MRS_SUB_HEADER")
	
	new is_retry = is_user_retry(id)

	keys |= (1<<0)|(1<<9)
	
	len += format(body[len], 511 - len, "%s%L^n^n\r1. \w%L", is_retry ? "\y" : "\d", id, is_retry ? "MSR_IN_RETRY" : "MSR_NO_RETRY", id, is_retry ? "MSR_OFF_RETRY" : "MSR_ON_RETRY")
	
	len += format(body[len], 511 - len, "^n^n\r0. \w%L", id, "MSR_BACK")
	
	return show_menu(id, keys, body, -1, "redirect sub menu")
}

public sub_menu(id, key)
{
	if(key == 9) return show_main_menu(id, menu_position[id])
	
	if(is_user_retry(id))
	{
		retry_check_and_delete(id)
	}
	else
	{
		retry_add(id, sub_data[id])
	}
	
	return show_sub_menu(id, sub_data[id])
}

public update_data()
{
	new map[32], current_players = get_playersnum(1)

	get_mapname(map, 31)	

	new sql_cond[44]
	if(get_pcvar_num(pcvar_hide))
		copy(sql_cond, sizeof(sql_cond) - 1, " AND `current_online` = '1'"); 
	
	new len = format(query_cache, 2047, "SELECT hostname, address, server_id, current_map, current_pwd, current_players, \
		current_maxplayers, current_viewplayers, current_admins, current_reserved_slots, current_timestamp, current_online \
		FROM `acp_servers_redirect` LEFT JOIN `acp_servers` ON acp_servers.id = acp_servers_redirect.server_id \
		WHERE acp_servers.opt_redirect = '1'%s ORDER BY acp_servers.rating DESC;", sql_cond)
	len += format(query_cache[len], 2047 - len, "INSERT INTO `acp_servers_redirect` (`server_id`,`current_map`,`current_pwd`,`current_players`,`current_maxplayers`,`current_viewplayers`,\
		`current_admins`,`current_reserved_slots`,`current_timestamp`,`current_online`) \
		VALUES ('%d','%s','%s','%d','%d','%d','%d','%d',UNIX_TIMESTAMP(NOW()),'1')", g_serverID, map,
		g_password, current_players, maxplayers, maxplayers - g_hideslots, admins, g_reservation)
	len += format(query_cache[len], 2047 - len, "	ON DUPLICATE KEY UPDATE `current_map` = '%s', `current_pwd` = '%s', `current_players` = '%d', \
		`current_maxplayers` = '%d', `current_viewplayers` = '%d', `current_admins` = '%d', `current_reserved_slots` = '%d', `current_timestamp` = UNIX_TIMESTAMP(NOW()), `current_online` = '1';",
		map, g_password, current_players, maxplayers, maxplayers - g_hideslots, admins, g_reservation)
	len += format(query_cache[len], 2047 - len, "UPDATE `acp_servers_redirect` SET `current_online` = '0' WHERE (`current_timestamp` + %d) < UNIX_TIMESTAMP(NOW());", floatround(get_pcvar_float(pcvar_update)) * 4)

	return SQL_ThreadQuery(tuple, "update_data_post", query_cache)
}

public update_data_post(failstate, Handle:query, const error[], errornum, const qdata[], size, Float:queuetime)
{
	if(failstate)
	{
		return SQL_ThreadError(query, error, errornum, failstate)
	}
	
	if(!task_exists(UPDATE_TASK))
	{
		return SQL_FreeHandle(query)
	}
	
	ArrayClear(server_name)
	ArrayClear(server_ip)
	ArrayClear(server_map)
	ArrayClear(server_players)
	ArrayClear(server_maxplayers)
	ArrayClear(server_viewplayers)
	ArrayClear(server_admins)
	ArrayClear(server_password)
	ArrayClear(server_online)
	ArrayClear(server_slots)
	
	new curpos, temp[64], erase[44]
	get_pcvar_string(pcvar_erase, erase, 43)

	new qcolIP = column("address")
	new qcolHostname = column("hostname")
	new qcolMap = column("current_map")
	new qcolPass = column("current_pwd")
	new qcolPl = column("current_players")
	new qcolPlmax = column("current_maxplayers")
	new qcolPlview = column("current_viewplayers")
	new qcolAdmins = column("current_admins")
	new qcolSlots = column("current_reserved_slots")
	new qcolOnline = column("current_online")
	
	while(SQL_MoreResults(query))
	{
		SQL_ReadResult(query, qcolHostname, temp, 63)
		if(strlen(erase)) replace_all(temp, sizeof(temp) - 1, erase, "")		
		ArrayPushString(server_name, temp)
		SQL_ReadResult(query, qcolIP, temp, 31)
		if(equal(temp,g_serverip))
		{
			current_server = curpos
		}
		ArrayPushString(server_ip, temp)
		SQL_ReadResult(query, qcolMap, temp, 31)
		ArrayPushString(server_map, temp)
		SQL_ReadResult(query, qcolPass, temp)
		ArrayPushString(server_password, temp)
		ArrayPushCell(server_players, SQL_ReadResult(query, qcolPl))
		ArrayPushCell(server_maxplayers, SQL_ReadResult(query, qcolPlmax))
		ArrayPushCell(server_viewplayers, SQL_ReadResult(query, qcolPlview))
		ArrayPushCell(server_admins, SQL_ReadResult(query, qcolAdmins))
		ArrayPushCell(server_slots, SQL_ReadResult(query, qcolSlots))
		ArrayPushCell(server_online, SQL_ReadResult(query, qcolOnline))
		
		curpos++
		
		SQL_NextRow(query)
	}
	
	SQL_FreeHandle(query)
	
	new arrsize = ArraySize(retries_ids)
	
	for(new i; i < arrsize; i++)
	{
		new id = ArrayGetCell(retries_ids, i)
		new redirect_server[32], redirect_password[32]
		if(is_can_redirect(id, ArrayGetCell(retries_servers, i), redirect_server, 31, redirect_password, 31, MENU_CHECK))
		{
			if(strlen(redirect_password))
			{
				client_cmd(id, "^"connect^"%s;^"password^"%s", redirect_server, redirect_password)
			}
			else
			{
				client_cmd(id,"^"connect^"%s", redirect_server)
			}

			new val = ArrayGetCell(server_players, ArrayGetCell(retries_servers, i))			
			ArraySetCell(server_players, ArrayGetCell(retries_servers, i),  val + 1)
		}
	}
	
	return PLUGIN_CONTINUE
}

#if defined ACP_GENERAL

public acp_endmap_func()
{

#else

public plugin_end()
{
	if(tuple != Empty_Handle)
	{
		SQL_FreeHandle(tuple)
	}

#endif
	
	remove_task(UPDATE_TASK)
	
	ArrayDestroy(server_name)
	ArrayDestroy(server_ip)
	ArrayDestroy(server_map)
	ArrayDestroy(server_players)
	ArrayDestroy(server_maxplayers)
	ArrayDestroy(server_viewplayers)
	ArrayDestroy(server_admins)
	ArrayDestroy(server_password)
	ArrayDestroy(server_online)
	ArrayDestroy(server_slots)
	
	ArrayClear(retries_ids)
	ArrayClear(retries_servers)
}

stock is_can_redirect(id, server_id = 0, output[] = "", len = 0, output2[] = "", len2 = 0, type)
{
	switch(type)
	{
		case CONNECT_CHECK:
		{
			if(get_playersnum(1) <= (maxplayers - g_reservation) || (is_have_slot(id) && get_playersnum(1) <= maxplayers) || !get_pcvar_num(pcvar_join)) return REDIRECT_IGNORE

			for(server_id = 0; server_id < ArraySize(server_name); server_id++)
			{
				if(server_id == current_server) continue;

				new slots = ArrayGetCell(server_slots, server_id), pl = ArrayGetCell(server_maxplayers, server_id)
				if(((ArrayGetCell(server_players, server_id) < (pl-slots)) || is_have_slot(id) && (ArrayGetCell(server_players, server_id) < pl)) && ArrayGetCell(server_online, server_id))
				{
					ArrayGetString(server_ip, server_id, output, len)
					ArrayGetString(server_password, server_id, output2, len2)
					return REDIRECT_TRUE
				}
			}

			return REDIRECT_FALSE
		}
		case MENU_CHECK:
		{
			new slots = ArrayGetCell(server_slots, server_id), pl = ArrayGetCell(server_maxplayers, server_id)
			if(((ArrayGetCell(server_players, server_id) < (pl-slots)) || is_have_slot(id) && (ArrayGetCell(server_players, server_id) < pl)) && ArrayGetCell(server_online, server_id) && server_id != current_server)
			{
				ArrayGetString(server_ip, server_id, output, len)
				ArrayGetString(server_password, server_id, output2, len2)
				return REDIRECT_TRUE
			}
			return REDIRECT_FALSE
		}
	}
	return REDIRECT_IGNORE
}

stock is_have_slot(id)
{
	new ip[32]
	get_user_ip(id, ip, 31, 1)
	return (get_user_flags(id) & get_slot_flag() && g_reservation)
}

stock SQL_ThreadError(Handle:query, const error[], errornum, failstate)
{
	log_amx("[MSR] %L", LANG_SERVER, "MSR_THREADED_ERROR", errornum, error)
	
	new pquery[1024]
	SQL_GetQueryString(query, pquery, 1023)

	log_amx("[MSR] %L", LANG_SERVER, "MSR_QUERY_STRING", pquery)	
	
	if(failstate == TQUERY_CONNECT_FAILED)
	{
		log_amx("[MSR] %L", LANG_SERVER, "MSR_CONNECTION_FAILED")
	}
	else if(failstate == TQUERY_QUERY_FAILED)
	{
		log_amx("[MSR] %L", LANG_SERVER, "MSR_QUERY_FAILED")
	}
	
	return SQL_FreeHandle(query)
}

stock get_slot_flag()
{
	new str[32]
	get_pcvar_string(pcvar_flag, str, 31)
	return read_flags(str)
}

stock retry_check_and_delete(id)
{
	new arrsize = ArraySize(retries_ids)
	for(new i; i < arrsize; i++)
	{
		new tid = ArrayGetCell(retries_ids, i)
		if(id == tid)
		{
			ArrayDeleteItem(retries_ids, i)
			ArrayDeleteItem(retries_servers, i)
			return 1
		}
	}
	return 0
}

stock retry_add(id, server)
{
	ArrayPushCell(retries_ids, id)
	ArrayPushCell(retries_servers, server)
	return 1
}

stock sql_safe(dest[], len)
{
	replace_all(dest, len, "\\", "\\\\")
	replace_all(dest, len, "\0", "\\0")
	replace_all(dest, len, "\n", "\\n")
	replace_all(dest, len, "\r", "\\r")
	replace_all(dest, len, "\x1a", "\Z")
	replace_all(dest, len, "'", "\'")
	replace_all(dest, len, "^"", "\^"")
	
	return 1
}

stock is_user_retry(id)
{
	new arrsize = ArraySize(retries_ids)
	for(new i; i < arrsize; i++)
	{
		new tid = ArrayGetCell(retries_ids, i)
		if(id == tid)
		{
			return 1
		}
	}
	return 0
}