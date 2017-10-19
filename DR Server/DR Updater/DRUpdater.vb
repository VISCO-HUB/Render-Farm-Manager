Imports System
Imports System.Net
Imports System.Net.Sockets
Imports System.Text
Imports System.Threading
Imports System.IO
Imports System.ServiceProcess

Public Class clsIni
    ' API functions
    Private Declare Ansi Function GetPrivateProfileString _
  Lib "kernel32.dll" Alias "GetPrivateProfileStringA" _
  (ByVal lpApplicationName As String,
  ByVal lpKeyName As String, ByVal lpDefault As String,
  ByVal lpReturnedString As System.Text.StringBuilder,
  ByVal nSize As Integer, ByVal lpFileName As String) _
  As Integer
    Private Declare Ansi Function WritePrivateProfileString _
  Lib "kernel32.dll" Alias "WritePrivateProfileStringA" _
  (ByVal lpApplicationName As String,
  ByVal lpKeyName As String, ByVal lpString As String,
  ByVal lpFileName As String) As Integer
    Private Declare Ansi Function GetPrivateProfileInt _
  Lib "kernel32.dll" Alias "GetPrivateProfileIntA" _
  (ByVal lpApplicationName As String,
  ByVal lpKeyName As String, ByVal nDefault As Integer,
  ByVal lpFileName As String) As Integer
    Private Declare Ansi Function FlushPrivateProfileString _
  Lib "kernel32.dll" Alias "WritePrivateProfileStringA" _
  (ByVal lpApplicationName As Integer,
  ByVal lpKeyName As Integer, ByVal lpString As Integer,
  ByVal lpFileName As String) As Integer
    Dim strFilename As String

    ' Constructor, accepting a filename
    Public Sub New(ByVal Filename As String)
        strFilename = Filename
    End Sub

    ' Read-only filename property
    ReadOnly Property FileName() As String
        Get
            Return strFilename
        End Get
    End Property

    Public Function GetString(ByVal Section As String,
  ByVal Key As String, ByVal [Default] As String) As String
        ' Returns a string from your INI file
        Dim intCharCount As Integer
        Dim objResult As New System.Text.StringBuilder(256)
        intCharCount = GetPrivateProfileString(Section, Key, [Default], objResult, objResult.Capacity, strFilename)
        If intCharCount > 0 Then
            GetString = Left(objResult.ToString, intCharCount)
        Else
            GetString = ""
        End If

    End Function

    Public Function GetInteger(ByVal Section As String,
  ByVal Key As String, ByVal [Default] As Integer) As Integer
        ' Returns an integer from your INI file
        Return GetPrivateProfileInt(Section, Key,
       [Default], strFilename)
    End Function

    Public Function GetBoolean(ByVal Section As String,
  ByVal Key As String, ByVal [Default] As Boolean) As Boolean
        ' Returns a boolean from your INI file
        Return (GetPrivateProfileInt(Section, Key,
       CInt([Default]), strFilename) = 1)
    End Function

    Public Sub WriteString(ByVal Section As String,
  ByVal Key As String, ByVal Value As String)
        ' Writes a string to your INI file
        WritePrivateProfileString(Section, Key, Value, strFilename)
        Flush()
    End Sub

    Public Sub WriteInteger(ByVal Section As String,
  ByVal Key As String, ByVal Value As Integer)
        ' Writes an integer to your INI file
        WriteString(Section, Key, CStr(Value))
        Flush()
    End Sub

    Public Sub WriteBoolean(ByVal Section As String,
  ByVal Key As String, ByVal Value As Boolean)
        ' Writes a boolean to your INI file
        WriteString(Section, Key, CStr(CInt(Value)))
        Flush()
    End Sub

    Private Sub Flush()
        ' Stores all the cached changes to your INI file
        FlushPrivateProfileString(0, 0, 0, strFilename)
    End Sub
End Class


Module DRUpdater
    Dim selfPath As String = System.AppDomain.CurrentDomain.BaseDirectory
    Dim selfUpdatePath As String = System.AppDomain.CurrentDomain.BaseDirectory.Replace("update\", "")
    Dim objIniFile As New clsIni(selfPath & "settings.ini")
    Dim serviceName As String = "DR Server.exe"
    Dim updaterName As String = "DR Updater.exe"

    Private Function startService(ByVal name As String) As String
        For Each s As ServiceController In ServiceController.GetServices()
            s.Refresh()

            If s.ServiceName = name Then
                If s.Status = ServiceControllerStatus.Stopped Then
                    s.Start()
                    s.WaitForStatus(ServiceControllerStatus.Running)
                    Return name
                End If
            End If
        Next
        Return "ERROR"
    End Function

    Private Function stopService(ByVal name As String) As String
        For Each s As ServiceController In ServiceController.GetServices()
            s.Refresh()
            If s.ServiceName = name Then
                If s.Status = ServiceControllerStatus.Running Then
                    s.Stop()
                    s.WaitForStatus(ServiceControllerStatus.Stopped)
                    Return name
                End If
            End If
        Next
        Return "ERROR"
    End Function
    Public Function deleteOldFiles(ByVal path As String)
        Dim fileList As String() = Directory.GetFiles(path, "*.old")

        For Each f As String In fileList
            File.Delete(f)
        Next

        Return "OK"
    End Function
    Sub Main()
        Dim REMOTE_UPDATE As String = objIniFile.GetString("MAIN", "REMOTE_UPDATE", "")
        Console.WriteLine("START DR UPDATER")
        Console.WriteLine("")
        Console.WriteLine("GET REMOTE FOLDER: " & REMOTE_UPDATE)
        Console.WriteLine("UPDATE PATH: " & selfUpdatePath)
        Console.WriteLine("")

        If (REMOTE_UPDATE.Length > 3 And Directory.Exists(REMOTE_UPDATE)) Then
            Console.WriteLine("PREAPARE UPDATE PLEASE WAIT...")
            Console.WriteLine("")

            stopService(serviceName)

            Dim fileList As String() = Directory.GetFiles(REMOTE_UPDATE, "*.*")

            For Each f As String In fileList
                Dim fName As String = f.Substring(REMOTE_UPDATE.Length)

                Dim old As String = selfUpdatePath & Now.TimeOfDay.TotalMilliseconds & ".old"

                Try
                    File.Move(Path.Combine(selfUpdatePath, fName), old)

                    Console.WriteLine("DELETE: " & old)
                Catch ex As Exception
                End Try
            Next

            Console.WriteLine("")

            For Each f As String In fileList
                Dim fName As String = f.Substring(REMOTE_UPDATE.Length)

                Console.WriteLine("COPY FILE: " & fName)

                Try
                    File.Copy(Path.Combine(REMOTE_UPDATE, fName), Path.Combine(selfUpdatePath, fName))
                Catch copyError As IOException
                    Console.WriteLine(copyError.Message)
                End Try
            Next

            startService(serviceName)
        Else
            Console.WriteLine("REMOTE PATH NOT FOUND!")
        End If

        deleteOldFiles(selfUpdatePath)

        stopService(updaterName)

        Console.WriteLine("")
        Console.WriteLine("UPDATE DONE")
        'Thread.Sleep(6000)

    End Sub

End Module
