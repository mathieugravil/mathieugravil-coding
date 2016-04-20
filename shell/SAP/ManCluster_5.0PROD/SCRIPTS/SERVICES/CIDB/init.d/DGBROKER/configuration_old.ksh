#!/bin/ksh
. /opt/ManCluster/DG/CIDB/etc/profile.config
if [[ $# -ne 1 ]]; then
echo "I expected a DBname for connection !!!"
return 5 
else
if [[ `tnsping ${1}>/dev/null` -ne 0 ]]; then
echo "Failed to connect ${1}. Is listener up?"
return 2
else
dgmgrl sys/${sys_password}@$1<<EOF
SHOW CONFIGURATION;
EOF
fi
fi
