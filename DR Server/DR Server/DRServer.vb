Imports MySql.Data.MySqlClient
Imports System.Net
Imports System.Net.Sockets
Imports System.Text
Imports System.Threading
Imports System.IO
Imports System.ServiceProcess
Module DRServer

    Dim conn As New MySqlConnection
    Const port As Integer = 55001
    Dim output As String = ""
    Dim sep As String = "------------------------------------------------------"

    Public Function GetComputerName() As String
        Dim ComputerName As String
        ComputerName = System.Net.Dns.GetHostName
        Return ComputerName
    End Function
    Private Function getCPU() As String
        Dim cpuUsage = New PerformanceCounter("Processor", "% Processor Time", "_Total")
        cpuUsage.NextValue()
        Thread.Sleep(1000)
        Return (cpuUsage.NextValue())
    End Function
    Public Function GetComputerIP() As IPAddress
        Dim ipAddress As IPAddress = System.Net.Dns.GetHostEntry("localhost").AddressList(0)
        Return ipAddress
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
    'Private Function getServices() As DataSet
    '    Dim query As String = "SELECT * FROM services"
    '    Dim cmd As New MySqlCommand()
    '    Dim serviceList As ArrayList

    '    Try
    '        'conn.Open()
    '        cmd.Connection = conn

    '        cmd.CommandText = query
    '        cmd.Prepare()

    '        Dim reader As MySqlDataReader = cmd.ExecuteReader()

    '        While reader.Read()
    '            serviceList.Add(reader.GetString(1))
    '        End While

    '        'conn.Close()

    '    Catch ex As MySqlException
    '        Console.WriteLine("Error: " & ex.ToString())
    '    End Try
    'End Function
    Public Sub insertData()
        Dim query As String = "INSERT IGNORE INTO dr(name, status) VALUES(@Name, @Status);"

        Dim cmd As New MySqlCommand()

        Try
            'conn.Open()
            cmd.Connection = conn

            cmd.CommandText = query
            cmd.Prepare()

            cmd.Parameters.AddWithValue("@Name", GetComputerName())
            cmd.Parameters.AddWithValue("@Status", "0")
            cmd.ExecuteNonQuery()

            'conn.Close()

        Catch ex As MySqlException
            Console.WriteLine("Error: " & ex.ToString())
        End Try
    End Sub
    Public Sub setData()
        Dim query As String = "UPDATE dr SET status=@Status, cpu=@Cpu WHERE name=@Name;"

        Dim cmd As New MySqlCommand()

        Try
            'conn.Open()
            cmd.Connection = conn

            cmd.CommandText = query
            cmd.Prepare()

            cmd.Parameters.AddWithValue("@Name", GetComputerName())
            cmd.Parameters.AddWithValue("@Status", "555")
            cmd.Parameters.AddWithValue("@Cpu", getCPU())

            cmd.ExecuteNonQuery()

            'conn.Close()

        Catch ex As MySqlException
            Console.WriteLine("Error: " & ex.ToString())
        End Try
    End Sub
    Private Function startService(ByVal name As String) As String
        Try
            Dim sc = New System.ServiceProcess.ServiceController(name)
            sc.Refresh()
            Try
                sc.Stop()
                sc.WaitForStatus(ServiceControllerStatus.Stopped)
            Catch
            End Try

            sc.Start()

                Return name
            Catch
                Return "Error"
        End Try

    End Function
    Private Function stopService(ByVal name As String) As String
        Try
            Dim sc = New System.ServiceProcess.ServiceController(name)
            sc.Refresh()
            sc.Stop()
            sc.WaitForStatus(ServiceControllerStatus.Stopped)

            Return "None"
        Catch
            Return "Error"
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
            'mstrMessage = Encoding.ASCII.GetString(bytesReceived, 0, bytesReceived.Length)
            mstrMessage = Encoding.UTF8.GetString(bytesReceived.ToArray(), 0, bytesReceived.Length).TrimEnd(Chr(0))
            mscClient = client
            'mstrMessage = mstrMessage.Substring(0, 3)

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
                    setData()
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
    Sub Main()
        connectDB()
        insertData()
        Console.WriteLine("DR SERVER")
        Console.WriteLine("")

        createListener()

    End Sub
End Module
