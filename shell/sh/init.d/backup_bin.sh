#!/bin/sh

BACKUP_USER=bkpuser
PASSWORD=eUiyDsnnmRzLZARAx7XT
BACKUP_DIR=/var/lib/backups
LOG_DIR=/var/log/mariadb
log_date=`date +%Y%m%d_%H%M%S`
BIN_BACKUP_NAME=$(readlink -f ${BACKUP_DIR}/last_backup)"_bin.backup"
master_info=$(echo "show slave status \G" | mysql  --password=${PASSWORD} --user=${BACKUP_USER} | egrep -e 'Master_Host|Master_Port')
master_host=$( echo "${master_info}" | grep Host | cut -d: -f2)
master_port=$( echo "${master_info}" | grep Port | cut -d: -f2)
launch_cmd ()
{
desc=$(expr substr "${1}" 1 42)
$1
if [[ $? -ne 0 ]]; then
echo "${log}"
echo "ERROR `date +%Y-%m-%d_%H:%M:%S`  ${desc}"
exit 5
else
echo "SUCCESS `date +%Y-%m-%d_%H:%M:%S` ${desc}"
return 0
fi

}

set -x
master_status=$( echo "show master status " | mysql --host 192.168.1.4 --port 3306 --password='eUiyDsnnmRzLZARAx7XT' --user='bkpuser'     | grep -v File_size | grep -v Position )
last=$(echo "$master_status" | awk '{print $2 }')
CURRENT_BIN=$(echo "$master_status" | awk '{print $1 }')
ALL_BIN=$(echo $( echo "show BINARY LOGS " | mysql --host 192.168.1.4 --port 3306 --password='eUiyDsnnmRzLZARAx7XT' --user='bkpuser'     | grep -v File_size| awk '{ print $1 }' ))

if [[ -f ${BIN_BACKUP_NAME} ]]; then
        first=$( cat  ${BIN_BACKUP_NAME} | grep end_log_pos | tail -1 | awk '{ print $7}')
        FIRST_BIN=$( cat  ${BIN_BACKUP_NAME} | grep  $(echo ${CURRENT_BIN}| cut -d'.' -f1) | tail -1 | awk '{print $10}' )
        if [[ ${FIRST_BIN} = ${CURRENT_BIN} ]]; then
                mysqlbinlog  -R  --start-position=${first}  --stop-position=${last} --host ${master_host}  --port ${master_port}  --password=${PASSWORD}  --user=${BACKUP_USER}  ${FIRST_BIN}   >> ${BIN_BACKUP_NAME} || echo "FAILURE $? "
        else
                if [[ ${first} -ge ${last} ]]; then
                        mysqlbinlog  -R   --stop-position=${last} --host ${master_host}  --port ${master_port}  --password=${PASSWORD}  --user=${BACKUP_USER}  ${ALL_BIN}   >> ${BIN_BACKUP_NAME} || echo "FAILURE $? "
                else
                        mysqlbinlog  -R  --start-position=${first}  --stop-position=${last} --host ${master_host}  --port ${master_port}  --password=${PASSWORD}  --user=${BACKUP_USER}  ${ALL_BIN}   >> ${BIN_BACKUP_NAME} || echo "FAILURE $? "
                fi
        fi
else
        mysqlbinlog  -R   --stop-position=${last} --host ${master_host}  --port ${master_port}  --password=${PASSWORD}  --user=${BACKUP_USER}     ${ALL_BIN} >> ${BIN_BACKUP_NAME}  || echo "FAILURE $? "
fi
