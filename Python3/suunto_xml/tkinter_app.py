import matplotlib
matplotlib.use("TkAgg")

from matplotlib.backends.backend_tkagg import FigureCanvasTkAgg, NavigationToolbar2TkAgg
from matplotlib.figure import Figure

import tkinter as tk
from tkinter import ttk

LARGE_FONT=("Verdana",12)


class MyApp(tk.Tk):

  def __init__(self,*args,**kwargs):
    tk.Tk.__init__(self,*args , **kwargs)
    tk.Tk.wm_title(self,'TEST')
    #tk.Tk.iconbitmap(self, default="MGava.bmp")
    container = tk.Frame(self)
    container.pack(side="top",fill="both",expand=True)
    container.grid_rowconfigure(0,weight=1) # minsize, priority
    container.grid_columnconfigure(0,weight=1)

    self.frames = {}
    for F in (StartPage, PageOne, PageTwo , PageThree): 
      frame = F(container,self)
      self.frames[F] = frame
      frame.grid(row=0, column=0, sticky="nsew") # sticky expand on all direction

    self.show_frame(StartPage)

  def show_frame(self, cont):
    frame = self.frames[cont]
    frame.tkraise()
    


class StartPage(tk.Frame):
  def __init__(self, parent, controller):
    tk.Frame.__init__(self, parent)
    label = tk.Label(self, text="Start Page", font=LARGE_FONT)
    label.pack(pady=10,padx=10)
    button1 = ttk.Button(self, text="Visit Page1",
                        command=lambda: controller.show_frame(PageOne))
    button1.pack()
    button2 = ttk.Button(self, text="Visit Page2",
                        command=lambda: controller.show_frame(PageTwo))
    button2.pack()
    button3 = ttk.Button(self, text="Visit Page3",
                        command=lambda: controller.show_frame(PageThree))
    button3.pack()
class PageOne(tk.Frame):
  def __init__(self, parent, controller):
    tk.Frame.__init__(self, parent)
    label = tk.Label(self, text="Page One", font=LARGE_FONT)
    label.pack(pady=10,padx=10)
    button = ttk.Button(self, text="Back",
                        command=lambda: controller.show_frame(StartPage))
    button.pack()
    button2 = ttk.Button(self, text="Visit Page2",
                        command=lambda: controller.show_frame(PageTwo))
    button2.pack()

class PageTwo(tk.Frame):
  def __init__(self, parent, controller):
    tk.Frame.__init__(self, parent)
    label = tk.Label(self, text="Page Two", font=LARGE_FONT)
    label.pack(pady=10,padx=10)
    button = ttk.Button(self, text="Back",
                        command=lambda: controller.show_frame(StartPage))
    button.pack()
    button1 = ttk.Button(self, text="Visit Page1",
                        command=lambda: controller.show_frame(PageOne))
    button1.pack()

class PageThree(tk.Frame):
  def __init__(self, parent, controller):
    tk.Frame.__init__(self, parent)
    label = tk.Label(self, text="Page THree", font=LARGE_FONT)
    label.pack(pady=10,padx=10)
    button = ttk.Button(self, text="Back",
                        command=lambda: controller.show_frame(StartPage))
    button.pack()

    f = Figure(figsize=(5,5), dpi=100)
    a = f.add_subplot(111)
    a.plot([1,2,3,4,5,6],[3,4,7,1,5,7])

    canvas = FigureCanvasTkAgg(f,self)
    canvas.show()
    canvas.get_tk_widget().pack(side=tk.TOP,fill=tk.BOTH, expand = True)

    toolbar= NavigationToolbar2TkAgg(canvas,self)
    toolbar.update()
    canvas._tkcanvas.pack(side=tk.TOP,fill=tk.BOTH, expand = True)

    

app = MyApp()
app.mainloop()
