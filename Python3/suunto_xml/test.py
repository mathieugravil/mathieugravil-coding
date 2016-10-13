import os
import xml.dom.minidom
import getopt
import sys
import xml.etree.ElementTree as ET
import sqlite3


def usage():
    print ("""
ambit2gpx [--suunto] [--noalti] [--altibaro] [--noext] filename
Creates a file filename.gpx in GPX format from filename in Suunto Ambit SML format.
If option --suunto is given, only retain GPS fixes retained by Suunto distance algorithm.
If option --noalti is given, elevation will be put to zero.
If option --altibaro is given, elevation is retrieved from altibaro information. The default is to retrieve GPS elevation information.
If option --noext is given, extended data (hr, temperature, cadence) will not generated. Useful for instance if size of output file matters.
""")

def xml2sqlite(filename, db_file):
    print(filename)
    cnx = sqlite3.connect(db_file)
    cursor = cnx.cursor()
    (rootfilename, ext) = os.path.splitext(filename)
    if (ext == ""):
        filename += ".sml"
    if (not os.path.exists(filename)):
        print (str(sys.stderr), "File {0} doesn't exist".format(filename))
        sys.exit()
#    file = open(filename,encoding="utf-8" )
#    file.readline() # Skip first line
#    filecontents = file.read()
#    file.close()

    print ("Parsing file {0}".format(filename))
#    doc = xml.dom.minidom.parseString('<?xml version="1.0" encoding="utf-8"?><top>'+filecontents+'</top>')
 #   assert doc != None
  #  top = doc.getElementsByTagName('top')
   # assert len(top) == 1
    #print ("Done.")

    dll_create='CREATE TABLE HEADER ( "id" '
    insert_cmd='INSERT INTO HEADER ("id" '
    dll_create1='CREATE TABLE SAMPLES ( "id" '
    insert_cmd1=''

    tree = ET.parse(filename)
    root = tree.getroot()

