/*
*********************************************************************************
*
*   Chat Control v3.6
*   Last Update: 06/05/2013
*   © a114 Team
*
*   by Hafner
*   Support: http://www.a114games.com/community/threads/1634/
*
*
*********************************************************************************
*/

#include <amxmodx> 
#include <amxmisc> 
#include <regex>
#include <sqlx>

//#define UTF_QUERY "SET NAMES utf8;"
#define ACP_GENERAL

#if defined ACP_GENERAL
	#include <acp>
#endif

#define PLUGIN_NAME	"[ACP] Chat Control"
#define PLUGIN_VERSION	"3.6"
#define PLUGIN_AUTHOR	"Hafner"

#define REGEX_STEAMID_PATTERN "^^STEAM_(0|1):(0|1):\d+$"

#define CC_ORIGINAL 0
#define CC_TRANSLIT 1

new Regex:g_SteamID_pattern
#define IsValidAuthid(%1) (regex_match_c(%1, g_SteamID_pattern, g_regex_return) > 0)

enum _:MsgData
{ 
	MSG_TEXT[ 64 ], 
	MSG_LENGTH[ 11 ], 
	MSG_REASON[ 44 ] 
};

new const g_team_names[][] = 
{ 
	"Spectator", 
	"Terrorist", 
	"Counter-Terrorist", 
	"Spectator" 
}; 

new const g_action_list[][] = 
{
	"whitelist",
	"hidelist",
	"banlist", 
	"kicklist", 
	"warnlist"
};

new Float:g_Flooding[33], g_Flood[33]

new g_msgid_SayText, gmsgFade, g_max_clients, g_warning_counter[33], bool:g_player_cfg_clean[33]
new g_player_reason[44], g_ban_length[11], bool:g_player_translit[33], translit_field[10]

new Trie:ActionData
new Array:NoSaveWords
new Trie:TranslitData

#if !defined ACP_GENERAL
	new Handle:sql
#endif
new Handle:info
new error[128]

new default_cfg[100][50], default_cfg_num

new Regex:zRes, g_regex_return, g_serverip[22], trigger[16]

new pcvar_sql_host, pcvar_sql_user, pcvar_sql_pass, pcvar_sql_db
new pcvar_floodtime, pcvar_adminview, pcvar_bantype, pcvar_bantime, pcvar_warn_count, pcvar_warn_action, pcvar_vipaccess
new pcvar_bancmd, pcvar_adminlisten, pcvar_adminlisten_type, pcvar_immunity, pcvar_savelogs, pcvar_allchat, pcvar_serverip
new pcvar_translit, pcvar_translit_field, pcvar_translit_def, pcvar_translit_access, pcvar_translit_trigger, pcvar_cleancfg

public plugin_init() 
{ 
	register_plugin ( PLUGIN_NAME, PLUGIN_VERSION, PLUGIN_AUTHOR )
	
	register_dictionary("acp_chatcontrol.txt")

	pcvar_bancmd = register_cvar("acp_cc_bancommand", "amx_ban %userid% %time% %reason%")
	pcvar_bantype = register_cvar("acp_cc_bantype", "0")
	pcvar_bantime = register_cvar("acp_cc_bantime", "1440")
	pcvar_warn_count = register_cvar("acp_cc_warn_count", "3")
	pcvar_warn_action = register_cvar("acp_cc_warn_action", "0")
	pcvar_vipaccess = register_cvar("acp_cc_vipaccess", "t")
	pcvar_savelogs = register_cvar("acp_cc_savelogs", "1")
	pcvar_immunity = register_cvar("acp_cc_immunity", "a")
	pcvar_adminlisten = register_cvar("acp_cc_adminlisten", "a")
	pcvar_adminlisten_type = register_cvar("acp_cc_adminlisten_type", "0")
	pcvar_floodtime = register_cvar("acp_cc_floodtime", "0.75")
	pcvar_adminview = register_cvar("acp_cc_adminview", "i")
	pcvar_allchat = register_cvar("acp_cc_allchat", "0")
	pcvar_serverip = register_cvar("acp_cc_serverip", "")
	pcvar_cleancfg = register_cvar("acp_cc_clean_config", "1")

	pcvar_translit = register_cvar("acp_cc_translit", "1")
	pcvar_translit_field = register_cvar("acp_cc_translit_field", "translit")
	pcvar_translit_def = register_cvar("acp_cc_translit_default", "0")
	pcvar_translit_access = register_cvar("acp_cc_translit_access", "")
	pcvar_translit_trigger = register_cvar("acp_cc_translit_trigger", "/lang")
	
	pcvar_sql_host = register_cvar("acp_sql_host", "localhost")
	pcvar_sql_user = register_cvar("acp_sql_user", "root")
	pcvar_sql_pass = register_cvar("acp_sql_pass", "")
	pcvar_sql_db = register_cvar("acp_sql_db", "amx")
	
	register_clcmd("say", "CmdSay")
	register_clcmd("say_team", "CmdTeamSay")
	register_clcmd("amx_chat", "cmdAdminChat")
	
	g_msgid_SayText = get_user_msgid("SayText"); 
	gmsgFade = get_user_msgid("ScreenFade");
	g_max_clients = get_maxplayers(); 
	
	new regerror[2];
	g_SteamID_pattern = regex_compile(REGEX_STEAMID_PATTERN, g_regex_return, regerror, sizeof(regerror) - 1)
}

