#!/bin/ksh
DIR1=`pwd`
DIR2=`dirname $0`
DIR3=`cd $DIR1; cd $DIR2; pwd`
DIR=`cd $DIR3; pwd`;
. $DIR/../../etc/profile.config
if [[ $# -ne 1 ]]; then
echo "I expected a DBname for connection (and option for startup ) !!!"
return 5
else
if [[ `tnsping ${1}>/dev/null` -ne 0 ]]; then
echo "Failed to connect ${1}. Is listener up?"
return 2
else
logdate=$(date +"%Y%m%d-%H-%M-%S")
name=$(basename ${0}| cut -d. -f1)
logfile=`echo ${HCLLOG}"/DGBROKER/"${name}"_"${logdate}.log`
dgmgrl sys/${sys_password}@${1} "EDIT DATABASE '${1}' SET STATE='APPLY-OFF'; ">$logfile
echo $logfile
fi
fi