#    parser = ET.XMLParser(encoding="utf-8")

      
  

    #root = ET.fromstring('<Log>'+filecontents+'</Log>' , parser=parser )
    for child2 in root:
          print(child2.tag)
          if (child2.tag == 'Log'):
              for child in child2 :
                  if (child.tag == 'Header'):
                      header_dict={'Duration':0,'Ascent':0,'Descent':0,'AscentTime':0,'DescentTime':0,'RecoveryTime':0,'Speed Avg':0,'Speed Max':0,'Speed MaxTime':0,'Cadence Avg':0,'Cadence MaxTime':0,'Cadence Max':0,'Altitude Max':0,'Altitude Min':0,'Altitude MaxTime':0,'Altitude MinTime':0,'HR Avg':0,'HR Max':0,'HR Min':0,'HR MaxTime':0,'HR MinTime':0,'PeakTrainingEffect':0,'ActivityType':0,'Activity':0,'Temperature Max':0,'Temperature Min':0,'Temperature MaxTime':0,'Temperature MinTime':0,'Distance':0,'LogItemCount':0,'Energy':0,'TimeToFirstFix':0,'BatteryChargeAtStart':0,'BatteryCharge':0,'DistanceBeforeCalibrationChange':0,'DateTime':0 ,'Unknown1':0,'Unknown2':0,'Unknown3':0,'Unknown4':0,'Unknown5':0,'Unknown6':0}
                      for label in header_dict.keys():
                          dll_create = dll_create+', "'+str(label)+'"'
                          insert_cmd= insert_cmd+', "'+str(label)+'"'
                      dll_create = dll_create + ', PRIMARY KEY ("id"));'
                      print(dll_create)
                      try :
                          cursor.execute(str(dll_create))
                      except sqlite3.OperationalError as err:
                          print("table HEADER already exist. Only insert will be perfom")
                      cnx.commit()
                      for childheader in child:
                            if(len(childheader) ==  0 ):
                                print(childheader.tag,  childheader.text )
                                header_dict[childheader.tag] = childheader.text
                            else:
                                for child2 in childheader:
                                    if(len(child2) ==  0 ):
                                        header_dict[childheader.tag+" "+child2.tag]=child2.text
                                        print(childheader.tag, child2.tag,  child2.text )
                                        line=''
                      insert_cmd=insert_cmd+') VALUES ("'+str(filename)+'"'

                      for column in header_dict.values():
                          insert_cmd=insert_cmd+', "'+str(column)+'"'
                      insert_cmd=insert_cmd+');'
                      print(insert_cmd);
                      try :
                          cursor.execute(str(insert_cmd))
                      except sqlite3.IntegrityError as err:
                          print("Entry already exist. No insert will be perfom")
                      cnx.commit()
                               
                              # HR bat/s => x60.
                              # Speed m/s
                              # Duration in s
                              # Ascent in m
                              # Descent in m
                              # Temperature in Kelvin => T= T in  Kelvin -273.15
                              # UTC : YYYY-MM-DDTHH:mm:ssZ
                              # SampleType : gps-base : NavType , NavValid  , NavTypeExplanation , <Satellites>, GPSAltitude , GPSHeading , GPSSpeed , GpsHDOP , NumberOfSatellites , Latitude , Longitude , EHPE , Time , UTC
                              #              gps-small : NumberOfSatellites , Latitude , Longitude , EHPE , Time , UTC
                              #              gps-tiny : Latitude , Longitude , EHPE , Time , UTC
                              #              periodic : VerticalSpeed , HR , EnergyConsumption , Temperature , SeaLevelPressure , Altitude , Distance , Speed , Time , UTC
                              #
                          
                  elif ( child.tag == 'Samples'):
                      line_dict={'SampleType':0,'Unknown':0,'DistanceSource':0, 'TimeRef':0,'UTCReference':0,'VerticalSpeed':0 , 'HR':0 , 'EnergyConsumption':0 , 'Temperature':0 , 'SeaLevelPressure':0 , 'Altitude':0 , 'Distance':0 , 'Speed':0 ,'NavType':0 , 'NavValid':0  , 'NavTypeExplanation':0 , 'GPSAltitude':0 , 'GPSHeading':0 , 'GPSSpeed':0 , 'GpsHDOP':0 , 'NumberOfSatellites':0 , 'Latitude':0 , 'Longitude':0 , 'EHPE':0  ,'Time':0 ,'UTC':0 ,'Data':0,'Type':0,'RuleOutput1':0,'Cadence':0,'IBI':0}
                      
                      insert_cmd1=insert_cmd1+'INSERT INTO SAMPLES ("id" '
                      for label in line_dict.keys():
                          dll_create1 = dll_create1+', "'+str(label)+'"'
                          insert_cmd1= insert_cmd1+', "'+str(label)+'"'
                      dll_create1 = dll_create1 + ');'
                      #, PRIMARY KEY ("id", "UTC","SampleType"));'
                      print(dll_create1)
                      try :
                          cursor.execute(str(dll_create1))
                      except sqlite3.OperationalError as err:
                          print("table SAMPLES already exist. Only insert will be perfom")
                      cnx.commit()
                      
                      insert_cmd1=insert_cmd1+') VALUES ("'+str(filename)+'"'
                      all_insert=insert_cmd1
                      single_insert=''
                      for Sample in child:
                          
                          line_dict={'SampleType':0 , 'Unknown':0, 'DistanceSource':0,'TimeRef':0,'UTCReference':0,'VerticalSpeed':0 , 'HR':0 , 'EnergyConsumption':0 , 'Temperature':0 , 'SeaLevelPressure':0 , 'Altitude':0 , 'Distance':0 , 'Speed':0 ,'NavType':0 , 'NavValid':0  , 'NavTypeExplanation':0 , 'GPSAltitude':0 , 'GPSHeading':0 , 'GPSSpeed':0 , 'GpsHDOP':0 , 'NumberOfSatellites':0 , 'Latitude':0 ,    'Longitude':0 , 'EHPE':0  ,'Time':0 ,'UTC':0,'Data':0,'Type':0,'RuleOutput1':0,'Cadence':0,'IBI':0}
                          if (len(Sample)>1):
                              for childSample in Sample:
                                  if (len(childSample)== 0 ):
                                      line_dict[childSample.tag]= str(childSample.text)
                                 #     print(childSample.tag +":"+ str(childSample.text))

                          for cle, column in line_dict.items():
                              print(cle+":"+str(column ))
                              if  single_insert == '':
                                  single_insert=insert_cmd1+', "'+str(column)+'"'
                              else:
                                  single_insert=single_insert+ ', "'+str(column)+'"'
                          
                          single_insert=single_insert+');'
                          print(single_insert)
                          all_insert=all_insert+single_insert
                          try :
                              cursor.execute(str(single_insert))
                          except sqlite3.IntegrityError as err:
                              print("Entry already exist. No insert will be perfom")
                          cnx.commit()
                          single_insert=''
           # print(all_insert)
            
    cursor.close()
    cnx.close()


def xml2csv(filename):
    print(filename)
    (rootfilename, ext) = os.path.splitext(filename)
    if (ext == ""):
        filename += ".sml"
    if (not os.path.exists(filename)):
        print (str(sys.stderr), "File {0} doesn't exist".format(filename))
        sys.exit()
    file = open(filename)
    file.readline() # Skip first line
    filecontents = file.read()
    file.close()

    print ("Parsing file {0}".format(filename))
