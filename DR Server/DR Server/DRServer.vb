Imports System.Net
Imports System.Net.Sockets
Imports System.Text
Imports System.Threading
Imports System.IO
Imports System.ServiceProcess
Imports System.Timers
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
Module DRServer

    Const port As Integer = 55001
    Dim busyTime = 60 'min
    Dim output As String = String.Empty
    Dim serviceStatus As String
    Dim sep As String = "------------------------------------------------------"
    Dim cpuLoad As String = 0
    Dim cpuCount As Int16 = 0
    Dim busyCnt As Int16 = 0
    Dim BACKBURNERSRV As String = ""
    Dim objIniFile As New clsIni(System.AppDomain.CurrentDomain.BaseDirectory + "\settings.ini")
    Dim URL As String = String.Empty

    Dim cpuUsage = New PerformanceCounter("Processor", "% Processor Time", "_Total")
    Private Function sendWebRequest(url As String, Data As String)
        Dim request As HttpWebRequest = DirectCast(WebRequest.Create(url), HttpWebRequest)

        request.Method = "POST"
        request.KeepAlive = True

        Dim byteData As Byte() = Encoding.UTF8.GetBytes(Data)
        request.ContentType = "application/json"

        request.ContentLength = byteData.Length

        Dim dataStream As Stream = request.GetRequestStream()

        dataStream.Write(byteData, 0, byteData.Length)
        dataStream.Close()
        Dim responseFromServer As String = "OK"
        Try
            Dim response As WebResponse = request.GetResponse()
            dataStream = response.GetResponseStream()
            Dim reader As New StreamReader(dataStream)
            responseFromServer = reader.ReadToEnd()
            If (responseFromServer.Count = 0) Then
                responseFromServer = "ERROR"
            End If
            reader.Close()
            response.Close()
        Catch
            responseFromServer = "ERROR"
        End Try

        dataStream.Close()

        Return responseFromServer
    End Function
    Public Function GetComputerName() As String
        Dim ComputerName As String
        ComputerName = System.Net.Dns.GetHostName
        Return ComputerName
    End Function
    Public Function GetComputerIP() As String
        Dim GetIPv4Address As String = String.Empty
        Dim strHostName As String = System.Net.Dns.GetHostName()
        Dim iphe As System.Net.IPHostEntry = System.Net.Dns.GetHostEntry(strHostName)

        For Each ipheal As System.Net.IPAddress In iphe.AddressList
            If ipheal.AddressFamily = System.Net.Sockets.AddressFamily.InterNetwork Then
                GetIPv4Address = ipheal.ToString()
            End If
        Next

        Return GetIPv4Address
    End Function
    Private Function getServices() As ArrayList
        Dim services As New ArrayList
        Dim Resp As String = ""
        Resp = sendWebRequest(URL & "exeGetServices.php", "{""get"":""services""}")
        If (Resp = "ERROR") Then
            Return services
        End If

        Dim s As String() = Resp.Split(New Char() {";"c})
        For Each i In s
            Try
                Dim sc = New System.ServiceProcess.ServiceController(i)
                sc.Refresh()
                serviceStatus &= i & "=" & sc.Status & ";"
                services.Add(i)
            Catch ex As Exception
                serviceStatus &= i & "=notfound" & ";"
            End Try
        Next

        Return services
    End Function
    Public Function insertData()
        Dim Data As String = "{""ip"":""" & GetComputerIP() & """, ""name"":""" & GetComputerName() & """}"

        Return sendWebRequest(URL & "exeInsertData.php", Data)
    End Function
    Public Function setData(Optional ByVal isBackBurener As Int16 = 0)
        serviceStatus = String.Empty
        getServices()
        Dim user As String = "NO"

        If (isBackBurener = 1) Then user = "BackBurner"
        If (isBackBurener = 2) Then user = "CLEAR"

        Dim Data As String = "{""ip"":""" & GetComputerIP() & """, ""name"":""" & GetComputerName() & """, ""cpu"":""" & cpuLoad & """, ""service"":""" & serviceStatus & """, ""user"":""" & user & """}"

        Return sendWebRequest(URL & "exeSetData.php", Data)
    End Function
    Public Sub stopAllServices()
        Dim sevices As ArrayList = getServices()
        sevices.Add(BACKBURNERSRV)

        For Each srv In sevices
            Dim scs = New System.ServiceProcess.ServiceController(srv)
            scs.Refresh()
            Try
                scs.Stop()
                scs.WaitForStatus(ServiceControllerStatus.Stopped)
            Catch
            End Try
        Next
        ' Kill 3Ds Max
        For Each prog As Process In Process.GetProcesses
            If prog.ProcessName = "3dsmax" Then
                Console.WriteLine(prog.ProcessName)
                prog.Kill()
            End If
        Next

    End Sub
    Private Function startService(ByVal name As String) As String
        stopAllServices()
        Try
            Dim sc = New System.ServiceProcess.ServiceController(name)
            sc.Start()
            sc.WaitForStatus(ServiceControllerStatus.Running)

            setData()
            Return name
        Catch
            Return "ERROR"
        End Try

    End Function
    Private Function rebootNode() As String
        Dim shutdown As New System.Diagnostics.ProcessStartInfo("shutdown.exe")

        shutdown.Arguments = "/f /r /t 000"

        System.Diagnostics.Process.Start(shutdown)

        Return "OK"
    End Function
    Private Function stopService(ByVal name As String) As String
        Try
            Dim sc = New System.ServiceProcess.ServiceController(name)
            sc.Refresh()
            sc.Stop()
            sc.WaitForStatus(ServiceControllerStatus.Stopped)
            setData()
            Return "NONE"
        Catch
            Return "ERROR"
        End Try

    End Function
    Class SocketHelper
        Private mscClient As TcpClient
        Private mstrMessage As String
        Private mstrResponse As String
        Private bytesSent() As Byte

        Public Sub processMsg(ByVal client As TcpClient, ByVal stream As NetworkStream, ByVal bytesReceived() As Byte)
            ' Handle the message received and 
            ' send a response back to the client.            
            mstrMessage = Encoding.UTF8.GetString(bytesReceived.ToArray(), 0, bytesReceived.Length).TrimEnd(Chr(0))
            mscClient = client
            Dim Data As String = setData()
            mstrResponse = "ERROR"

            Dim cmds As String() = mstrMessage.Split(New Char() {":"c})

            Select Case cmds(0)
                Case "STARTSERVICE"
                    Console.WriteLine("START SERVICE: {1}", cmds)
                    mstrResponse = startService(cmds(1))
                Case "STOPSERVICE"
                    Console.WriteLine("STOP SERVICE: {1}", cmds)
                    mstrResponse = stopService(cmds(1))
                Case "CHALLANGE"
                    Console.WriteLine("CHALLANGE")
                    mstrResponse = setData()
                Case "REBOOT"
                    Console.WriteLine("REBOOT")
                    setData()
                    mstrResponse = rebootNode()
                Case "DROPNODE"
                    Console.WriteLine("DROPNODE")

                    mstrResponse = rebootNode()
                Case "EXIT"
                    Console.WriteLine("EXIT")
                    Environment.Exit(0)
                    mstrResponse = "OK"
            End Select

            Console.WriteLine(mstrResponse)
            Console.WriteLine(sep)
            bytesSent = Encoding.UTF8.GetBytes(mstrResponse)
            stream.Write(bytesSent, 0, bytesSent.Length)

        End Sub
    End Class
    Public Sub createListener()
        ' Create an instance of the TcpListener class.
        Dim tcpListener As TcpListener = Nothing
        Dim ipAddress As IPAddress = IPAddress.Parse("127.0.0.1")

        Try
            ' Set the listener on the local IP address.
            ' and specify the port.
            tcpListener = New TcpListener(IPAddress.Any, port)

            tcpListener.Start()
            output = "WAITING FOR A CONNECTION..."
        Catch e As Exception
            output = "Error: " + e.ToString()
        End Try
        Console.WriteLine(output)
        Console.WriteLine(sep)

        While True
            ' Always use a Sleep call in a while(true) loop
            ' to avoid locking up your CPU.
            Thread.Sleep(100)
            ' Create a TCP socket.
            ' If you ran this server on the desktop, you could use
            ' Socket socket = tcpListener.AcceptSocket()
            ' for greater flexibility.

            Dim tcpClient As TcpClient = tcpListener.AcceptTcpClient()

            ' Read the data stream from the client.
            Dim bytes(255) As Byte
            Dim stream As NetworkStream = tcpClient.GetStream()
            stream.Read(bytes, 0, bytes.Length)
            Dim helper As New SocketHelper()
            Dim ID As String = CType(tcpClient.Client.RemoteEndPoint, IPEndPoint).ToString()
            Console.WriteLine("CLIENT CONNECTED: {0}", ID)
            helper.processMsg(tcpClient, stream, bytes)
            tcpClient.Close()

        End While

    End Sub
    WithEvents cpuTimer As New System.Timers.Timer
    WithEvents busyTimer As New System.Timers.Timer
    Private Sub tick(ByVal sender As Object, ByVal e As System.Timers.ElapsedEventArgs) Handles cpuTimer.Elapsed
        If cpuCount = 1 Then
            cpuLoad = Int(cpuUsage.NextValue().ToString).ToString

            cpuCount = 0
        Else
            cpuCount += 1
        End If
    End Sub
    Private Sub startBackBurner()
        For Each s As ServiceController In ServiceController.GetServices()
            If s.ServiceName = BACKBURNERSRV Then
                If s.Status = ServiceControllerStatus.Stopped Then
                    Console.WriteLine("SET NODE IN FREE")
                    Console.WriteLine(sep)

                    stopAllServices()
                    s.Start()
                End If
            End If
        Next
        setData(2)
        busyCnt += 1
    End Sub
    Private Sub setNodeBusy()
        For Each s As ServiceController In ServiceController.GetServices()
            If s.ServiceName = BACKBURNERSRV Then
                If s.Status = ServiceControllerStatus.Running Then
                    setData(1)
                End If
            End If
        Next

        busyCnt = 0
    End Sub
    Private Sub tickBusy(ByVal sender As Object, ByVal e As System.Timers.ElapsedEventArgs) Handles busyTimer.Elapsed
        busyTime = Int(objIniFile.GetString("MAIN", "BUSYTIME", ""))

        If Int(cpuLoad) < 60 And busyCnt > busyTime Then ' If Free
            startBackBurner()
        Else
            setNodeBusy()
        End If
    End Sub
    Sub Main()
        ' SETTINGS
        URL = objIniFile.GetString("MAIN", "URL", "")
        URL = URL & "vault/exe/"
        BACKBURNERSRV = objIniFile.GetString("MAIN", "BACKBURNER", "")
        busyTime = Int(objIniFile.GetString("MAIN", "BUSYTIME", ""))

        ' TIMERS
        cpuTimer.Interval = 1000
        AddHandler cpuTimer.Elapsed, AddressOf tick
        cpuTimer.Start()

        busyTimer.Interval = 60 * 1000 '1 minute
        AddHandler busyTimer.Elapsed, AddressOf tickBusy
        busyTimer.Start()

        ' SET FIRST INFO
        insertData()

        ' CONSOLE LOG
        Console.WriteLine("START NODE SERVER")
        Console.WriteLine("")
        Console.WriteLine("SET REMOTE URL: " & URL)
        Console.WriteLine("SET BACKBURNER SERVICE: " & BACKBURNERSRV)
        Console.WriteLine("SET BUSYTIME: " & busyTime & " MIN")
        Console.WriteLine("")

        ' SOCKET LISTENER
        createListener()
    End Sub
End Module
