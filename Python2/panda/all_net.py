import os
import pandas as pd


df = []
df1 = []
for f in os.listdir('c://local/DB/Mar2018RVTOOLS'):
    xlsx = pd.ExcelFile('c://local/DB/Mar2018RVTOOLS//'+f)
    data = pd.read_excel(xlsx, 'vNetwork',  usecols=[0,1,3,4,5,6,8,10] )
    data.index = [os.path.basename(f)[:-5]] * len(data)
    df.append(data)
    data1 = pd.read_excel(xlsx, 'dvPort',  usecols=[0,4] )
    data1.index = [os.path.basename(f)[:-5]] * len(data1)
    df1.append(data1)
    data2 = pd.read_excel(xlsx, 'vPort',  usecols=[3,5] )
    data2.index = [os.path.basename(f)[:-5]] * len(data2)
    df1.append(data2)
df = pd.concat(df)
df1 = pd.concat(df1)

with pd.ExcelWriter('c:\\local\\DB\\allLAN.xlsx') as writer:
    df.to_excel(writer, sheet_name='net')
    df1.to_excel(writer, sheet_name='port')



