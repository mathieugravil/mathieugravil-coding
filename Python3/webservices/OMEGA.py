from suds.client import Client
from suds.transport.http import HttpAuthenticated
from datetime import timedelta, datetime, date, time
import random

import tkinter 
from tkinter import ttk
from tkinter import messagebox
from tkinter import *
from tkinter import ttk
from tkinter.messagebox import * # boîte de dialogue
from tkinter.ttk	import *	# Widgets avec thèmes




murl = 'http://omega.rm.corp.local/itg/ppmservices/DemandService?wsdl'

closed="Accept and Close"
reject="Rework"
validate="Validate"
reestimate="Restimate"
withdraw="Withdraw"
cancel="Cancel"


class Action(object):
    def __init__(self):
        self.root = tkinter.Tk()
        self.style = ttk.Style()
        available_actions = (closed,reject,validate,reestimate,cancel,withdraw)

        self.style.theme_use('alt')
        self.root.title('OMEGA')

        frm = ttk.Frame(self.root)
        frm.pack(expand=True, fill='both')
        #User Label    
        self.LUser = ttk.Label(frm, text='User')
        self.LUser.pack()
        # User Entry    
        self.User=StringVar()
        self.User.set("J0242224")
        self.user = ttk.Entry(frm, textvariable=self.User)
        
        self.user.pack()
        
        #Password Label    
        self.LPasswd = ttk.Label(frm, text='Password') 
        self.LPasswd.pack()
        # Password Entry    
        self.Passwd=StringVar()
        self.passwd = ttk.Entry(frm, textvariable=self.Passwd,show='*')
        self.passwd.insert(0,'SR0*b*nKHb')
        self.passwd.pack()
        
    #Ticket Label    
        self.LTicket = ttk.Label(frm, text='Ticket')
        self.LTicket.pack()
    # Ticket Entry    
        TicketNb=StringVar()
        self.ticket = ttk.Entry(frm, textvariable=TicketNb)
        self.ticket.pack()
    #Comments Label    
        self.LComments = ttk.Label(frm, text='Comments')
        self.LComments.pack()
    # Comments Entry    
        Comments=StringVar()
        self.comments = tkinter.Text(frm , width='50', height='5')
#       self.comments = ttk.Entry(frm, textvariable=Comments,width=50)
        self.comments.pack()
        #Action Label    
        self.LAction = ttk.Label(frm, text='Choose action')
        self.LAction.pack()
    # create a Combobox with themes to choose from
        self.combo = ttk.Combobox(frm, values=available_actions)
        self.combo.pack()
    # make the Enter key change the style
        self.combo.bind('<Return>', self.change_status)
    # make a Button to change the status
        CH_STATUS = ttk.Button(frm, text='Change Status')
        CH_STATUS['command'] = self.change_status
        CH_STATUS.pack()
    # make a Button to add comments
        AD_COMM = ttk.Button(frm, text='Add Comments')
        AD_COMM['command'] = self.add_comments
        AD_COMM.pack()    

    def change_status(self, event=None):
        action = self.combo.get()
        ticket = self.ticket.get()
        mIGG = self.user.get()
        mpasswd = self.passwd.get()
 
        if mIGG == '' or mpasswd == '':
            messagebox.showwarning(message="µUser or Password should not be Empty!")
        else:
            if ticket.isdigit() :
                if action == '':
                    messagebox.showwarning(message="Action should not be Empty!")
                else:
                    result=changeStatus(murl, mIGG, mpasswd, ticket, action)
                    messagebox.showinfo(message=result)
            else:
                messagebox.showwarning(message="Ticket "+ticket+" is not  an integer!")
        
    def add_comments(self, event=None):
        ticket = self.ticket.get()
        comments = self.comments.get('1.0', END)
        mIGG = self.user.get()
        mpasswd = self.passwd.get()
        if mIGG == '' or mpasswd == '':
            messagebox.showwarning(message="User or Password should not be Empty!")
        else:
            if ticket.isdigit() :
                if comments == '':
                    messagebox.showinfo(message="Comments should not be Empty!")
                else:
                    result=addNote(murl, mIGG, mpasswd, ticket, comments)
                    messagebox.showinfo(message=result)
            else:
                messagebox.showinfo(message="Ticket "+ticket+" is not  an integer!")
          
        





def addNote(url, IGG, passwd, ticketid, comments):
  t = HttpAuthenticated(username= IGG, password=passwd)
  client = Client(url, transport=t)
  requestid = client.factory.create("ns1:Identifier")
  requestid.id=ticketid
  mynotes = client.factory.create("ns0:Note")
  now=datetime.now()- timedelta(hours=1)
  cdate=now.strftime("%Y-%m-%dT%H:%M:%S")
  mynotes.content=comments
  mynotes.author = IGG
  mynotes.creationDate=cdate
  mystatus = client.factory.create("ns0:Note")
  try:
      result = client.service.addRequestNotes(requestid, mynotes)
  except:
      return "ERROR"
  else:
      print (result)
      return "OK"

  
def changeStatus(url, IGG, passwd, ticketid, status):
  t = HttpAuthenticated(username= IGG, password=passwd)
  client = Client(url, transport=t)
  requestid = client.factory.create("ns1:Identifier")
  requestid.id=ticketid
  try:
      result = client.service.executeWFTransitions(requestid, status)
  except:
      return "ERROR"
  else :
      print(result)
      return "OK"

def main():
    app = Action()
    app.root.mainloop()



if __name__ == "__main__":
    main()  
