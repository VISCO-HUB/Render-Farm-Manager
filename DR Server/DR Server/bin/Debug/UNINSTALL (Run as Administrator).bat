@ECHO OFF
CLS

SET SELF=%~dp0
SET SRV=DR Server.exe
SET UTIL=nssm.exe
SET DESK=DR Server Manager

"%SELF%%UTIL%" stop "%SRV%" 
"%SELF%%UTIL%" remove "%SRV%" confirm
PAUSE
