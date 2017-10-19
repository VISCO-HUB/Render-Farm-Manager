@ECHO OFF
CLS

SET SELF=%~dp0
SET SRV=DR Updater.exe
SET UTIL=nssm.exe
SET DESK=DR Server Updater

"%SELF%%UTIL%" stop "%SRV%" 
"%SELF%%UTIL%" remove "%SRV%" confirm
PAUSE
