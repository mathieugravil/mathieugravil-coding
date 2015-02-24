
import socket


HOST="t2jserv"
PORT = 8000
s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
s.connect((HOST, PORT))
data = s.recv(1024)
s.close()
print ('Received'+data.decode())

