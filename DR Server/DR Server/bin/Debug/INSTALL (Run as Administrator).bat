@ECHO OFF
CLS

SET SELF=%~dp0
SET SRV=DR Server.exe
SET UTIL=nssm.exe
SET DESC=DR Server Manager

"%SELF%%UTIL%" stop "%SRV%" 
"%SELF%%UTIL%" remove "%SRV%" confirm

"%SELF%%UTIL%" install "%SRV%" "%SELF%%SRV%"

sc config "%SRV%" obj= visco\jrender password= !ViscoLviv! start= auto

"%SELF%%UTIL%" start "%SRV%" 

PAUSE