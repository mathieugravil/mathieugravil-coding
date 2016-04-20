#!/bin/ksh




ok_ko() {
  if [[ $1 -eq 0 ]]  
then 
	echo "OK"
else
    echo "KO"; exit 5
  fi
}

search_primary() {
SVCADMIN=${DSLPATH}/SCRIPTS/ADMIN/SERVICES/svcadmin
   $SSH -i $sshkey_master_node $master_node "ksh $SVCADMIN status NET" > /dev/null
   if [[ $? -eq 1 ]]; then
     primary_node=$master_node
     primary_key=$sshkey_master_node
     secondary_node=$aux_node
     secondary_key=$sshkey_aux_node
  elif [[ $? -eq 0 ]]; then
     primary_node=$aux_node
     primary_key=$sshkey_aux_node
     secondary_node=$master_node
     secondary_key=$sshkey_master_node
  else
  echo "ERROR"
   exit 5	 
 fi
}





if [[ $# -ne 1 ]]
then 
echo "I need one parameter. I expect name of directory which contain conf file in DSL depot"
exit 5
else


sid=`echo $1 | tr "[:upper:]" "[:lower:]" `
DSLPATH=`dirname $0`
#`echo $( cd -P -- "$(dirname -- "$(command -v -- "$0")")" && pwd -P )`

BASE=/opt/ManCluster/5.0
CONF="${DSLPATH}/CONFIG/$1/"

. ${CONF}/profile.config
. ${DSLPATH}/SCRIPTS/etc/global.config
is_asm=$(echo ${ASM}  | tr "[:lower:]" "[:upper:]")

search_primary

if [[ -d $BASE ]]
then
echo "# Suppression of existing directory :"
rm -rf $BASE
ok_ko $?
fi

mkdir -p ${BASE}

echo "# Installation of Scripts :"
cp -rp ${DSLPATH}/SCRIPTS/*  ${BASE}/ 
ok_ko $?

echo "# Creation of symbolic links :"
compteur1=10
compteur2=90

for res in ${RESSOURCES_LIST}
do

ln -s $BASE/SERVICES/CIDB/init.d/${res} $BASE/SERVICES/CIDB/rc3.d/S${compteur1}$res
ok_ko $?
compteur1=$(echo $compteur1 + 10 | bc)
if [[ "$res" != "ORACLE"  && "$res" != "LISTENER"  && "$res" != "ASM" ]] ; then
ln -s $BASE/SERVICES/CIDB/init.d/${res} $BASE/SERVICES/CIDB/rc0.d/K${compteur2}$res
ok_ko $?
compteur2=$(echo $compteur2 - 10 | bc)
fi
done



echo "# Installation of configurtation files :"
cp -rp  $CONF/*  $BASE/SERVICES/CIDB/etc/
ok_ko $?


echo "# Chown: "
chown -R root:root $BASE
ok_ko $?

echo "# Chmod : "
chmod -R a+wx $BASE
ok_ko $?


echo "# Installation on the secondary node :"
tar -cvf /tmp/HCluster.tar $BASE 
scp -Cr -i $secondary_key /tmp/HCluster.tar $secondary_node:/tmp/
cmd1=`echo "rm -rf " ${BASE}`
cmd2=`echo "cd / ; tar -mxvf /tmp/HCluster.tar" `

ssh -nq -i $secondary_key $secondary_node $cmd1
ok_ko $?

ssh -nq -i $secondary_key $secondary_node $cmd2
ok_ko $?



fi