#    doc = xml.dom.minidom.parseString('<?xml version="1.0" encoding="utf-8"?><top>'+filecontents+'</top>')
 #   assert doc != None
  #  top = doc.getElementsByTagName('top')
   # assert len(top) == 1
    #print ("Done.")

    outputfilename = rootfilename+ '.csv'
    outputfile = open(outputfilename, 'w')
    print ("Creating file {0}".format(outputfilename))

    root = ET.fromstring('<?xml version="1.0" encoding="utf-8"?><top>'+filecontents+'</top>')
    for child in root:
          if (child.tag == 'header'):
              header_dict={'Duration':0,'Ascent':0,'Descent':0,'AscentTime':0,'DescentTime':0,'RecoveryTime':0,'Speed Avg':0,'Speed Max':0,'Speed MaxTime':0,'Cadence MaxTime':0,'Altitude Max':0,'Altitude Min':0,'Altitude MaxTime':0,'Altitude MinTime':0,'HR Avg':0,'HR Max':0,'HR Min':0,'HR MaxTime':0,'HR MinTime':0,'PeakTrainingEffect':0,'ActivityType':0,'Activity':0,'Temperature Max':0,'Temperature Min':0,'Temperature MaxTime':0,'Temperature MinTime':0,'Distance':0,'LogItemCount':0,'Energy':0,'TimeToFirstFix':0,'BatteryChargeAtStart':0,'BatteryCharge':0,'DistanceBeforeCalibrationChange':0,'DateTime':0}
              headers=''
              
              for label in header_dict.keys():
                  if headers == '':
                      headers=label
                  else:
                      headers=headers+";"+label
              outputfile.write(headers+"\n")
              for childheader in child:                  
                  if(len(childheader) ==  0 ):
                      print(childheader.tag,  childheader.text )
                      header_dict[childheader.tag] = childheader.text
                  #    outputfile.write(childheader.tag+";"+childheader.text+"\n")
                  else:
                      for child2 in childheader:
                          if(len(child2) ==  0 ):
                              header_dict[childheader.tag+" "+child2.tag]=child2.text
                              print(childheader.tag, child2.tag,  child2.text )
                   #           outputfile.write(childheader.tag+" "+child2.tag+";"+child2.text+"\n")
              line=''              
              for column in header_dict.values():
                  if line == '':
                      line=str(column)
                  else:
                      line=line+";"+str(column)
              outputfile.write(line+"\n")                       
                      # HR bat/s => x60.
                      # Speed m/s
                      # Duration in s
                      # Ascent in m
                      # Descent in m
                      # Temperature in Kelvin => T= T in  Kelvin -273.15
                      # UTC : YYYY-MM-DDTHH:mm:ssZ
                      # SampleType : gps-base : NavType , NavValid  , NavTypeExplanation , <Satellites>, GPSAltitude , GPSHeading , GPSSpeed , GpsHDOP , NumberOfSatellites , Latitude , Longitude , EHPE , Time , UTC
                      #              gps-small : NumberOfSatellites , Latitude , Longitude , EHPE , Time , UTC
                      #              gps-tiny : Latitude , Longitude , EHPE , Time , UTC
                      #              periodic : VerticalSpeed , HR , EnergyConsumption , Temperature , SeaLevelPressure , Altitude , Distance , Speed , Time , UTC
                      #
                  
          elif ( child.tag == 'Samples'):
              line_dict={'SampleType':0,'VerticalSpeed':0 , 'HR':0 , 'EnergyConsumption':0 , 'Temperature':0 , 'SeaLevelPressure':0 , 'Altitude':0 , 'Distance':0 , 'Speed':0 ,'NavType':0 , 'NavValid':0  , 'NavTypeExplanation':0 , 'GPSAltitude':0 , 'GPSHeading':0 , 'GPSSpeed':0 , 'GpsHDOP':0 , 'NumberOfSatellites':0 , 'Latitude':0 , 'Longitude':0 , 'EHPE':0  ,'Time':0 ,'UTC':0}
              headers=''
              for label in line_dict.keys():
                  if headers == '':
                      headers=label
                  else:
                      headers=headers+";"+label
              outputfile.write(headers+"\n")
              for Sample in child:
                  line_dict={'SampleType':0,'VerticalSpeed':0 , 'HR':0 , 'EnergyConsumption':0 , 'Temperature':0 , 'SeaLevelPressure':0 , 'Altitude':0 , 'Distance':0 , 'Speed':0 ,'NavType':0 , 'NavValid':0  , 'NavTypeExplanation':0 , 'GPSAltitude':0 , 'GPSHeading':0 , 'GPSSpeed':0 , 'GpsHDOP':0 , 'NumberOfSatellites':0 , 'Latitude':0 , 'Longitude':0 , 'EHPE':0  ,'Time':0 ,'UTC':0}
                  if (len(Sample)>1):
                      for childSample in Sample:
                          if (len(childSample)== 0 ):
                              line_dict[childSample.tag]= childSample.text
                  line=''
                  for column in line_dict.values():                     
                      if line == '':
                          line=str(column)
                      else:
                          line=line+";"+str(column)
                  #print(line)
                  outputfile.write(line+"\n")
    outputfile.close
    


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
      #for o, a in opts:
      
      filename = args[0]
      db_file =  args[1]

      for file in os.listdir(filename):
            xml2sqlite(str(filename)+'/'+str(file),db_file)
      #xml2csv(filename)      
  

if __name__ == "__main__":
    main()
