import csv
import mysql.connector
from datetime import datetime
import re

def create_table_from_csv(filename,tablename,schema, file_cmd):
  spamReader = csv.reader(open(filename, newline='\n'), delimiter=';')
  fic = open (str(file_cmd), "a")
  i=0
  max_lenght= []
  typ = []
  label = []
  cnx = mysql.connector.connect(user='J0242224', password='Spir@u38', host='localhost',database='cmdb')
  cursor = cnx.cursor()
  for row in spamReader:
    if ( i == 0):
      for k in range(len(row)):
        if len(row[k]) != 0 :
          max_lenght.append(0)
        typ.append("int")
        label.append(row[k])
      print( "nb champ = "+str(len(max_lenght)))
    else:
      for j in range(len(max_lenght)):
        if len(row[j]) >  max_lenght[j] :
          max_lenght[j]=len(row[j])
        if ( typ[j] != "varchar" ):
          try:
            float(row[j])
          except ValueError:
            typ[j]="varchar"
          else:
            if ( typ[j] == "int" ):
              try:
                int(row[j])
              except ValueError:
                typ[j]="float"
              else:
                typ[j]="int"
          if  ( typ[j] == "varchar" ):
            try:
              datetime.strptime(row[j],'%d/%m/%Y')
            except:
              typ[j]="varchar"
            else:
              typ[j]="datetime"
    i+=1
  print("Nb ligne = "+str(i))
  DLL="CREATE TABLE `"+schema+"`.`"+tablename+"` (`id"+str(tablename)+"` INT NOT NULL AUTO_INCREMENT,"
  for l in range(len(max_lenght)):
    DLL=DLL+"`"+str(label[l])+"` "+str(typ[l])+"("+str(max_lenght[l])+"),\n"
  DLL=DLL+" PRIMARY KEY (`id"+str(tablename)+"`));"
  print(str(DLL))
  cursor.execute(str(DLL))
  cursor.close()
  cnx.close()
  fic.write(str(DLL)+"\n")
  fic.close

def insertfromcsv(schema,tablename,filename,header,file_cmd):
  spamReader = csv.reader(open(filename, newline='\n'), delimiter=';')
  i=0
  fic = open (str(file_cmd), "a")
  cnx = mysql.connector.connect(user='J0242224', password='Spir@u38', host='localhost',database='cmdb')
  cursor = cnx.cursor()
  INSERT_CMD=""
  for row in spamReader:
    if ( i == 0 and header == "Y"):
      print( "On prend pas le header : nb champ = "+str(len(row)))
    else:
      for j in range(len(row)):
        INSERT_CMD=INSERT_CMD+", '"+str(row[j]).replace("'", r"\'" )+"'"
      INSERT_CMD=INSERT_CMD+" );"
    i+=1
##    print(str(INSERT_CMD));
    fic.write(str(INSERT_CMD)+"\n")
    cursor.execute(str(INSERT_CMD))
    INSERT_CMD="INSERT INTO `"+schema+"`.`"+tablename+"` VALUE ( null "
  fic.close
  cursor.close()
  cnx.close()



def main():
  file_cmd="commad.sql"
  create_table_from_csv("ONLY_ON_BUFFERS_DOCSTR.csv","DOC","cmdb",file_cmd)
  insertfromcsv("cmdb","DOC","ONLY_ON_BUFFERS_DOCSTR.csv","Y",file_cmd)


if __name__ == "__main__":
  main()
