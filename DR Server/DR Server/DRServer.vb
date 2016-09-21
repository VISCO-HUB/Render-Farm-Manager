Imports MySql.Data.MySqlClient
Imports System.Net
Imports System.Net.Sockets
Imports System.Text
Imports System.Threading
Imports System.IO
Imports System.ServiceProcess
Imports System.Timers

Module DRServer

    Dim conn As New MySqlConnection
    Const port As Integer = 55001
    Dim busyTime = 60 'min
    Dim output As String = String.Empty
    Dim serviceStatus As String
    Dim sep As String = "------------------------------------------------------"
    Dim cpuLoad As String = 0
    Dim cpuCount As Int16 = 0
    Dim busyCnt As Int16 = 0
    Dim BACKBURNERSRV As String = "BACKBURNER_SRV_200"

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

        Dim response As WebResponse = request.GetResponse()
        dataStream = response.GetResponseStream()
        Dim reader As New StreamReader(dataStream)
        Dim responseFromServer As String = reader.ReadToEnd()
        Console.WriteLine(responseFromServer)
        reader.Close()
        dataStream.Close()
        response.Close()

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
    Public Sub connectDB()
        Dim DatabaseName As String = "dr_manager"
        Dim server As String = "svg-web-002"
        Dim userName As String = "dr_manager"
        Dim password As String = "!Genius!"
        If Not conn Is Nothing Then conn.Close()

        conn.ConnectionString = String.Format("server={0}; user id={1}; password={2}; database={3}; pooling=false", server, userName, password, DatabaseName)

        Dim cmd As New MySqlCommand

        Try
            conn.Open()
        Catch ex As Exception
            MsgBox(ex.Message)
        End Try

        'conn.Close()
    End Sub
    Private Function getServices() As ArrayList
        Dim query As String = "SELECT * FROM services"
        Dim cmd As New MySqlCommand()
        Dim services As New ArrayList

        Try
            'conn.Open()
            cmd.Connection = conn

            cmd.CommandText = query

            Dim reader As MySqlDataReader = cmd.ExecuteReader()

            While reader.Read()
                Dim srv As String = reader.GetString(1)
                If srv IsNot Nothing Then
                    Try
                        Dim sc = New System.ServiceProcess.ServiceController(srv)
                        sc.Refresh()
                        serviceStatus &= srv & "=" & sc.Status & ";"
                        services.Add(srv)
                    Catch ex As Exception
                        serviceStatus &= srv & "=notfound" & ";"
                    End Try
                End If
            End While

            reader.Close()
            'conn.Close()
        Catch ex As MySqlException
            Console.WriteLine("Error: " & ex.ToString())
        End Try

        Return services
    End Function
    Public Sub insertData()
        Dim query As String = "INSERT IGNORE INTO dr(name, status, ip) VALUES(@Name, @Status, @Ip);"

        Dim cmd As New MySqlCommand()

        Try
            'conn.Open()
            cmd.Connection = conn

            cmd.CommandText = query
            cmd.Prepare()

            cmd.Parameters.AddWithValue("@Name", GetComputerName())
            cmd.Parameters.AddWithValue("@Status", "0")
            cmd.Parameters.AddWithValue("@Ip", GetComputerIP())

            cmd.ExecuteNonQuery()

            'conn.Close()

        Catch ex As MySqlException
            Console.WriteLine("Error: " & ex.ToString())
        End Try
    End Sub
    Public Sub setData(Optional ByVal isBackBurener As Int16 = 0)
        serviceStatus = String.Empty
        getServices()

        Dim query As String = "UPDATE dr SET status=@Status, cpu=@Cpu, services=@Services, ip=@Ip WHERE name=@Name"
        If (isBackBurener > 0) Then query = query & ", user=@User"

        Dim cmd As New MySqlCommand()

        Try
            ' conn.Open()
            cmd.Connection = conn

            cmd.CommandText = query
            cmd.Prepare()

            cmd.Parameters.AddWithValue("@Name", GetComputerName())
            cmd.Parameters.AddWithValue("@Status", "0")
            cmd.Parameters.AddWithValue("@Cpu", cpuLoad)
            cmd.Parameters.AddWithValue("@Services", serviceStatus)
            cmd.Parameters.AddWithValue("@Ip", GetComputerIP())
            If (isBackBurener = 1) Then cmd.Parameters.AddWithValue("@User", "BackBurner")
            If (isBackBurener = 2) Then cmd.Parameters.AddWithValue("@User", "null")

            cmd.ExecuteNonQuery()

            'conn.Close()
        Catch ex As MySqlException
            Console.WriteLine("Error: " & ex.ToString())
        End Try
    End Sub
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

                    sendWebRequest("http://viscocg.com/dr/vault/test.php", "{""msg"":""HELLOW WORLD""}")
                    setData()
                    mstrResponse = "OK"
                Case "REBOOT"
                    Console.WriteLine("REBOOT")
                    setData()
                    mstrResponse = rebootNode()
                Case "DROPNODE"
                    Console.WriteLine("DROPNODE")
                    setData()
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
    Private Sub tickBusy(ByVal sender As Object, ByVal e As System.Timers.ElapsedEventArgs) Handles busyTimer.Elapsed
        If Int(cpuLoad) < 60 And busyCnt > busyTime Then ' If Free

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
        Else
            For Each s As ServiceController In ServiceController.GetServices()
                If s.ServiceName = BACKBURNERSRV Then
                    If s.Status = ServiceControllerStatus.Running Then
                        setData(1)
                    End If
                End If
            Next

            busyCnt = 0
        End If
    End Sub
    Sub Main()
        cpuTimer.Interval = 1000
        AddHandler cpuTimer.Elapsed, AddressOf tick
        cpuTimer.Start()

        busyTimer.Interval = 60 * 1000 '1 minute
        AddHandler busyTimer.Elapsed, AddressOf tickBusy
        busyTimer.Start()

        connectDB()
        insertData()
        Console.WriteLine("DR SERVER")
        Console.WriteLine("")

        createListener()
    End Sub
End Module
