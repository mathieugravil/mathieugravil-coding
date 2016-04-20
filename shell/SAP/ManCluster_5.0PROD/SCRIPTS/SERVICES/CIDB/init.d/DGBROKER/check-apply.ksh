#!/bin/ksh
DIR1=`pwd`
DIR2=`dirname $0`
DIR3=`cd $DIR1; cd $DIR2; pwd`
DIR=`cd $DIR3; pwd`;
. $DIR/../../etc/profile.config

logdate=$(date +"%Y%m%d-%H-%M-%S")
name=$(basename ${0}| cut -d. -f1)
logfile=`echo ${HCLLOG}"/DGBROKER/"${name}"_"${logdate}.log`
sqlplus -S /nolog <<EOF >/dev/null
CONNECT /  AS SYSDBA
set head off
set echo off
spool $logfile
SELECT 'Difference :'||(max(ARCH.SEQUENCE#) - max(APPL.SEQUENCE#)) "Difference"  FROM (SELECT THREAD# ,SEQUENCE# FROM V\$ARCHIVED_LOG WHERE (THREAD#,FIRST_TIME ) IN (SELECT THREAD#,MAX(FIRST_TIME) FROM V\$ARCHIVED_LOG GROUP BY THREAD#)) ARCH,(SELECT THREAD# ,SEQUENCE# FROM V\$LOG_HISTORY WHERE (THREAD#,FIRST_TIME ) IN (SELECT THREAD#,MAX(FIRST_TIME) FROM V\$LOG_HISTORY GROUP BY THREAD#)) APPL  WHERE  ARCH.THREAD# = APPL.THREAD# ;
spool off;
exit;
EOF
echo $logfile
