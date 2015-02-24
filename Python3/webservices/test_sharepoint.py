import urllib.request, urllib.error, urllib.parse
from ntlm import HTTPNtlmAuthHandler
from suds.client import Client
from suds import transport
from suds.transport.http import HttpAuthenticated
from suds.transport.https import WindowsHttpAuthenticated
#from suds.xsd.doctor import ImportDoctor, Import
#from suds.cache import NoCache
#from suds.cache import ObjectCache
#from suds.sax.parser import Parser
from suds.sax.element import Element
from suds.sax.attribute import Attribute
import logging

proxyOpts = dict()

#imp = Import('http://www.w3.org/2001/XMLSchema')    # the schema to import.
#imp.filter.add('http://schemas.microsoft.com/sharepoint/soap/')  # the schema to import into.
#d = ImportDoctor(imp)
proxyOpts = {'http': '10.30.39.243:8080'}




murl='http://sharepoint/_vti_bin/Lists.asmx?wsdl'
user='DOMAIN\user'
passwd='mypaswd'
listName='CC1BFE7F-07A5-458E-8163-22A8AEDD6CC9'
viewName='A014C29D-C4F5-411E-B9E7-A854A8426BC7'


logging.basicConfig(level=logging.INFO)
logging.getLogger('suds.client').setLevel(logging.DEBUG)
#logging.getLogger('suds.transport').setLevel(logging.DEBUG)
#logging.getLogger('suds.xsd.schema').setLevel(logging.DEBUG)
#logging.getLogger('suds.wsdl').setLevel(logging.DEBUG)
#logging.getLogger('suds.sax').setLevel(logging.DEBUG)



def extractHTML(url, user, passwd):
  passman = urllib.request.HTTPPasswordMgrWithDefaultRealm()
  passman.add_password(None, url, user, passwd)
  auth_NTLM = HTTPNtlmAuthHandler.HTTPNtlmAuthHandler(passman)
  opener = urllib.request.build_opener(auth_NTLM)
  urllib.request.install_opener(opener)
  response = urllib.request.urlopen(url)
  f = open('c:\Python34\wsdl.xml', 'wb')
  pagetext = response.read()
  f.write(pagetext)
  f.close()


t = WindowsHttpAuthenticated(username=user, password=passwd, proxy=proxyOpts)

client = Client(murl, transport=t  )

#print (client)
# see https://msdn.microsoft.com/en-us/library/lists.lists.getlistitems(v=office.12).aspx
viewFields = Element('ViewFields')
viewFields.append(Element('FieldRef').append(Attribute('Name','OMEGA_TICKET')))

print(viewFields)
result=client.service.GetListItems(listName,viewName,viewFields)
#print(result)
#client.service.GetListItems(xs:string listName, xs:string viewName, query query, viewFields viewFields,
#                            xs:string rowLimit, queryOptions queryOptions, xs:string webID)