public plugin_cfg()
{
	new configsDir[64]
	get_configsdir(configsDir, 63)

	server_cmd("exec %s/acp/sql.cfg", configsDir)	
	server_cmd("exec %s/acp/chatcontrol.cfg", configsDir)

	ActionData = TrieCreate();
	NoSaveWords = ArrayCreate(44);
	TranslitData = TrieCreate();

	get_pcvar_string(pcvar_translit_trigger, trigger, 15)
	get_pcvar_string(pcvar_translit_field, translit_field, 9)

	new line, len, sFile[128], sLine[50]

	if( get_pcvar_num(pcvar_translit) )
	{
		new symb_Original[8], symb_Translate[8], www
		formatex(sFile, 127, "%s/acp/chatcontrol_translit.ini", configsDir)
		if( file_exists(sFile) )
		{
			while( (line = read_file(sFile, line, sLine, 49, len)) )
			{
				if( len == 0 || equal (sLine, ";", 1) )
					continue

				strtok(sLine, symb_Original, 7, symb_Translate, 7, ' ')
				TrieSetString(TranslitData, symb_Original, symb_Translate)
				www++;
			}
		}
	}

	if( get_pcvar_num(pcvar_cleancfg) )
	{
		formatex(sFile, 127, "%s/acp/chatcontrol_clean_cfg.ini", configsDir)
		if( file_exists(sFile) )
		{
			while( (line = read_file(sFile, line, sLine, 49, len)) )
			{
				if( len == 0 || equal (sLine, ";", 1) )
					continue
	
				copy( default_cfg[default_cfg_num], 49, sLine )
				default_cfg_num++
			}
		}
	}

	#if !defined ACP_GENERAL
		set_task(1.0, "sql_init")
	#endif
} 

#if defined ACP_GENERAL
public acp_sql_initialized(Handle:sqlTuple)
{
	if( info != Empty_Handle )
	{
		log_amx("[ChatControl] DB Info Tuple from acp_general initialized twice!")
		return PLUGIN_HANDLED
	}
	
	info = sqlTuple

	if( info == Empty_Handle )
	{
		log_amx("[ChatControl] DB Info Tuple from acp_chatcontrol is empty! Trying to get a valid one")

		new host[32], user[32], pass[32], db[32]
		get_pcvar_string(pcvar_sql_host, host, 31);
		get_pcvar_string(pcvar_sql_user, user, 31);
		get_pcvar_string(pcvar_sql_pass, pass, 31);
		get_pcvar_string(pcvar_sql_db, db, 31);
		
		info = SQL_MakeDbTuple(host, user, pass, db)
	}

	ServerInitialized()

	return PLUGIN_CONTINUE
}

