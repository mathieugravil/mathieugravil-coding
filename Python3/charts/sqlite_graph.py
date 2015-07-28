import sqlite3
import time
import datetime
import getopt
import sys
import numpy as np
import matplotlib.pyplot as plt
import matplotlib.dates as mdates


#select substr(DateTime,1,7),Activity, round(sum(Duration)) from HEADER group by substr(DateTime,1,7),Activity ;
def usage():
    print ("""
ambit2gpx [--suunto] [--noalti] [--altibaro] [--noext] filename
Creates a file filename.gpx in GPX format from filename in Suunto Ambit SML format.
If option --suunto is given, only retain GPS fixes retained by Suunto distance algorithm.
If option --noalti is given, elevation will be put to zero.
If option --altibaro is given, elevation is retrieved from altibaro information. The default is to retrieve GPS elevation information.
If option --noext is given, extended data (hr, temperature, cadence) will not generated. Useful for instance if size of output file matters.
""")


def query2piechart(query, db_file,Title, mainfig,nrows, ncols,plot_nb):
    try:
        cnx = sqlite3.connect(db_file)
        cursor = cnx.cursor()
        cursor.execute(query)
        rows = cursor.fetchall()
        label=[]
        data=[]
        for row in rows:
            label.append(row[0])
            data.append(row[1])
        piechart= mainfig.add_subplot(nrows, ncols,plot_nb)    
        piechart.pie(data,labels=label, autopct='%1.1f%%', shadow=True, startangle=90)
        
    except sqlite3.Error as err:
        print("ERROR SQLITE")
        sys.exit(1)    

def query2graphbar(query, db_file, labelx,labely,mainfig,nrows, ncols,plot_nb):
    try:
        cnx = sqlite3.connect(db_file)
        cursor = cnx.cursor()
        cursor.execute(query)
        rows = cursor.fetchall()
        x_data=[]
        y_data=[]
        for row in rows:
            x_data.append(row[0])
            y_data.append(row[1])
            print(row)
        ind = np.arange(len(rows))
        width = 0.35
        barchart = mainfig.add_subplot(nrows, ncols,plot_nb)
        barchart.bar(ind,y_data, width,color='r')
        plt.ylabel(labely)
        plt.xticks(ind+width/2., x_data )
      
    except sqlite3.Error as err:
        print("ERROR SQLITE")
        sys.exit(1)
    

def query2graphline(query , db_file, labelx, labely):
    try:
        cnx = sqlite3.connect(db_file)
        cursor = cnx.cursor()
        cursor.execute(query)
        rows = cursor.fetchall()
        x_data=[]
        y_data=[]
        plt.plot(rows)
        plt.show()
    except sqlite3.Error as err:
        print("ERROR SQLITE")
        sys.exit(1)


            


def main():
  try:
        opts, args = getopt.getopt(sys.argv[1:], "ha", ["help", "suunto", "noalti", "altibaro", "noext"])
  except getopt.GetoptError as err:
    print (str(err)) # will print something like "option -a not recognized"
    usage()
    sys.exit(2)
  if len(sys.argv[1:]) == 0:
    usage()
    sys.exit(2)
  db_file = args[0]
  #query='select Activity, round(sum(Duration)) FROM HEADER GROUP BY Activity '
  #query2graphbar(query, db_file,'Activity' ,'Duration(s)')
  #query2='select replace(substr(DateTime,1,7),"-",""), round(sum(Duration)) from HEADER group by replace(substr(DateTime,1,7),"-","") '
  #query2graphbar(query2 , db_file, 'date', 'Duration(s)')
  query='select Activity, round(sum(Duration)) FROM HEADER GROUP BY Activity '
  nrows = 2
  ncols = 1     

  fig = plt.figure()

        
  query2piechart(query, db_file,'Distribution of Activities (Duration)', fig,nrows, ncols,1)
  plt.title('Report')
  query2graphbar(query, db_file,'Activity' ,'Duration(s)',fig,nrows, ncols,2)
  
  plt.show()



if __name__ == "__main__":
    main()
