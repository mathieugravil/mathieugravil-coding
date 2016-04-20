#!/bin/ksh -p
# -------------------------------------------------------------------------
# AUTEUR: Ahmed Younsi                                                    -
# CREATION DATE : 03/08/2013                                              -
# MODIFIED BY:                                                            -
# MODIFIED ON:                                                            -
# COMMENTS:                                                               -
# -------------------------------------------------------------------------
action=$1
resource=$2

[[ -z $1 ]] && shift
[[ -z $2 ]] && shift

########################## LOADING CONFIG FILE ################################
DIR1=`pwd`
DIR2=`dirname $0`
DIR3=`cd $DIR1; cd $DIR2; pwd`
DIR=`cd $DIR3; pwd`;
. $DIR/../../SERVICES/CIDB/etc/profile.config
. $DIR/../../etc/global.config


#config_file=$SERVICE_CONF_DIR/service.config
#. $config_file


SCRIPT_NAME=$( basename $0 | $SED 's/\.ksh//' )

################################## FUNCTIONS ################################
# Get the list of applications that are started by svcadmin
get_active_applications() {
   LIST_APPLI=$( $LS $RC3DIR/S* | sed -e "s:$RC3DIR/S[0-9][0-9]*::g" )
   LIST_APPLI=$( $ECHO $LIST_APPLI | sed "s/  */|/g" )
}

usage() {
 echo "Usage: $SCRIPT_NAME -h"
 echo "Usage: $SCRIPT_NAME nodetype"
 echo "Usage: $SCRIPT_NAME action [$LIST_APPLI]"
 echo "Where action is start, stop, or status"
}


################## checknode() ###################
# check if a node is running
# 0: not running
# 1: Applications are running
checknode() {
   node=$1
   if [[ -z $node ]]; then
      $ECHO "Nodename must provided: checknode <hostname>"
      exit 
   fi

   if [[ $node = $master_node ]]; then
      sshkey=$sshkey_master_node
   else
      sshkey=$sshkey_aux_node
   fi

   $PING -c 1 -w 2 $node > /dev/null 2>&1 
   if [[ $? -ne 0 ]]; then
      $ECHO "Node $node unreachable. I considered it down. \c"
   else
      $SSH $node -i $sshkey ksh $INITDIR/ALL_RESOURCES check
   fi

}

################################## MAIN PROGRAM ##############################
get_active_applications

if [[ -z $action ]] || [[ $action = "-h" ]]; then
  usage
  exit 0
fi

if [[ $action = nodetype ]]; then
   echo I am $mytype node
   exit 0
fi

if [[ $action = list ]]; then
   ksh $INITDIR/ALL_RESOURCES list
   exit 0
fi

if [[ $resource = force ]]; then
   resource=
   set -- force 
fi

if [[ $action = checknode ]]; then
   checknode $2
   exit $?
fi

if [ "$action" != "stop" -a "$action" != "start" -a "$action" != "status"  -a "$action" != "check" -a "$action" != "list"  ]; then
 $ECHO "action $action invalid. Expected start, stop, status or list action"
 usage
 exit 1
fi

# resource is a component such as Control-M, the nework (NET), the filesystems (FS)...
if [[ -z $resource ]]; then
  resource=ALL_RESOURCES
fi

# Check if the resource exists
if [ ! -f $INITDIR/$resource ]; then
 $ECHO "Resource $resource does not exist. Check $INITDIR/$resource "
 exit 1
fi

# find the remote node
if [[ $nodename = $master_node ]]; then 
    other_node=$aux_node
elif [[ $nodename = $aux_node ]]; then 
    other_node=$master_node
else 
    echo "BUG. 
        - in the profile.config, master node has been to $master_node
        - in the profile.config, aux node has been to $aux_node
        - nodename $nodename has not been defined as master or aux node, please correct the profile.config file and rerun the tool
        "
        exit 1
fi

if [[ $action = status ]] || [[ $action = check ]]; then
  ksh $INITDIR/$resource $action
  exit
fi

step=1.
if [[ $action = "start" ]]; then
##START : Check must be done for DB on Dataguard ### 
if [[ $resource != "ORACLE" ]]; then
##END : Check must be done for DB on Dataguard ### 
  # check if the remote node is running services
  $ECHO "1. Before Running $( $ECHO $resource | $SED 's/ALL_RESOURCES/all resources/'), I check the remote node: \c"
  checknode $other_node
  if [[ $? -ne 0 ]]; then
     $ECHO 
     $ECHO "KO. Please Stop Applications on the remote node $other_node before starting them on this server."
     $ECHO
     exit 1
  else
    $ECHO OK.
  fi
##START : Check must be done for DB on Dataguard ### 
fi
##END : Check must be done for DB on Dataguard ### 
  step="2."
fi
  

#
$ECHO "$step Now, I $action $( $ECHO $resource | $SED 's/ALL_RESOURCES/all resources/' ) "
resp=
while [[ $resp != "yes" ]] && [[ $resp != "no" ]]; do
  $ECHO "Are you sure you want to $action $( $ECHO $resource | $SED 's/ALL_RESOURCES/all resources/' )? [yes,no] \c"
  read resp
done

if [[ $resp = no ]]; then
   $ECHO "OK I give up to $action $resource"
   exit 0
fi

$ECHO "I execute $INITDIR/$resource $action"
ksh $INITDIR/$resource $action $*
if [[ $? -eq 0 ]]; then
  $ECHO
  $ECHO "Status of $action $resource: OK"
  exit 0
else
  $ECHO
  $ECHO "Status of $action $resource: KO"
  exit 1
fi
