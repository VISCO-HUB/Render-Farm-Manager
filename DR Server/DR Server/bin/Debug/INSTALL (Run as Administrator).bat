@ECHO OFF
CLS

SET SELF=%~dp0
SET SRV=DR Server.exe
SET UTIL=nssm.exe
SET DESC=DR Server Manager

"%SELF%%UTIL%" stop "%SRV%" 
"%SELF%%UTIL%" remove "%SRV%" confirm

"%SELF%%UTIL%" install "%SRV%" "%SELF%%SRV%"
"%SELF%%UTIL%" start "%SRV%" 



PAUSE