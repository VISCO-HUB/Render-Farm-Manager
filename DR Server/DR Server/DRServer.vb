Imports System
Imports System.Net
Imports System.Net.Sockets
Imports System.Text
Imports System.Threading
Imports System.IO
Imports System.ServiceProcess
Imports System.Text.RegularExpressions

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
    Dim globalSettings As Int64()
    Dim output As String = String.Empty
    Dim serviceStatus As String
    Dim sep As String = "------------------------------------------------------"
    Dim cpuLoad As String = 0
    Dim cpuLoad3dmax As String = 0
    Dim cpuCount As Int16 = 0
    Dim busyCnt As Int16 = 0
    Dim BACKBURNERSRV As String = ""
    Dim UPDATERATE As Int32 = 3
    Dim DEBUG As Int32 = 0
    Dim cpuNumber As Int32 = Convert.ToInt32(Environment.ProcessorCount.ToString)

    Dim objIniFile As New clsIni(System.AppDomain.CurrentDomain.BaseDirectory + "\settings.ini")
    Dim URL As String = String.Empty
    Dim servicesList As String = String.Empty

    Dim cpuUsage As New System.Diagnostics.PerformanceCounter("Processor", "% Processor Time", "_Total")
    Dim cpuUsage3dmax As New System.Diagnostics.PerformanceCounter("Process", "% Processor Time", "3dsmax")

    Class Log
        Public Shared Sub Write(logMessage As String)
            DEBUG = objIniFile.GetInteger("MAIN", "DEBUG", 0)

            If (DEBUG = 1) Then
                Using w As StreamWriter = File.AppendText("log/" & (DateTime.Now.ToString("yyyy-MM-dd")) & ".txt")
                    w.WriteLine("{0} [DR SERVER] >  {1}", DateTime.Now.ToLocalTime(), logMessage)
                End Using
            End If
        End Sub
    End Class

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
    Private Function sendWebGetReques(url As String)
        Dim resp As String = "ERROR"
        Try
            resp = New System.Net.WebClient().DownloadString(url)
            Log.Write("WEB REQUEST: " & url)
        Catch
        End Try
        Log.Write("WEB REQUEST RESPONCE: " & resp)
        Return resp
    End Function
    Public Function GetComputerName() As String
        Dim ComputerName As String
        ComputerName = System.Net.Dns.GetHostName
        Return ComputerName
    End Function
    Public Function GetCpuData() As String

        Dim s As String = CreateObject("WScript.Shell").RegRead("HKEY_LOCAL_MACHINE\HARDWARE\DESCRIPTION\System\CentralProcessor\0\ProcessorNameString")
        s = Regex.Replace(s, "[^a-z0-9@\s\(\)-.]", "", RegexOptions.IgnoreCase)
        s = s.Replace("\n", "")
        s = s.Replace("  ", "")
        Return s

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
    Public Function GetRam() As String
        Return (System.Math.Round(My.Computer.Info.TotalPhysicalMemory / (1024 * 1024 * 1024), 1)).ToString()
    End Function
    Public Function GetFreeRam() As String
        Return (System.Math.Round(My.Computer.Info.AvailablePhysicalMemory / (1024 * 1024 * 1024), 1)).ToString()
    End Function
    Private Function getServicesList() As String

        servicesList = sendWebGetReques(URL & "exeGetServices.php")

        Return servicesList
    End Function
    Private Function getServices(Optional ByVal isUpdate As Int16 = 0) As ArrayList
        Dim services As New ArrayList

        If (servicesList = "") Then
            getServicesList()
        End If

        Dim s As String() = servicesList.Split(New Char() {";"c})
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
        Dim getString As String = "ip=" & GetComputerIP() & "&name=" & GetComputerName() & "&ram=" & GetRam()
        Return sendWebGetReques(URL & "exeInsertData1.php?" & getString)
    End Function
    Public Function getUser()
        Dim getString As String = "ip=" & GetComputerIP()
        Return sendWebGetReques(URL & "exeGetUser.php?" & getString)
    End Function
    Public Function getGlobal()
        Dim r As String = sendWebGetReques(URL & "exeGetGlobal.php?ip=" & GetComputerIP())

        Dim s As String() = r.Split(New Char() {"|"c})
        Dim o(3) As Int64
        o(0) = If(s(0) = "1", 1, 0)
        o(1) = If(s(1) IsNot "", s(1), 120)
        o(2) = If(s(2) = "0", 0, 1)
        o(3) = If(s(3) = "0", 0, 1)
        Return o
    End Function
    Public Function dropNode()
        Dim getString As String = "ip=" & GetComputerIP()

        Return sendWebGetReques(URL & "exeDropNode.php?" & getString)
    End Function
    Public Function setData(Optional ByVal isBackBurener As Int16 = 0)
        serviceStatus = String.Empty
        getServices()
        Dim user As String = "NONE"

        If (isBackBurener = 1) Then user = "BackBurner"

        'Dim Data As String = "{""ip"":""" & GetComputerIP() & """, ""name"":""" & GetComputerName() & """, ""cpu"":""" & cpuLoad & """, ""service"":""" & serviceStatus & """, ""user"":""" & user & """}"

        Dim getString As String = "ip=" & GetComputerIP() & "&name=" & GetComputerName() & "&cpu=" & cpuLoad3dmax & "&3dsmax=" & cpuLoad3dmax & "&cpunumber=" & cpuNumber & "&service=""" & serviceStatus & """" & "&user=" & user & "&ram=" & GetRam() & "&aram=" & GetFreeRam() & "&cpudata=""" & GetCpuData() & """"

        Return sendWebGetReques(URL & "exeSetData1.php?" & getString)
        'Return sendWebRequest(URL & "exeSetData.php", Data)
    End Function

    Public Sub stopAllServices(Optional ByVal closeMax As Int16 = 1)
        Dim sevices As ArrayList = getServices()

        sevices.Add(BACKBURNERSRV)

        'For Each srv In sevices

        '    Dim scs = New System.ServiceProcess.ServiceController(srv)
        '    scs.Refresh()
        '    Try
        '        scs.Stop()
        '        scs.WaitForStatus(ServiceControllerStatus.Stopped)
        '    Catch
        '    End Try
        'Next
        For Each srv In sevices
            For Each s As ServiceController In ServiceController.GetServices()
                s.Refresh()
                If s.ServiceName = srv Then
                    If s.Status = ServiceControllerStatus.Running Then
                        s.Stop()
                        s.WaitForStatus(ServiceControllerStatus.Stopped)
                    End If
                End If
            Next
        Next

        If (closeMax = 1) Then
            ' Kill 3Ds Max
            For Each prog As Process In Process.GetProcesses
                If prog.ProcessName = "3dsmax" Then
                    Try
                        prog.Kill()
                    Catch
                    End Try
                End If
            Next
        End If
    End Sub
    Private Function startService(ByVal name As String) As String
        stopAllServices()
        'Try
        '    Dim sc = New System.ServiceProcess.ServiceController(name)
        '    sc.Start()
        '    sc.WaitForStatus(ServiceControllerStatus.Running)

        '    setData()
        '    Return name
        'Catch
        '    Return "ERROR"
        'End Try
        'Return "ERROR"

        For Each s As ServiceController In ServiceController.GetServices()
            If s.ServiceName = name Then
                If s.Status = ServiceControllerStatus.Stopped Then
                    s.Start()
                    Return name
                End If
            End If
        Next
        Return "ERROR"
    End Function
    Private Function rebootNode() As String
        Dim shutdown As New System.Diagnostics.ProcessStartInfo("shutdown.exe")

        shutdown.Arguments = "/f /r /t 000"

        System.Diagnostics.Process.Start(shutdown)

        Return "OK"
    End Function
    'Private Function dropNode() As String
    '    stopAllServices()
    '    Return startService(BACKBURNERSRV)
    'End Function
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
            'Dim Data As String = setData()
            mstrResponse = "ERROR"

            Dim cmds As String() = mstrMessage.Split(New Char() {":"c})
            If globalSettings(0) = 1 Then
                Select Case cmds(0)
                    Case "STARTSERVICE"
                        busyCnt = 0
                        Console.WriteLine("START SERVICE: {1}", cmds)
                        Log.Write("START SERVICE: " & cmds(1))
                        mstrResponse = startService(cmds(1))
                        setData()
                    Case "STOPSERVICE"
                        Console.WriteLine("STOP SERVICE: {1}", cmds)
                        Log.Write("STOP SERVICE: " & cmds(1))
                        mstrResponse = stopService(cmds(1))
                    Case "STOPSERVICES"
                        Console.WriteLine("STOP ALL SERVICES")
                        Log.Write("STOP ALL SERVICES")
                        stopAllServices()
                        mstrResponse = "OK"
                    Case "CHALLANGE"
                        Console.WriteLine("CHALLANGE")
                        Log.Write("CHALLANGE")
                        mstrResponse = setData()
                    Case "DROP"
                        mstrResponse = dropNode()
                        setData()
                        Console.WriteLine("DROPNODE")
                        Log.Write("DROPNODE")
                    Case "REBOOT"
                        Console.WriteLine("REBOOT")
                        Log.Write("REBOOT")
                        setData()
                        mstrResponse = rebootNode()
                    Case "EXIT"
                        Console.WriteLine("EXIT")
                        Log.Write("EXIT")
                        Environment.Exit(0)
                        mstrResponse = "OK"
                End Select
            End If

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
            Log.Write("CLIENT CONNECTED: " & ID)
            helper.processMsg(tcpClient, stream, bytes)
            tcpClient.Close()

        End While

    End Sub
    WithEvents cpuTimer As New System.Timers.Timer
    WithEvents busyTimer As New System.Timers.Timer
    WithEvents setDataTimer As New System.Timers.Timer
    Private Sub tick(ByVal sender As Object, ByVal e As System.Timers.ElapsedEventArgs) Handles cpuTimer.Elapsed
        If cpuCount = 1 Then
            Try
                cpuLoad = Int(cpuUsage.NextValue().ToString).ToString
            Catch
                cpuLoad = "0"
            End Try

            Try
                cpuLoad3dmax = (Int(cpuUsage3dmax.NextValue() / cpuNumber)).ToString
            Catch
                cpuLoad3dmax = "0"
            End Try

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
                    s.WaitForStatus(ServiceControllerStatus.Running)
                End If
            End If
        Next
    End Sub
    Private Sub setNodeBusy()
        For Each s As ServiceController In ServiceController.GetServices()
            If s.ServiceName = BACKBURNERSRV Then
                If s.Status = ServiceControllerStatus.Running Then
                    setData(1)
                End If
            End If
        Next
    End Sub
    Dim timeStamp2 As Long = DateTime.Now.Ticks
    Private Sub tickBusy(ByVal sender As Object, ByVal e As System.Timers.ElapsedEventArgs) Handles busyTimer.Elapsed
        If (DateTime.Now.Ticks - timeStamp2 > 10) Then
            globalSettings = getGlobal()

            If (globalSettings(0) = 1 And globalSettings(2) = 0) Then
                Dim user As String = getUser()
                If Int(cpuLoad3dmax) < 2 Then ' If Free

                    If busyCnt >= globalSettings(1) Or user = "null" Then
                        If globalSettings(3) = 1 Then
                            startBackBurner()
                        Else
                            stopAllServices(0)
                        End If

                        Log.Write("SET NODE IN FREE (" & busyCnt.ToString() & " >= " & globalSettings(1).ToString() & " USER: " & user & ")")

                        dropNode()
                        busyCnt = 0
                    End If
                    busyCnt += 1
                Else
                    setNodeBusy()
                    busyCnt = 0
                End If
            End If
        End If
        timeStamp2 = DateTime.Now.Ticks
    End Sub
    Dim timeStamp1 As Long = DateTime.Now.Ticks
    Private Sub tickSetData(ByVal sender As Object, ByVal e As System.Timers.ElapsedEventArgs) Handles setDataTimer.Elapsed
        If (DateTime.Now.Ticks - timeStamp1 > 1) Then
            setData()
        End If
        timeStamp1 = DateTime.Now.Ticks
    End Sub
    Sub Main()

        ' SETTINGS
        URL = objIniFile.GetString("MAIN", "URL", "")
        URL = URL & "vault/exe/"
        BACKBURNERSRV = objIniFile.GetString("MAIN", "BACKBURNER", "")
        UPDATERATE = objIniFile.GetInteger("MAIN", "UPDATERATE", 3)

        ' SET FIRST INFO
        insertData()

        globalSettings = getGlobal()
        getServicesList()

        ' TIMERS
        cpuTimer.Interval = 1000
        AddHandler cpuTimer.Elapsed, AddressOf tick
        cpuTimer.Start()

        setDataTimer.Interval = UPDATERATE * 1000
        AddHandler setDataTimer.Elapsed, AddressOf tickSetData
        setDataTimer.Start()

        busyTimer.Interval = 60 * 1000 '1 minute
        AddHandler busyTimer.Elapsed, AddressOf tickBusy
        busyTimer.Start()

        ' CONSOLE LOG
        Console.WriteLine("START NODE SERVER")
        Console.WriteLine("")
        Console.WriteLine("SET REMOTE URL: " & URL)
        Console.WriteLine("SET BACKBURNER SERVICE: " & BACKBURNERSRV)
        Console.WriteLine("SET BUSYTIME: " & globalSettings(1) & " MIN")
        Console.WriteLine("SET UPDATERATE: " & UPDATERATE & " SEC")
        Console.WriteLine("AUTO START BACKBURNER: " & (If(globalSettings(3) = 1, "YES", "NO")))
        Console.WriteLine("GET SERVICE LIST: " & servicesList)
        Console.WriteLine("WEB SERVICE IS: " & (If(globalSettings(0) = 1, "ONLINE", "OFFLINE")))
        Console.WriteLine("NODE STATUS IS: " & (If(globalSettings(2) = 0, "ONLINE", "OFFLINE")))
        Console.WriteLine("NODE IP: " & GetComputerIP())
        Console.WriteLine("NODE CPU: " & GetCpuData())
        Console.WriteLine("NODE RAM: " & GetRam() & " GB")
        Console.WriteLine("NODE AVAILABLE RAM: " & GetFreeRam() & " GB")
        Console.WriteLine("")

        Log.Write("-------------------")
        Log.Write("RUN SERVICE")
        Log.Write("-------------------")
        Log.Write("SET REMOTE URL: " & URL)
        Log.Write("SET BACKBURNER SERVICE: " & BACKBURNERSRV)
        Log.Write("SET BUSYTIME: " & globalSettings(1) & " MIN")
        Log.Write("SET UPDATERATE: " & UPDATERATE & " SEC")
        Log.Write("AUTO START BACKBURNER: " & (If(globalSettings(3) = 1, "YES", "NO")))
        Log.Write("GET SERVICE LIST: " & servicesList)
        Log.Write("WEB SERVICE IS: " & (If(globalSettings(0) = 1, "ONLINE", "OFFLINE")))
        Log.Write("NODE STATUS IS: " & (If(globalSettings(2) = 0, "ONLINE", "OFFLINE")))
        Log.Write("NODE IP: " & GetComputerIP())
        Log.Write("NODE CPU: " & GetCpuData())
        Log.Write("NODE RAM: " & GetRam() & " GB")
        Log.Write("NODE AVAILABLE RAM: " & GetFreeRam() & " GB")
        Log.Write("-------------------")

        insertData()

        ' SOCKET LISTENER
        createListener()
    End Sub
End Module
