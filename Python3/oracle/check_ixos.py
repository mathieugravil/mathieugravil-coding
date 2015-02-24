import cx_Oracle
import mysql.connector
import fileinput
from datetime import datetime
import sys
import re
import os

def main():
  arc='UD'
  SID='P3B'
  os.environ['PATH'] = os.environ['PATH'] + r" ;c:\Program Files (x86)\Oracle\OraCli112\bin"
  os.environ['ORACLE_HOME'] = r"c:\Program Files (x86)\Oracle\OraCli112"

    
  filename="result_"+SID+"_"+arc+".log"
  fic = open (filename, "a")
  sql_cnx = mysql.connector.connect(user='J0242224', password='Spir@u38', host='localhost',database='cmdb', buffered=True)
  sql_cursor = sql_cnx.cursor()
  sql_cursor1 = sql_cnx.cursor()
  sql_query = ( "SELECT doc FROM test20140908, arch_sid WHERE  test20140908.arch=arch_sid.arch AND SID = %s and arch_sid.arch = %s  and is_prod is null   ")
  sql_cursor.execute(sql_query,(SID, arc))
  ora_con = cx_Oracle.connect('J0242224/J0242224@10.126.115.52:1500/P3B')
  ora_cur1 = ora_con.cursor()
  ora_cur1.prepare ('select distinct(CONNECTION) from SAPSR3.TOAOM WHERE ARCHIV_ID= :rarc')
  ora_cur1.execute(None,{'rarc' : arc })
  ora_cur = ora_con.cursor()
  #for table in ora_cur1:
  for table in [ 'TOA01' , 'TOA02' , 'TOA03' ]:
    print(str(table))
    schema_table="select count(*) FROM SAPSR3."+str(table)
    for   doc  in sql_cursor :
      ora_query=schema_table+" WHERE ARC_DOC_ID = :rdoc"
 #     print(ora_query)
      ora_cur.prepare (ora_query)
      ora_cur.execute(None,{ 'rdoc' : doc[0] })
      ora_row=ora_cur.fetchone()
  
      sql_update = ( "UPDATE test20140908 set is_prod= %s , table_sap = %s , nb_doc = %s WHERE doc = %s ")
      if ( int(ora_row[0]) > 0):
        sql_cursor1.execute(sql_update,  ( "1" , str(table[0]), ora_row[0],  str(doc[0])))
      else:
        sql_cursor1.execute(sql_update,  ( "0" , str(table[0]), ora_row[0],  str(doc[0])))
      sql_cnx.commit()



  sql_cursor.close()
  sql_cursor1.close()
  sql_cnx.close()
  ora_cur.close()
  ora_cur1.close()
  ora_con.close()
  
  






if __name__ == "__main__":
  main()