public acp_endmap_func()
{

#else

public sql_init()
{
	new host[32], user[32], pass[32], dbname[32];
	get_pcvar_string(pcvar_sql_host,host,31);
	get_pcvar_string(pcvar_sql_user,user,31);
	get_pcvar_string(pcvar_sql_pass,pass,31);
	get_pcvar_string(pcvar_sql_db,dbname,31);

	info = SQL_MakeDbTuple(host,user,pass,dbname);

	ServerInitialized()
}

public plugin_end()
{
	if(info != Empty_Handle)
	{
		SQL_FreeHandle(info)
	}

#endif

	TrieDestroy(ActionData);
	ArrayDestroy(NoSaveWords);
	TrieDestroy(TranslitData)
}

public ServerInitialized()
{
	get_pcvar_string(pcvar_serverip, g_serverip, 21)
	if( !strlen(g_serverip) )
	{
		get_user_ip(0, g_serverip, 21)
	}

#if defined ACP_GENERAL

	if( !acp_get_server_id() )
	{
		log_amx("[ChatControl] No of this server to the database!")
	}
	else
	{

#else

	new errno, query[512]

	if(info) sql = SQL_Connect(info, errno, error, 127);

	if( sql == Empty_Handle )
	{
		log_amx("[ChatControl] %L", LANG_SERVER, "CC_SQL_ERROR", error)
	}
	else
	{
		formatex(query, 511, "SELECT `id` FROM `acp_servers` WHERE (address = '%s') LIMIT 1", g_serverip)
		SQL_FreeHandle(sql)
		SQL_ThreadQuery(info, "ServerInitialized_Post", query)
	}
}

public ServerInitialized_Post(FailState,Handle:query,error[],errcode,Data[],DataSize) 
{ 
    	if( FailState != TQUERY_SUCCESS )
	{
		log_amx("[ChatControl] %L", LANG_SERVER, "CC_SQL_ERROR", error)
	}
	else if( !SQL_NumResults(query) )
	{
		new servername[100], query_insert[512]	
		get_cvar_string("hostname", servername, 99)
	
		formatex(query_insert, 511, "INSERT INTO `acp_servers` (`address`, `hostname`) VALUES ('%s','%s')", g_serverip, servername)	
		SQL_ThreadQuery(info, "QueryHandle", query_insert)
	}
	else
	{

#endif

		LoadNoSaveWords()		
		LoadPatterns()
	}
}

public LoadPatterns()
{
	TrieClear(ActionData);

	new query[512]

	formatex(query, 511, "SELECT `action`,`pattern`,`reason`,`length` FROM `acp_chat_patterns`")
	SQL_ThreadQuery(info, "LoadPatterns_Post", query)

	return PLUGIN_HANDLED
}

public LoadPatterns_Post(FailState,Handle:query,error[],errcode,Data[],DataSize)
{
    	if( FailState != TQUERY_SUCCESS )
	{
		log_amx("[ChatControl] %L", LANG_SERVER, "CC_CANT_LOAD", error)
	}
	else if( !SQL_NumResults(query) )
	{
		server_print("[ChatControl] %L", LANG_SERVER, "CC_NO_PATTERNS")
	}
	else
	{
		new patternsCount

		new zAction = SQL_FieldNameToNum(query, "action")
		new zMsg = SQL_FieldNameToNum(query, "pattern")
		new zReason = SQL_FieldNameToNum(query, "reason")
		new zLength = SQL_FieldNameToNum(query, "length")

		new data[ MsgData ], g_zAction;

		while (SQL_MoreResults(query))
		{
			g_zAction = SQL_ReadResult(query, zAction);
			SQL_ReadResult(query, zLength, data[ MSG_LENGTH ], 10);
			SQL_ReadResult(query, zMsg, data[ MSG_TEXT ], 63);
			SQL_ReadResult(query, zReason, data[ MSG_REASON ], 43);

			new Array:patternsData

			if(!TrieGetCell(ActionData,g_action_list[g_zAction],patternsData))
			{
				patternsData = ArrayCreate(MsgData);
			}

			ArrayPushArray(patternsData,data)
			TrieSetCell(ActionData,g_action_list[g_zAction],patternsData);

			patternsCount++;
			SQL_NextRow(query)
		}

		if( patternsCount == 1 )
		{
			server_print("[ChatControl] %L", LANG_SERVER, "CC_LOADED_PATTERN")
		}
		else
		{
			server_print("[ChatControl] %L", LANG_SERVER, "CC_LOADED_PATTERNS", patternsCount)
		}
	}
}

public LoadNoSaveWords()
{
	new query[512]

	formatex(query, 511, "SELECT `value` FROM `acp_chat_nswords`")
	SQL_ThreadQuery(info, "LoadNoSaveWords_Post", query)

	return PLUGIN_HANDLED
}

public LoadNoSaveWords_Post(FailState,Handle:query,error[],errcode,Data[],DataSize)
{
    	if( FailState != TQUERY_SUCCESS )
	{
		log_amx("[ChatControl] %L", LANG_SERVER, "CC_CANT_LOAD", error)
	}
	else if( !SQL_NumResults(query) )
	{
		server_print("[ChatControl] %L", LANG_SERVER, "CC_NO_NSWORDS")
	}
	else
	{
		new wordsCount
		new zNSW = SQL_FieldNameToNum(query, "value")
		new g_zNSW[44];

		while (SQL_MoreResults(query))
		{
			SQL_ReadResult(query, zNSW, g_zNSW, 43);
			ArrayPushString(NoSaveWords, g_zNSW);

			wordsCount++;
			SQL_NextRow(query)
		}

		if (wordsCount == 1)
		{
			server_print("[ChatControl] %L", LANG_SERVER, "CC_LOADED_NSWORD")
		}
		else
		{
			server_print("[ChatControl] %L", LANG_SERVER, "CC_LOADED_NSWORDS", wordsCount)
		}
	}
}

public client_putinserver(id)
{
	g_warning_counter[id] = 0
	g_player_cfg_clean[id] = false
	g_player_translit[id] = false

	new szValue[2]
	get_user_info(id, translit_field, szValue, 1)

	if( equal(szValue, "1") )
	{
		new zAccept[22]	
		get_pcvar_string(pcvar_translit_access, zAccept, sizeof(zAccept) - 1)

		if ( (strlen(zAccept) > 0 && (get_user_flags(id) & read_flags(zAccept))) || !strlen(zAccept) ) 
			g_player_translit[id] = true
	}

	if( get_pcvar_num(pcvar_translit_def) && !g_player_translit[id] )
		PlayerSetLang(id)
}

public PlayerSetLang(id)
{
	g_player_translit[id] = (g_player_translit[id]) ? false : true;

	client_cmd(id, "setinfo ^"%s^" ^"%s^"", translit_field, (g_player_translit[id]) ? "1" : "0")

	client_print(id, print_chat, "[ChatControl] %L", LANG_PLAYER, (g_player_translit[id]) ? "CC_TRANSLIT_ON" : "CC_TRANSLIT_OFF")
}

public CmdSay(id) 
{
	new Float:maxChat = get_pcvar_float(pcvar_floodtime)

	if ( maxChat )
	{
		new Float:nexTime = get_gametime()
		
		if (g_Flooding[id] > nexTime)
		{
			if (g_Flood[id] >= 3)
			{
				client_print(id, print_chat, "[ChatControl] %L", LANG_PLAYER, "CC_STOP_FLOOD")
				g_Flooding[id] = nexTime + maxChat + 3.0
				return PLUGIN_HANDLED
			}
			g_Flood[id]++
		}
		else if (g_Flood[id])
		{
			g_Flood[id]--
		}
		
		g_Flooding[id] = nexTime + maxChat
	}

	new said[128], sName[32], nRes; 
	
	get_user_name(id, sName, sizeof(sName) - 1)    
	read_args(said, sizeof(said) - 1); 
	remove_quotes(said);
	replace_all(said, charsmax(said), "%s", "");
	replace_all(said, charsmax(said), "%S", "");
	
	if( !IsValidMessage(said) ) return PLUGIN_HANDLED;

	if( get_pcvar_num(pcvar_translit) && (said[0] != '!') && (said[0] != '/') )
	{
		if( g_player_translit[id] )
		{
			new output[5], symbol[2]
			for( new i; i < strlen(said); i++ )
			{
				copy(symbol, 1, said[i])
				if( TrieGetString(TranslitData, symbol, output, 4) )
				{
					if( strlen(said) < 126 )
						replace(said, charsmax(said), symbol, output)
				}
			}
		}
	}

	nRes = check_msg(id, said);
	insert_logs(id,said,nRes); 

	if( (said[0] == '@') && (get_user_flags(id) & ADMIN_CHAT) ) return PLUGIN_HANDLED_MAIN;
	if( equal(said, trigger, strlen(trigger)) && get_pcvar_num(pcvar_translit) )
	{
		new zAccept[22]	
		get_pcvar_string(pcvar_translit_access, zAccept, sizeof(zAccept) - 1)

		if ( (strlen(zAccept) > 0 && (get_user_flags(id) & read_flags(zAccept))) || !strlen(zAccept) ) 
			PlayerSetLang(id)
		else
			client_print(id, print_chat, "[ChatControl] %L", LANG_PLAYER, "CC_TRANSLIT_NOT")

		return PLUGIN_HANDLED_MAIN;
	}

	switch (nRes)
	{
		case -1, 0: {

			new zFlags[22], zAccess[22], alive = is_user_alive(id); 
			get_pcvar_string(pcvar_adminlisten, zAccess, sizeof(zAccess) - 1)
			get_pcvar_string(pcvar_vipaccess, zFlags, sizeof(zFlags) - 1)
	
			new tag[9]; 
			if( get_user_team(id) == 3 ) 
			{ 
				copy(tag, sizeof(tag) - 1, "*SPEC* "); 
			} 
			else if( !alive ) 
			{ 
				copy(tag, sizeof(tag) - 1, "*DEAD* "); 
			} 
			     
			new message[192]; 
	
			if( !(strlen(zFlags) > 0) || !(get_user_flags(id) & read_flags(zFlags)) )
			{
				formatex(message, sizeof(message) - 1, "^x01%s^x03%s^x01 :  ^x01%s", tag, sName, said); 
			}
			else 
			{
				formatex(message, sizeof(message) - 1, "^x01%s^x03%s^x01 :  ^x04%s", tag, sName, said); 
			}
	
			new uaccess, ualive
			new admin_allchat = get_pcvar_num(pcvar_adminlisten_type)
			new all_allchat = get_pcvar_num(pcvar_allchat)
	
			for( new i = 1; i <= g_max_clients; i++ ) 
			{ 
				if( !is_user_connected(i) ) continue;

				if( all_allchat < 2 )
				{
					uaccess = false
					ualive = is_user_alive(i)

					if( (get_user_flags(i) & read_flags(zAccess)) && strlen(zAccess) > 0  && (!ualive || admin_allchat) )
						uaccess = true;

					if( !uaccess )
					{
						if( (ualive != alive && !all_allchat) || (all_allchat && !alive && ualive) )
							continue;
					}
				}
	
				message_begin(MSG_ONE_UNRELIABLE, g_msgid_SayText, _, i); 
				write_byte(id); 
				write_string(message); 
				message_end(); 
			} 
	
			return PLUGIN_HANDLED_MAIN; 
		}
		case 1: {
			return PLUGIN_HANDLED_MAIN;
		}
		case 2: {

			new zAccess[22]
			get_pcvar_string(pcvar_adminview, zAccess, sizeof(zAccess) - 1)

			if( zAccess[0] )
			{
				new tag[9], zFlags[22], alive = is_user_alive(id) 
				get_pcvar_string(pcvar_vipaccess, zFlags, sizeof(zFlags) - 1)
		
				if( get_user_team(id) == 3 ) 
				{ 
					copy(tag, sizeof(tag) - 1, "*SPEC* ")
				} 
				else if( !alive ) 
				{ 
					copy(tag, sizeof(tag) - 1, "*DEAD* ")
				} 
				     
				new message[192]

				if( !(strlen(zFlags) > 0) || !(get_user_flags(id) & read_flags(zFlags)) )
				{
					formatex(message, sizeof(message) - 1, "***^x01%s^x03%s^x01 :  ^x01%s", tag, sName, said); 
				}
				else 
				{
					formatex(message, sizeof(message) - 1, "***^x01%s^x03%s^x01 :  ^x04%s", tag, sName, said); 
				}
		
				for( new i = 1; i <= g_max_clients; i++ ) 
				{ 
					if( !is_user_connected(i) || !(get_user_flags(i) & read_flags(zAccess)) ) continue; 	
					
					message_begin(MSG_ONE_UNRELIABLE, g_msgid_SayText, _, i); 
					write_byte(id);
					write_string(message);
					message_end();	
				}
			}

			AddPlayerBan(id, sName)

			return PLUGIN_HANDLED
		}
		case 3: {

			new zAccess[22]
			get_pcvar_string(pcvar_adminview, zAccess, sizeof(zAccess) - 1)

			if( zAccess[0] )
			{
				new tag[9], zFlags[22], alive = is_user_alive(id) 
				get_pcvar_string(pcvar_vipaccess, zFlags, sizeof(zFlags) - 1)
		
				if( get_user_team(id) == 3 ) 
				{ 
					copy(tag, sizeof(tag) - 1, "*SPEC* ")
				} 
				else if( !alive ) 
				{ 
					copy(tag, sizeof(tag) - 1, "*DEAD* ")
				} 
				     
				new message[192]

				if( !(strlen(zFlags) > 0) || !(get_user_flags(id) & read_flags(zFlags)) )
				{
					formatex(message, sizeof(message) - 1, "***^x01%s^x03%s^x01 :  ^x01%s", tag, sName, said); 
				}
				else 
				{
					formatex(message, sizeof(message) - 1, "***^x01%s^x03%s^x01 :  ^x04%s", tag, sName, said); 
				}
		
				for( new i = 1; i <= g_max_clients; i++ ) 
				{ 
					if( !is_user_connected(i) || !(get_user_flags(i) & read_flags(zAccess)) ) continue; 	
					
					message_begin(MSG_ONE_UNRELIABLE, g_msgid_SayText, _, i); 
					write_byte(id);
					write_string(message);
					message_end();	
				}
			}

			KickPlayer(id, sName)
				
			return PLUGIN_HANDLED
		}
		case 4: {

			new zAccess[22]
			get_pcvar_string(pcvar_adminview, zAccess, sizeof(zAccess) - 1)

			if( zAccess[0] )
			{
				new tag[9], zFlags[22], alive = is_user_alive(id) 
				get_pcvar_string(pcvar_vipaccess, zFlags, sizeof(zFlags) - 1)
		
				if( get_user_team(id) == 3 ) 
				{ 
					copy(tag, sizeof(tag) - 1, "*SPEC* ")
				} 
				else if( !alive ) 
				{ 
					copy(tag, sizeof(tag) - 1, "*DEAD* ")
				} 
				     
				new message[192]

				if( !(strlen(zFlags) > 0) || !(get_user_flags(id) & read_flags(zFlags)) )
				{
					formatex(message, sizeof(message) - 1, "***^x01%s^x03%s^x01 :  ^x01%s", tag, sName, said); 
				}
				else 
				{
					formatex(message, sizeof(message) - 1, "***^x01%s^x03%s^x01 :  ^x04%s", tag, sName, said); 
				}
		
				for( new i = 1; i <= g_max_clients; i++ ) 
				{ 
					if( !is_user_connected(i) || !(get_user_flags(i) & read_flags(zAccess)) ) continue; 	
					
					message_begin(MSG_ONE_UNRELIABLE, g_msgid_SayText, _, i); 
					write_byte(id);
					write_string(message);
					message_end();	
				}
			}

			g_warning_counter[id]++
			if( g_warning_counter[id] >= get_pcvar_num(pcvar_warn_count) )
			{
				if( get_pcvar_num(pcvar_cleancfg) && !g_player_cfg_clean[id] )
				{
					viewMenu(id)
				}
				else
				{
					if( get_pcvar_num(pcvar_warn_action) )
					{
						AddPlayerBan(id, sName)
					}
					else
					{
						KickPlayer(id, sName)
					}
				}

				return PLUGIN_HANDLED
			}
	
			client_print(id, print_center, "%L", LANG_PLAYER, "CC_WARN_HUD", g_warning_counter[id], get_pcvar_num(pcvar_warn_count))
	
			client_cmd(id,"spk fvox/warning")
			
			message_begin(MSG_ONE, gmsgFade, {0,0,0}, id)
			write_short(1<<12) // fade lasts this long duration  
			write_short(1<<12) // fade lasts this long hold time  
			write_short(0<<1) // fade type OUT 
			write_byte(255) // fade red  
			write_byte(0) // fade green  
			write_byte(0) // fade blue    
			write_byte(255) // fade alpha   
			message_end()
	
			client_print(0, print_chat, "[ChatControl] %L", LANG_PLAYER, "CC_WARN_MSG", sName)
			return PLUGIN_HANDLED
		}		
	}
	return PLUGIN_HANDLED 
} 

public CmdTeamSay(id) 
{
	new Float:maxChat = get_pcvar_float(pcvar_floodtime)

	if ( maxChat )
	{
		new Float:nexTime = get_gametime()
		
		if (g_Flooding[id] > nexTime)
		{
			if (g_Flood[id] >= 3)
			{
				client_print(id, print_chat, "[ChatControl] %L", LANG_PLAYER, "CC_STOP_FLOOD")
				g_Flooding[id] = nexTime + maxChat + 3.0
				return PLUGIN_HANDLED
			}
			g_Flood[id]++
		}
		else if (g_Flood[id])
		{
			g_Flood[id]--
		}
		
		g_Flooding[id] = nexTime + maxChat
	}

	new said[128], sName[32], nRes; 
	
	get_user_name(id, sName, sizeof(sName) - 1)    
	read_args(said, sizeof(said) - 1); 
	remove_quotes(said);

	if( !IsValidMessage(said) ) return PLUGIN_HANDLED;

	if( get_pcvar_num(pcvar_translit) && (said[0] != '!') && (said[0] != '/') )
	{
		if( g_player_translit[id] )
		{
			new output[5], symbol[2]
			for( new i; i < strlen(said); i++ )
			{
				copy(symbol, 1, said[i])
				if( TrieGetString(TranslitData, symbol, output, 4) )
				{
					if( strlen(said) < 126 )
						replace(said, charsmax(said), symbol, output)
				}
			}
		}
	}
	
	nRes = check_msg(id, said);
	insert_logs(id,said,nRes);

	if( (said[0] == '@') && (get_user_flags(id) & ADMIN_CHAT) ) return PLUGIN_HANDLED_MAIN;
	if( equal(said, trigger, strlen(trigger)) && get_pcvar_num(pcvar_translit) )
	{
		new zAccept[22]	
		get_pcvar_string(pcvar_translit_access, zAccept, sizeof(zAccept) - 1)

		if ( (strlen(zAccept) > 0 && (get_user_flags(id) & read_flags(zAccept))) || !strlen(zAccept) ) 
			PlayerSetLang(id)
		else
			client_print(id, print_chat, "[ChatControl] %L", LANG_PLAYER, "CC_TRANSLIT_NOT")

		return PLUGIN_HANDLED_MAIN;
	}

	switch (nRes) {
		case -1, 0: {

			new zFlags[22], zAccess[22], alive = is_user_alive(id); 
			get_pcvar_string(pcvar_adminlisten, zAccess, sizeof(zAccess) - 1)
			get_pcvar_string(pcvar_vipaccess, zFlags, sizeof(zFlags) - 1)
	
			new tag[8], team = get_user_team(id); 
			if( team == -1 ) team = 0
			if( !alive && team != 3) 
			{ 
				copy(tag, sizeof(tag) - 1, "*DEAD*"); 
			}
			     
			new message[192]; 
	
			if( !(strlen(zFlags) > 0) || !(get_user_flags(id) & read_flags(zFlags)) )
			{
				formatex(message, sizeof(message) - 1, "^x01%s(%s)^x03 %s^x01 :  ^x01%s", tag, g_team_names[team], sName, said); 
			}
			else 
			{
				formatex(message, sizeof(message) - 1, "^x01%s(%s)^x03 %s^x01 :  ^x04%s", tag, g_team_names[team], sName, said); 
			}

			new uaccess, ualive
			new admin_allchat = get_pcvar_num(pcvar_adminlisten_type)
			new all_allchat = get_pcvar_num(pcvar_allchat)
	
			for( new i = 1; i <= g_max_clients; i++ ) 
			{ 
				if( !is_user_connected(i) ) continue;

				if( all_allchat < 2 )
				{
					uaccess = false
					ualive = is_user_alive(i)

					if( (get_user_flags(i) & read_flags(zAccess)) && strlen(zAccess) > 0  && (!ualive || admin_allchat) )
						uaccess = true;

					if( !uaccess )
					{
						if( (get_user_team(i) != team) || (ualive != alive && !all_allchat) || (all_allchat && !alive && ualive) )
							continue;
					}
				}

				if( all_allchat < 2 )
				{
					uaccess = false
					ualive = is_user_alive(i)

					if( (get_user_flags(i) & read_flags(zAccess)) && strlen(zAccess) > 0  && (!ualive || admin_allchat) )
						uaccess = true;
		 
					if( !uaccess && get_user_team(i) != team )
						continue;

					if( ualive != alive && !all_allchat )
						continue;
				}
	
				message_begin(MSG_ONE_UNRELIABLE, g_msgid_SayText, _, i); 
				write_byte(id); 
				write_string(message); 
				message_end(); 
			} 

			return PLUGIN_HANDLED_MAIN; 
		}
		case 1: {
			return PLUGIN_HANDLED_MAIN;
		}
		case 2: {

			new zAccess[22]
			get_pcvar_string(pcvar_adminview, zAccess, sizeof(zAccess) - 1)

			if( zAccess[0] )
			{
				new tag[8], zFlags[22], team = get_user_team(id), alive = is_user_alive(id) 
				get_pcvar_string(pcvar_vipaccess, zFlags, sizeof(zFlags) - 1)
		
				if( !alive && team != 3) 
				{ 
					copy(tag, sizeof(tag) - 1, "*DEAD*"); 
				}
				     
				new message[192]

				if( !(strlen(zFlags) > 0) || !(get_user_flags(id) & read_flags(zFlags)) )
				{
					formatex(message, sizeof(message) - 1, "***^x01%s(%s)^x03 %s^x01 :  ^x01%s", tag, g_team_names[team], sName, said); 
				}
				else 
				{
					formatex(message, sizeof(message) - 1, "***^x01%s(%s)^x03 %s^x01 :  ^x04%s", tag, g_team_names[team], sName, said); 
				}
		
				for( new i = 1; i <= g_max_clients; i++ ) 
				{ 
					if( !is_user_connected(i) || !(get_user_flags(i) & read_flags(zAccess)) ) continue; 	
					
					message_begin(MSG_ONE_UNRELIABLE, g_msgid_SayText, _, i); 
					write_byte(id); 
					write_string(message); 
					message_end();	
				}
			}
	
			AddPlayerBan(id, sName)
		
			return PLUGIN_HANDLED
		}
		case 3: {

			new zAccess[22]
			get_pcvar_string(pcvar_adminview, zAccess, sizeof(zAccess) - 1)

			if( zAccess[0] )
			{
				new tag[8], zFlags[22], team = get_user_team(id), alive = is_user_alive(id) 
				get_pcvar_string(pcvar_vipaccess, zFlags, sizeof(zFlags) - 1)
		
				if( !alive && team != 3) 
				{ 
					copy(tag, sizeof(tag) - 1, "*DEAD*"); 
				}
				     
				new message[192]

				if( !(strlen(zFlags) > 0) || !(get_user_flags(id) & read_flags(zFlags)) )
				{
					formatex(message, sizeof(message) - 1, "***^x01%s(%s)^x03 %s^x01 :  ^x01%s", tag, g_team_names[team], sName, said); 
				}
				else 
				{
					formatex(message, sizeof(message) - 1, "***^x01%s(%s)^x03 %s^x01 :  ^x04%s", tag, g_team_names[team], sName, said); 
				}
		
				for( new i = 1; i <= g_max_clients; i++ ) 
				{ 
					if( !is_user_connected(i) || !(get_user_flags(i) & read_flags(zAccess)) ) continue; 	
					
					message_begin(MSG_ONE_UNRELIABLE, g_msgid_SayText, _, i); 
					write_byte(id); 
					write_string(message); 
					message_end();	
				}
			}

			KickPlayer(id, sName)
				
			return PLUGIN_HANDLED
		}
		case 4: {

			new zAccess[22]
			get_pcvar_string(pcvar_adminview, zAccess, sizeof(zAccess) - 1)

			if( zAccess[0] )
			{
				new tag[8], zFlags[22], team = get_user_team(id), alive = is_user_alive(id) 
				get_pcvar_string(pcvar_vipaccess, zFlags, sizeof(zFlags) - 1)
		
				if( !alive && team != 3) 
				{ 
					copy(tag, sizeof(tag) - 1, "*DEAD*"); 
				}
				     
				new message[192]

				if( !(strlen(zFlags) > 0) || !(get_user_flags(id) & read_flags(zFlags)) )
				{
					formatex(message, sizeof(message) - 1, "***^x01%s(%s)^x03 %s^x01 :  ^x01%s", tag, g_team_names[team], sName, said); 
				}
				else 
				{
					formatex(message, sizeof(message) - 1, "***^x01%s(%s)^x03 %s^x01 :  ^x04%s", tag, g_team_names[team], sName, said); 
				}
		
				for( new i = 1; i <= g_max_clients; i++ ) 
				{ 
					if( !is_user_connected(i) || !(get_user_flags(i) & read_flags(zAccess)) ) continue; 	
					
					message_begin(MSG_ONE_UNRELIABLE, g_msgid_SayText, _, i); 
					write_byte(id); 
					write_string(message); 
					message_end();	
				}
			}

			g_warning_counter[id]++
			if( g_warning_counter[id] >= get_pcvar_num(pcvar_warn_count) )
			{
				if( get_pcvar_num(pcvar_cleancfg) && !g_player_cfg_clean[id] )
				{
					viewMenu(id)
				}
				else
				{
					if( get_pcvar_num(pcvar_warn_action) )
					{
						AddPlayerBan(id, sName)
					}
					else
					{
						KickPlayer(id, sName)
					}
				}

				return PLUGIN_HANDLED
			}
	
			client_print(id, print_center, "%L", LANG_PLAYER, "CC_WARN_HUD", g_warning_counter[id], get_pcvar_num(pcvar_warn_count))
	
			client_cmd(id, "spk fvox/warning")
			
			message_begin(MSG_ONE, gmsgFade, {0,0,0}, id)
			write_short(1<<12) // fade lasts this long duration  
			write_short(1<<12) // fade lasts this long hold time  
			write_short(0<<1) // fade type OUT 
			write_byte(255) // fade red  
			write_byte(0) // fade green  
			write_byte(0) // fade blue    
			write_byte(255) // fade alpha   
			message_end()
	
			client_print(0, print_chat, "[ChatControl] %L", LANG_PLAYER, "CC_WARN_MSG", sName)
			return PLUGIN_HANDLED
		}		
	}
	return PLUGIN_HANDLED; 
} 

public cmdAdminChat(id)
{
	if (get_user_flags(id) & ADMIN_CHAT)
	{
		if (read_argc() > 1)
		{
			new message[128]  
			read_args(message, 127)
			insert_logs(id, message, -1)
		}
	}
}

bool:IsValidMessage(const said[]) 
{ 
	if( !strlen(said) ) return false;

	for( new i = 0; said[i]; i++ ) 
	{ 
		if( said[i] != ' ' ) 
		{ 
			break; 
		} 
	} 

	return true; 
} 

public ResetOptions()
{
	copy(g_player_reason, 43, "");
	copy(g_ban_length, 10, "");
}

public check_msg(id, said[])
{
	new msg[128], zAccess[22]
	copy(msg, sizeof(msg) - 1, said)
	strtolower(msg)

	get_pcvar_string(pcvar_immunity, zAccess, sizeof(zAccess) - 1)

	for (new u = 0; u < sizeof( g_action_list ); u++)
	{
		new Array:patternsData
		if( TrieGetCell(ActionData,g_action_list[u],patternsData) )
		{
			for( new y=0; y<ArraySize(patternsData); y++ )
			{
				new data[ MsgData ]
				ArrayGetArray(patternsData,y,data);
				zRes = regex_match(msg, data[ MSG_TEXT ], g_regex_return, error, 127)
				if( zRes >= REGEX_OK )
				{
					regex_free(zRes)

					switch(u) {
						case 2: {
							copy(g_player_reason, 43, data[ MSG_REASON ]);
							copy(g_ban_length, 10, data[ MSG_LENGTH ]);
						}
						case 3: {
							copy(g_player_reason, 43, data[ MSG_REASON ]);
						}
						case 4: {
							if ( get_pcvar_num(pcvar_warn_action) )
							{
								copy(g_player_reason, 43, data[ MSG_REASON ]);
								copy(g_ban_length, 10, data[ MSG_LENGTH ]);
							}
							else
							{
								copy(g_player_reason, 43, data[ MSG_REASON ]);
							}
						}
					}

					if( strlen(zAccess) > 0 && (get_user_flags(id) & read_flags(zAccess)) && u != 1  )
						return 1
					else
						return u 									
				}					
			}
		}
	}
		
	return -1
}

public check_nsw(id, said[])
{
	new msg[128], szWord[44]
	copy(msg, sizeof(msg) - 1, said)
	strtolower(msg)

	for (new u = 0; u < ArraySize(NoSaveWords); u++)
	{
		ArrayGetString(NoSaveWords, u, szWord, charsmax(szWord));
		zRes = regex_match(msg, szWord, g_regex_return, error, 127)
		if (zRes >= REGEX_OK)
		{
			regex_free(zRes)
			return 1
		}
	}

	return 0
}

public insert_logs(id,said[],pattern) 
{
	if(is_user_bot(id) || !is_user_connected(id) || check_nsw(id, said) || !get_pcvar_num(pcvar_savelogs) ) return PLUGIN_HANDLED

	new authid[32], name[64], ip[16], cmd[9], insert_msg[256], team = get_user_team(id),  timestamp = get_systime(0), foradmins

	get_user_authid(id, authid, sizeof(authid) - 1)  
	get_user_name(id, name, sizeof(name) - 1)  
	get_user_ip(id, ip, sizeof(ip) - 1, 1)
	read_argv(0,cmd,8)
	copy(insert_msg, sizeof(insert_msg) - 1, said)
	replace_all(insert_msg, sizeof(insert_msg) - 1, "\", "")
	replace_all(insert_msg, sizeof(insert_msg) - 1, "'", "\'")

	if( (insert_msg[0] == '@') && equal(cmd,"say_team") )
	{
		if ( get_user_flags(id) & ADMIN_CHAT )
		{
			format(cmd, sizeof(cmd) - 1, "amx_chat")
		}
		else
		{
			foradmins = true
		}

		format(insert_msg, sizeof(insert_msg) - 1, "%s", insert_msg[1])
	}
	replace_all(name, sizeof(name) - 1, "'", "\'")

	new query[1001], subquery[128], uid = 0
	#if defined ACP_GENERAL
		uid = acp_get_player_dbid(id)
	#endif
	#if defined UTF_QUERY
		formatex(subquery, 127, "%s", UTF_QUERY)
	#endif
	format(query,1000,"%sINSERT into `acp_chat_logs` (serverip,name,authid,ip,alive,team,timestamp,message,cmd,foradmins,pattern,uid) values ('%s','%s','%s','%s','%d','%s','%i','%s','%s','%d','%d','%d')", subquery, g_serverip, name, authid, ip, is_user_alive(id), g_team_names[team], timestamp, insert_msg, cmd, foradmins ? 1 : 0, pattern, uid) 
	SQL_ThreadQuery(info, "QueryHandle", query)

	return PLUGIN_CONTINUE
}

public QueryHandle(FailState, Handle:hQuery, Error[], Errcode, Data[], DataSize) 
{ 
	if( FailState != TQUERY_SUCCESS )                       
	{
		log_amx("[ChatControl] SQL Error #%d - %s", Errcode, Error)        
	}
	return PLUGIN_CONTINUE
}

AddPlayerBan(id, name[])
{
	new ban_cmd[128], auth[32]

	if ( equal(g_player_reason,"") )
		format(g_player_reason, 43, "%L", LANG_PLAYER, "CC_BAN_REASON")

	if ( equal(g_ban_length, "") || !is_str_num(g_ban_length) )
		get_pcvar_string(pcvar_bantime, g_ban_length, 10)

	if( get_pcvar_num(pcvar_bantype) == 2 )
	{
		new tempstr[36]
		get_pcvar_string(pcvar_bancmd, ban_cmd, 127)

		formatex(tempstr, 33, "#%d", get_user_userid(id))
		replace_all(ban_cmd, 127, "%userid%", tempstr)

		get_user_authid(id, auth, 31)
		replace_all(ban_cmd, 127, "%steamid%", auth)
		
		get_user_ip(id, auth, 31, 1)		
		replace_all(ban_cmd, 127, "%ip%", auth)

		replace_all(ban_cmd, 127, "%name%", name)

		replace_all(ban_cmd, 127, "%reason%", g_player_reason)	

		replace_all(ban_cmd, 127, "%time%", g_ban_length)
	}
	else
	{
		new bool:banbysteam = true
		get_user_authid(id, auth, 31)

		if( !IsValidAuthid(auth) )
		{
			banbysteam = false
			get_user_ip(id, auth, 31, 1)
		}		
	
		if( banbysteam )
		{	
			formatex(ban_cmd, 127, "amx_ban %s %s %s", (get_pcvar_num(pcvar_bantype) == 1) ? g_ban_length : auth, (get_pcvar_num(pcvar_bantype) == 1) ? auth : g_ban_length, g_player_reason)	
		}
		else
		{
			formatex(ban_cmd, 127, "amx_banip %s %s %s", (get_pcvar_num(pcvar_bantype) == 1) ? g_ban_length : auth, (get_pcvar_num(pcvar_bantype) == 1) ? auth : g_ban_length, g_player_reason)	
		}
	}
	
	server_cmd(ban_cmd)

	client_print(0, print_chat, "[ChatControl] %L", LANG_PLAYER, "CC_BAN_MSG", name, g_player_reason)

	ResetOptions()
}

KickPlayer(id, name[])
{
	new userid = get_user_userid(id)							
	if ( equal(g_player_reason,"") )
		format(g_player_reason, 43, "%L", LANG_PLAYER, "CC_KICK_REASON")

	server_cmd("kick #%d ^"%s^"", userid, g_player_reason)

	client_print(0, print_chat, "[ChatControl] %L", LANG_PLAYER, "CC_KICK_MSG", name, g_player_reason)
}

viewMenu(iPlayer)
{
	new szTitle[200], szYes[100], szNo[100]

	formatex(szTitle, charsmax(szTitle), "%L", iPlayer, "CC_MENU_TITLE")
	formatex(szYes, charsmax(szYes), "%L", iPlayer, "CC_MENU_YES")
	formatex(szNo, charsmax(szNo), "%L", iPlayer, "CC_MENU_NO")

    	new hMenu = menu_create( szTitle, "funcMenu" )

	menu_additem(hMenu, szYes, "1")
	menu_additem(hMenu, szNo, "2")
	menu_setprop(hMenu, MPROP_NUMBER_COLOR, "\r")
	menu_setprop(hMenu, MPROP_EXIT, MEXIT_NEVER)

	menu_display(iPlayer, hMenu)
}

public funcMenu(iPlayer, hMenu, iItem)
{
    	if( iItem == MENU_EXIT )
    	{
        	menu_destroy( hMenu );
        	return;
    	}

    	new iAccess, szNum[3], hCallback
    	menu_item_getinfo( hMenu, iItem, iAccess, szNum, charsmax( szNum ), _, _, hCallback )
    	menu_destroy( hMenu )

	new iItemIndex = str_to_num(szNum)

	switch( iItemIndex )
	{
		case 1:
		{
			for( new i = 0; i < default_cfg_num; i++ )
			{
				client_cmd(iPlayer, default_cfg[i])
			}

			g_warning_counter[iPlayer] = 0
			g_player_cfg_clean[iPlayer] = true
		}

		case 2:
		{
			new sName[32]
			get_user_name(iPlayer, sName, sizeof(sName) - 1)

			if( get_pcvar_num(pcvar_warn_action) )
			{
				AddPlayerBan(iPlayer, sName)
			}
			else
			{
				KickPlayer(iPlayer, sName)
			}
		}
	}
}