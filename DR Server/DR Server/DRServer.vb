Imports MySql.Data.MySqlClient
Imports System.Net
Imports System.Net.Sockets
Imports System.Text
Imports System.Threading
Module DRServer

    Dim conn As New MySqlConnection
    Const port As Integer = 8000
    Dim output As String = ""

    Public Function GetComputerName() As String
        Dim ComputerName As String
        ComputerName = System.Net.Dns.GetHostName
        Return ComputerName
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
        Dim query As String = "UPDATE dr SET status=@Status WHERE name=@Name;"

        Dim cmd As New MySqlCommand()

        Try
            'conn.Open()
            cmd.Connection = conn

            cmd.CommandText = query
            cmd.Prepare()

            cmd.Parameters.AddWithValue("@Name", GetComputerName())
            cmd.Parameters.AddWithValue("@Status", "555")
            cmd.ExecuteNonQuery()

            'conn.Close()

        Catch ex As MySqlException
            Console.WriteLine("Error: " & ex.ToString())
        End Try
    End Sub
    Class SocketHelper
        Private mscClient As TcpClient
        Private mstrMessage As String
        Private mstrResponse As String
        Private bytesSent() As Byte

        Public Sub processMsg(ByVal client As TcpClient, ByVal stream As NetworkStream, ByVal bytesReceived() As Byte)
            ' Handle the message received and 
            ' send a response back to the client.
            mstrMessage = Encoding.ASCII.GetString(bytesReceived, 0, bytesReceived.Length)
            mscClient = client
            mstrMessage = mstrMessage.Substring(0, 5)
            Console.WriteLine(mstrMessage)
            If mstrMessage.Equals("Hello") Then
                mstrResponse = "Goodbye"
                Console.WriteLine("Client connected!")
            Else
                mstrResponse = "What?"
                Console.WriteLine("Unrecognized command type!")
            End If
            bytesSent = Encoding.ASCII.GetBytes(mstrResponse)
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
            tcpListener = New TcpListener(ipAddress, 13)
            tcpListener.Start()
            output = "Waiting for a connection..."
        Catch e As Exception
            output = "Error: " + e.ToString()
        End Try
        Console.WriteLine(output)
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
            helper.processMsg(tcpClient, stream, bytes)
        End While

    End Sub
    Sub Main()
        'connectDB()
        'insertData()
        Console.WriteLine("DR Server")
        createListener()


    End Sub

End Module
