/* Load UI for Distributed Rendering script */
	
dr_Path = @"\\visco.local\data\Instal_Sync\scripts\dr\DR_MONITOR.ms"
try(fileIn dr_Path)catch(print (dr_Path + " - Not loaded!"))