@ECHO OFF
CLS

SET SELF=%~dp0
SET SRV=DR Updater.exe
SET UTIL=nssm.exe
SET DESC=DR Server Updater

"%SELF%%UTIL%" stop "%SRV%" 
"%SELF%%UTIL%" remove "%SRV%" confirm

"%SELF%%UTIL%" install "%SRV%" "%SELF%%SRV%"
REM "%SELF%%UTIL%" start "%SRV%" 

sc config "%SRV%" obj= visco\jrender password= !ViscoLviv! start= demand

PAUSE