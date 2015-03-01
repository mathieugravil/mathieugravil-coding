import os
import xml.dom.minidom
import getopt
import sys
import xml.etree.ElementTree as ET


def usage():
    print ("""
ambit2gpx [--suunto] [--noalti] [--altibaro] [--noext] filename
Creates a file filename.gpx in GPX format from filename in Suunto Ambit SML format.
If option --suunto is given, only retain GPS fixes retained by Suunto distance algorithm.
If option --noalti is given, elevation will be put to zero.
If option --altibaro is given, elevation is retrieved from altibaro information. The default is to retrieve GPS elevation information.
If option --noext is given, extended data (hr, temperature, cadence) will not generated. Useful for instance if size of output file matters.
""")

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
          if (child.tag == 'Header'):
              
              for childheader in child:                  
                  if(len(childheader) ==  0 ):
                      print(childheader.tag,  childheader.text )
                      outputfile.write(childheader.tag+";"+childheader.text+"\n")
                  else:
                      for child2 in childheader:
                          if(len(child2) ==  0 ):
                              print(childheader.tag, child2.tag,  child2.text )
                              outputfile.write(childheader.tag+" "+child2.tag+";"+childheader.text)
                       
                      # HR bat/s => x60.
                      # Speed m/s
                      # Duration in s
                      # Ascent in m
                      # Descent in m
                      # Temperature in Kelvin => T°= T in  Kelvin -273.15
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
      xml2csv(filename)
      
  

if __name__ == "__main__":
    main()
