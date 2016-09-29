try(DestroyDialog _DR)catch()	

global _DR_PATH = getFileNamePath (getThisScriptFileName())
global _DR_INI = _DR_PATH + "settings.ini"
global _DR_START = "DR_STARTUP.ms"
global _DR_STARTUP = _DR_PATH + _DR_START
global _DR_INSTALLED_STARTUP = (getDir #startupScripts) + @"\" + _DR_START

rollout _DR "Distributed Rendering Settings" 
(
	dotNetControl WebBrowser "System.Windows.Forms.WebBrowser" pos:[0,0] width:1000 height:775
	
	group "Distributed Rendering Settings:"
	(
		checkbox cbxRestartServersOnRenderEnd "Restart servers on render end" across: 2
		checkbox cbxTransferMissingAssets "Transfer missing assets" align: #left offset: [-200, 0]
		checkbox cbxUseLocalHost "Use local host" across: 2 checked: true
		checkbox cbxUseCachedAssets "Use cached assets" across: 2 enabled: false align: #left offset: [-200, 0]
		dropdownlist ddlCacheLimitType "" width: 90 items: #("None", "Age (hours)", "Size (GB)") align: #right offset: [-111,0] across: 2 enabled: false
		label lblCacheLimitType  "Cache limit type" align: #right offset: [-510, 3] enabled: false
		button btnResetToDefaults "Reset to defaults" across: 3 align: #left
		spinner spnCacheLimitValue "" width: 60 range: [0.0, 1000000.0, 100] align: #right offset: [-302,0] enabled: false
		label lblCacheLimitValue "Cache limit value" align: #right offset: [-505, 0] enabled: false
	)
	
	fn writeFile f s =
	(			
		f = createFile f mode: "wt"

		format(s as string) to:f

		flush f
		close f
	)
	fn getUser =  sysInfo.username
	fn getBool b = if(b == "true") then return true else return false
	fn getInt i = if(i != undefined and i != "0" and i != "") then return i as integer else return 0
	fn getFloat  f = if(f != undefined  and f != "") then return f as float else return 100.0
	fn boolToString b = if(b) then return "1" else return "0"
	
	fn drSettings t:#get =
	(
		u = getUser()
		
		TransferMissingAssets = cbxTransferMissingAssets.checked as string
		UseCachedAssets = cbxUseCachedAssets.checked as string
		CacheLimitType = (ddlCacheLimitType.selection - 1) as string
		CacheLimitValue = spnCacheLimitValue.value as string
		
		RestartServersOnRenderEnd = cbxRestartServersOnRenderEnd.checked as string
		UseLocalHost = cbxUseLocalHost.checked as string
		
		case t of
		(
			#get:
			(
				l = getINISetting _DR_INI "USE_LOCALHOST" u
				UseLocalHost = if(l == undefined or l == "") then true else getBool l
				cbxUseLocalHost.checked  = UseLocalHost
				
				RestartServersOnRenderEnd = getBool (getINISetting _DR_INI "RESTART_SERVERS" u)
				cbxRestartServersOnRenderEnd.checked = RestartServersOnRenderEnd
				
				/* ASSETS */
				
				TransferMissingAssets = getBool (getINISetting _DR_INI "MISSING_ASSET" u)
				cbxTransferMissingAssets.checked = TransferMissingAssets
				
				cbxUseCachedAssets.enabled = TransferMissingAssets
				
				UseCachedAssets = getBool (getINISetting _DR_INI "CACHED_ASSET" u)
				cbxUseCachedAssets.checked  = UseCachedAssets
				
				ddlCacheLimitType.enabled = UseCachedAssets and TransferMissingAssets
				lblCacheLimitType.enabled = UseCachedAssets and TransferMissingAssets
				
				CacheLimitType = getInt (getINISetting _DR_INI "CACHED_LIMIT_TYPE" u)  + 1				
				ddlCacheLimitType.selection = CacheLimitType
				
				spnCacheLimitValue.enabled = CacheLimitType > 1 and TransferMissingAssets and UseCachedAssets
				lblCacheLimitValue.enabled = CacheLimitType > 1 and TransferMissingAssets and UseCachedAssets
				
				CacheLimitValue = getFloat (getINISetting _DR_INI "CACHED_LIMIT_VALUE" u) 
				spnCacheLimitValue.value = CacheLimitValue
			)
			default: 
			(
				setINISetting _DR_INI "MISSING_ASSET" u TransferMissingAssets
				setINISetting _DR_INI "CACHED_ASSET" u UseCachedAssets
				setINISetting _DR_INI "CACHED_LIMIT_TYPE" u CacheLimitType				
				setINISetting _DR_INI "CACHED_LIMIT_VALUE" u CacheLimitValue
								
				setINISetting _DR_INI "USE_LOCALHOST" u UseLocalHost
				setINISetting _DR_INI "RESTART_SERVERS" u RestartServersOnRenderEnd								
			)
		)	 
	)
	
	on _DR open do
	(
		_URL = getINISetting _DR_INI "MAIN" "URL"
		if(_URL == undefined or _URL == "") do return messageBox "Please set correct Ulr in settings.ini"
		WebBrowser.url = dotNetObject "System.Uri" _URL
					
		_INSTALLED = getINISetting _DR_INI "INSTALLED" (getUser())
		
		if(not doesFileExist _DR_INSTALLED_STARTUP or _INSTALLED == "") do
		(
			f = @"c:\temp\"
			
			makeDir f
			f += "install.bat"
			
			s = ""
			s += "del /F /Q \"" + _DR_INSTALLED_STARTUP + "\""
			s += "\ncopy \"" + _DR_STARTUP + "\" \"" + _DR_INSTALLED_STARTUP  + "\" /Y"
			print s	
			writeFile f s
		
			setIniSetting _DR_INI "INSTALLED" (getUser()) "true"
			
			HiddenDOSCommand (_DR_PATH + "install.lnk") 
			setIniSetting _DR_INI "INSTALLED" (getUser()) "true"			
		)
		
		try
		(				
			fileIn _DR_INSTALLED_STARTUP		
		)catch()
		
		
		
		drSettings t:#get	
	)	
		
	on cbxRestartServersOnRenderEnd changed v do drSettings t:#set		
	on cbxUseLocalHost changed v do drSettings t:#set
	on ddlCacheLimitType selected v do drSettings t:#set
	on spnCacheLimitValue changed v do drSettings t:#set
		
	on cbxTransferMissingAssets changed x do
	(
		i = ddlCacheLimitType.selection
		ii = cbxUseCachedAssets.checked
		
		cbxUseCachedAssets.enabled = x
		
		
		ddlCacheLimitType.enabled = x and ii 
		lblCacheLimitType.enabled = x and ii
		spnCacheLimitValue.enabled = x and i != 1
		lblCacheLimitValue.enabled = x and i != 1		
		
		drSettings t:#set
	)
	
	on cbxUseCachedAssets changed x do
	(
		i = ddlCacheLimitType.selection
		
		ddlCacheLimitType.enabled = x
		lblCacheLimitType.enabled = x
		spnCacheLimitValue.enabled = x and i != 1
		lblCacheLimitValue.enabled = x and i != 1
		
		drSettings t:#set
	)
	
	on ddlCacheLimitType selected x do
	(
		i = ddlCacheLimitType.selection
		
		spnCacheLimitValue.enabled = (i != 1)
		lblCacheLimitValue.enabled = (i != 1)
	)
	on btnResetToDefaults pressed do
	(
		cbxRestartServersOnRenderEnd.checked = false
		cbxUseLocalHost.checked = true
		cbxTransferMissingAssets.checked = false
		cbxUseCachedAssets.checked = false
		ddlCacheLimitType.selection = 1
		spnCacheLimitValue.value = 100.0
		
		cbxUseCachedAssets.enabled = false
		ddlCacheLimitType.enabled = false
		spnCacheLimitValue.enabled = false
		lblCacheLimitType.enabled = false
		lblCacheLimitValue.enabled = false
		
		drSettings t:#set
		drSettings t:#get
	)
)

createDialog _DR 1000 900 