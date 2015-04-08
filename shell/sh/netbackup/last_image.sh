#http://www.symantec.com/business/support/index?page=content&id=TECH5584
echo "Client;Policy;date;sizeKB;nbfiles">>lastimage.txt
for i in `cat policy_list.txt`
do
bpimagelist -l -d 03/01/14 00:00:00  -policy $i | grep IMAGE | awk '{ print $2 ";" $7 ";" $14 ";" $19 ";" $20 }' >>lastimage.txt
done