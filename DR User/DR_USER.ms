
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
	
fn getCurrMaxVer =
(
	v = maxVersion()
	v = (v[1] / 1000) - 2
	v = (2000 + v) as string
	return v
)

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

fn getUserNodes u =
(
	url = "http://viscocg.com/dr/vault/userNodes.php?user=" + u
	w = dotNetObject (dotNetClass "System.Net.WebClient")
	s = dotNetClass "System.IO.StreamReader"
	o = w.OpenRead url
	l = dotNetObject s o
	t = l.ReadToEnd()
	
	if(t == "NONODES") do return ""
	if(t == "ERROR") do return -1
	return t
)	

fn checkNodesVray =
(
	udr = #()
	dr_cfg = (getDir #plugcfg) + @"\vray_dr.cfg"
	u = sysInfo.username		
	
	v = getCurrMaxVer()	
	n = getUserNodes u
		
	-- Do nothing if error
	if(n == "ERROR") do return false
	-- Clear config
	if(n == "NONODES") do 
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
	
	f = openFile dr_cfg mode: "wt"
	
	for i in (filterString n ";") do 
	(
		n = filterString i "-"
		if(v != n[2]) do continue
		
		format (n[1] + " 1 20204\n") to:f
	)
	
	flush f
	close f
)

fn checkNodes =
(
	if(isVray()) do checkNodesVray()
)

rollout _DR_MONITOR ""
(	
	on _DR_MONITOR open do
	(												
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
			enabled = getINISetting drINI "OFFLINE" "ENABLE"	
									
			hwnd = dialogMonitorOps.getWindowHandle()
			
			dialogTitle = uiAccessor.getWindowText hwnd
			
			if (dialogTitle == "V-Ray distributed rendering settings") do
			(
				UIAccessor.CloseDialog hwnd	
							
				try(fileIn (_DR_PATH  + _DR_RUN))catch(messageBox "Lost network connection!" title: "Warning")												
			)
						
			true			
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
		
		/*callbacks.removescripts id:#fwdUserIni1
		callbacks.removescripts id:#fwdUserIni2
		callbacks.removescripts id:#fwdUserIni3*/
		callbacks.removescripts id:#fwdPreRender
	)
)

createDialog _DR_MONITOR 0 0 style: #()