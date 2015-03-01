import os
import xml.dom.minidom
import getopt
import sys
import xml.etree.ElementTree as ET
#import cElementTree as ElementTree

class XmlListConfig(list):
    def __init__(self, aList):
        for element in aList:
            if element:
                # treat like dict
                if len(element) == 1 or element[0].tag != element[1].tag:
                    self.append(XmlDictConfig(element))
                # treat like list
                elif element[0].tag == element[1].tag:
                    self.append(XmlListConfig(element))
            elif element.text:
                text = element.text.strip()
                if text:
                    self.append(text)


class XmlDictConfig(dict):
    '''
    Example usage:

    >>> tree = ElementTree.parse('your_file.xml')
    >>> root = tree.getroot()
    >>> xmldict = XmlDictConfig(root)

    Or, if you want to use an XML string:

    >>> root = ElementTree.XML(xml_string)
    >>> xmldict = XmlDictConfig(root)

    And then use xmldict for what it is... a dict.
    '''
    def __init__(self, parent_element):
        if parent_element.items():
            self.update(dict(parent_element.items()))
        for element in parent_element:
            if element:
                # treat like dict - we assume that if the first two tags
                # in a series are different, then they are all different.
                if len(element) == 1 or element[0].tag != element[1].tag:
                    aDict = XmlDictConfig(element)
                # treat like list - we assume that if the first two tags
                # in a series are the same, then the rest are the same.
                else:
                    # here, we put the list in dictionary; the key is the
                    # tag name the list elements all share in common, and
                    # the value is the list itself 
                    aDict = {element[0].tag: XmlListConfig(element)}
                # if the tag has attributes, add those to the dict
                if element.items():
                    aDict.update(dict(element.items()))
                self.update({element.tag: aDict})
            # this assumes that if you've got an attribute in a tag,
            # you won't be having any text. This may or may not be a 
            # good idea -- time will tell. It works for the way we are
            # currently doing XML configuration files...
            elif element.items():
                self.update({element.tag: dict(element.items())})
            # finally, if there are no child tags and no attributes, extract
            # the text
            else:
                self.update({element.tag: element.text})


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
    file.readline() # Skip first line
    filecontents = file.read()
    file.close()

    print ("Parsing file {0}".format(filename))
    
    
    print ("Done.")

    outputfilename = rootfilename+ '.csv'
    outputfile = open(outputfilename, 'w')
    print ("Creating file {0}".format(outputfilename))

         

    root = ET.fromstring('<?xml version="1.0" encoding="utf-8"?><top>'+filecontents+'</top>')
    xmldict = XmlDictConfig(root)
    #dict_keys(['openambitlog'])
    #dict_keys(['SerialNumber', 'Time', 'Log', 'DeviceInfo', 'version', 'PersonalSettings', 'MovescountId'])
    #dict_keys(['Samples', 'Header'])
    #dict_keys(['Descent', 'Altitude', 'Activity', 'PeakTrainingEffect', 'LogItemCount', 'DistanceBeforeCalibrationChange', 'RecoveryTime', 'Unknown2', 'Unknown3', 'Energy', 'ActivityType',
    #'BatteryChargeAtStart', 'Unknown4', 'Unknown5', 'Ascent', 'Unknown6', 'DateTime', 'Cadence', 'Duration', 'DescentTime', 'Distance', 'Speed', 'Temperature', 'Unknown1', 'AscentTime', 'HR', 'TimeToFirstFix', 'BatteryCharge'])

    #print(xmldict['Header']['HR']['Avg'])      
    print(xmldict['openambitlog']['Log']['Header']['HR'].keys())      
                      # HR bat/s => 'x60.
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
