
/*
	DR User Monitor  for Render Farm Manager
	1.0.6
	MastaMan 
*/

try(DestroyDialog _DR_MONITOR)catch()

global _DR_MONITOR
global _DR_RUN = "DR_UI.ms"
global _DR_PATH = getFileNamePath (getThisScriptFileName())
global _DR_INI = _DR_PATH + "settings.ini"
global _URL = ""
global _CHK = 0
	
fn getCurrMaxVer =
(
	v = maxVersion()
	v = (v[1] / 1000) - 2
	v = (2000 + v) as string
	return v
)

fn getUser = sysInfo.username

fn isVray =
(
	_Vray = renderers.current
	if(matchPattern (_Vray as string) pattern:"V_Ray_Adv*") do return true
	return false
)

fn distributedOn s =
(
	_Vray = renderers.current
	if(isVray() == true) do _Vray.system_distributedRender = s
)

fn httpGet url =
(	
	w = dotNetObject (dotNetClass "System.Net.WebClient")
	s = dotNetClass "System.IO.StreamReader"
	o = w.OpenRead url
	l = dotNetObject s o
	t = l.ReadToEnd()
	
	return t
)

fn getUserNodes =
(
	url = _URL + "vault/mxs/mxsGetUserNodes.php?user=" + getUser()
	r = httpGet url
		
	if(r == "NONODES") do return ""
	return r
)	

fn deleteUser =
(
	url = _URL + "vault/mxs/mxsDropUserNodes.php?user=" + getUser()
	r = httpGet url
)

fn getIniVrayParams p d: "0" t: #boolint =
(
	u = getUser()

	s = getINISetting _DR_INI p u

	if(s == undefined or s == "") do return d
	
	case t of
	(
		#boolint: s = if(s == "true") then "1" else "0"
	)
	
	return s
)

fn checkNodesVray =
(
	udr = #()
	dr_cfg = (getDir #plugcfg) + @"\vray_dr.cfg"
	u = getUser()		
	
	v = getCurrMaxVer()	
	n = getUserNodes()
		
	-- Clear config
	if(n == "NONODES" or n == "ERROR") do 
	(		
		distributedOn false
		
		deleteFile dr_cfg
		
		f = openFile dr_cfg mode: "wt"
		format ("") to:f
		flush f
		close f	
		
		return false
	)

	distributedOn true
		
	deleteFile dr_cfg
	
	spawnersCount = 0
	
	f = openFile dr_cfg mode: "wt"
	
	for i in (filterString n ";") do 
	(
		n = filterString i "-"
		if(v != n[2]) do continue
		
		format (n[1] + " 1 20204\n") to:f
		spawnersCount += 1
	)
	
	RestartServersOnRenderEnd = getIniVrayParams "RESTART_SERVERS" d:"0" t:#boolint
	UseLocalHost = getIniVrayParams "USE_LOCALHOST" d:"1" t:#boolint 
	TransferMissingAssets = getIniVrayParams "MISSING_ASSET" d:"0" t:#boolint 
	UseCachedAssets = getIniVrayParams "CACHED_ASSET" d:"0" t:#boolint 
	CacheLimitType = getIniVrayParams "CACHED_LIMIT_TYPE" d:"0" t:#string 
	CacheLimitValue = getIniVrayParams "CACHED_LIMIT_VALUE" d:"100.0" t:#string
	
	format("restart_slaves " + RestartServersOnRenderEnd + "\n")  to:f
	format("list_in_scene 0\n")  to:f
	format("max_servers 0\n")  to:f
	format("use_local_machine " + UseLocalHost + "\n")  to:f
	format("transfer_missing_assets " + TransferMissingAssets + "\n")  to:f
	format("use_cached_assets " + UseCachedAssets + "\n")  to:f
	format("cache_limit_type " + CacheLimitType + "\n")  to:f
	format("cache_limit " + CacheLimitValue + "\n")  to:f
	
	flush f
	close f
	
	m = "Used nodes " + spawnersCount as string
	if(spawnersCount == 0) then
	(
		m = "Nodes not added! Not found VraySpawner " + v +" on any reserved node!"
	)
	
	pushPrompt ("Render Farm Manager: " + m)
)

fn checkNodes =
(
	if(isVray()) do 
	(	
		checkNodesVray()
	)		
)

rollout _DR_MONITOR ""
(
	on _DR_MONITOR open do
	(	
		_URL = getINISetting _DR_INI "MAIN" "URL"
		
		callbacks.removescripts id:#fwdUserIni1
		callbacks.addScript #systemPostReset "deleteUser()" id:#fwdUserIni1 persistent:false	
		
		callbacks.removescripts id:#fwdUserIni2
		callbacks.addScript #systemPostNew "deleteUser()" id:#fwdUserIni2 persistent:false	
		
		callbacks.removescripts id:#fwdUserIni3
		callbacks.addScript #preSystemShutdown "deleteUser()" id:#fwdUserIni3 persistent:false	
		
		callbacks.removescripts id:#fwdPreRender
		callbacks.addScript #preRender "checkNodes()" id:#fwdPreRender persistent:false
						
		SetDialogPos _DR_MONITOR [-1000,0]
						
		fn blockDr =
		(															
			hwnd = dialogMonitorOps.getWindowHandle()
			
			dialogTitle = uiAccessor.getWindowText hwnd
			
			if (dialogTitle == "V-Ray distributed rendering settings") then
			(
				enabled = getINISetting _DR_INI "OFFLINE" "ENABLE"	
				
				UIAccessor.CloseDialog hwnd	
							
				try(fileIn (_DR_PATH  + _DR_RUN))catch(messageBox "Lost network connection!" title: "Warning")	

				return true
			)	

			return true
		)	
				
		dialogMonitorOps.unregisterNotification id:#closeDrWindow
		dialogMonitorOps.enabled = true
		dialogMonitorOps.interactive = false
		dialogMonitorOps.registerNotification blockDr id:#closeDrWindow			
	)
		
	on _DR_MONITOR close do
	(
		dialogMonitorOps.unregisterNotification id:#closeDrWindow
		dialogMonitorOps.enabled = false
		
		callbacks.removescripts id:#fwdUserIni1
		callbacks.removescripts id:#fwdUserIni2
		callbacks.removescripts id:#fwdUserIni3
		callbacks.removescripts id:#fwdPreRender
	)	
)

createDialog _DR_MONITOR 0 0 style: #()