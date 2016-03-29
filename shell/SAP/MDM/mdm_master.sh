#!/bin/ksh
DIR1=`pwd`
. ${DIR1}/config

###### START OF start_sapservice ######
###### ${1} = SID OF SAP INSTANCE ######
###### ${2} = SERVICE MDM (MDS<SN> or MIDS<SN>) ######
###### ${3} = LOGICAL HOSTNAME ######
###### ${4} = OS USERNAME ######
###### ${5} = ORACLE_SID ######

start_sapservice()
{
if [[ $# = 4 ]];
then
if [[ -x /usr/sap/${1}/${2}/exe/sapstartsrv ]];
then
        if [[ -r /usr/sap/${1}/SYS/profile/${1}_${2}_${3} ]] ;
        then
                /usr/sap/${1}/${2}/exe/sapstartsrv pf=/usr/sap/${1}/SYS/profile/${1}_${2}_${3} -D -u ${4}
                return $? ;

        else
                echo "/usr/sap/${1}/SYS/profile/${1}_${2}_${3} doesn't exist or is not not readable!";
                return 1 ;
        fi
else
        echo "/usr/sap/${1}/${2}/exe/sapstartsrv doesn't exist or is not not executable!";
        return 1 ;
fi
else
echo "Missing args!!!"
return $5
fi
}
###### END OF start_sapservice ######



###### START OF status_sapservice ######
###### ${1} = SID OF SAP INSTANCE ######
###### ${2} = SERVICE MDM (MDS<SN> or MIDS<SN>) ######
###### ${3} = LOGICAL HOSTNAME ######
###### ${4} = OS USERNAME ######
status_sapservice()
{
if [[ $# = 4 ]];
then
        if [[ $(ps  -u ${4} -o cmd | grep  "/usr/sap/${1}/${2}/exe/sapstartsrv pf=/usr/sap/${1}/SYS/profile/${1}_${2}_${3} -D -u ${4}" | grep -v grep |wc -l ) = 1 ]];
        then
                echo "SAP service /usr/sap/${1}/${2}/exe/sapstartsrv is UP"
                return 0
        else
                echo "SAP service /usr/sap/${1}/${2}/exe/sapstartsrv is DOWN"
                return 1
        fi

else
echo "Missing args!!!"
return $5
fi
}
###### END OF status_sapservice ######



###### START OF status_db ######
###### ${1} = ORACLE_SID ######
status_db()
{
if [[ -x $ORACLE_HOME/bin/tnsping ]];
 then
        tnsping ${1}2>&1 >/dev/null
        echo "${1} is UP"
        return $?

else
        echo "NO tnsping";
        return 1
fi
}
###### END OF status_db ######

###### START OF status_MD ######
###### ${1} = SID OF SAP INSTANCE ######
###### ${2} = SERVICE MDM (MDS<SN> or MIDS<SN>) ######
###### ${3} = OS USERNAME ######

status_MD()
{
if [[ $# = 3 ]];
then
         serv=$(echo ${2} | sed 's/[0-9]*//g' | tr "[:upper:]" "[:lower:]")
         if [[ $(ps  -u ${3} -o cmd | grep ${serv}.sap${1}_${2} | grep -v grep ) > 0 ]];
         then
                echo "${serv}.sap${1}_${2} is UP"
                return 0
         else
                echo "${serv}.sap${1}_${2} is DOWN"
                return 1
         fi
else
        echo "Missing args!!!"
        return $5
fi
}


###### END OF status_MD ######

###### START OF GetRepolist ######
###### ${1} = ORACLE_SID ######
###### ${2} = ORACLE_USER ######
###### ${3} = ORACLE_PASSWORD ######
###### Return list of REPO ######
GetRepolist()
{
status_db ${1}>/dev/null
if [[ $? = 0 ]];
then
        if [[ -x $ORACLE_HOME/bin/sqlplus ]];
        then
        REPO_LIST_IN_DB=$(sqlplus -S ${2}/${3}@${DBHOST}:${DBPORT}/${1}<<-END
                        whenever sqlerror exit sql.sqlcode;
                        set echo OFF heading  off feedback OFF ;
                        select CATALOG_TAG FROM A2I_XCAT_DBS.A2I_CATALOGS;
                        exit ;
END
                        )
        if [[ $(echo $REPO_LIST_IN_DB | grep "ORA-" | wc -l) = 0 ]];
        then
                echo $REPO_LIST_IN_DB
                return 0
        else
                echo "ERROR : $REPO_LIST_IN_DB"
                return 1
        fi
        else
        echo "NO sqlplus";
        return 1
        fi
else
        echo "${1} is not UP";
        exit 1 ;
fi

}
###### END OF GetRepolist ######





###### START OF GetRepoStatus ######
###### ${1} = LOGICAL HOSTNAME ######
###### ${2} = REPO_NAME ######
###### ${3} = DBHOST ######
###### ${4} = DBPORT ######
###### ${5} = ORACLE_SID ######
###### ${6} = ORACLE_USER ######
###### ${7} = ORACLE_PASSWORD ######
###### ${8} = PASSWORD_OF ADMIN######
GetRepoStatus()
{
if [[ $# > 6 ]];
then

        if [[ $(clix  mdsStatus ${1}  Admin:${8} | grep ${2}| wc -l) = 0  ]];
        then
                echo ${2} "# is #NOT_MOUNTED#"
                return 1
        else
                clix  mdsStatus ${1}  Admin:${8} | grep "${2} "  | while read repname DBinst dbtype port reptype status
                do
                        if [[ $status = "Login" ]];
                        then
                                echo "Password I have for "${reponame}" is not valid (${8}) so I change it with the given one :D !"
                                myrep="${2};${3}:${4}/${5};O;${6};${7}"
                                clix repEmergencyAdminUserSetPassword  ${1} ${myrep}   Admin:${8} 2>&1 >/dev/null
                                clix  mdsStatus ${1}  Admin:${8} | grep "${2} "  | while read repname DBinst dbtype port reptype status
                                do
                                        echo ${2} "# has status #"$(echo ${status}| tr "[:lower:]" "[:upper:]")"# and it use port #"${port}"#."
                                done

                        else
                                echo ${2} "# has status #"$(echo ${status}| tr "[:lower:]" "[:upper:]")"# and it use port #"${port}"#."

                        fi
                done
        fi


else
      echo "Missing args!!!"
        return 5
fi
}

###### END OF GetRepoStatus ######

###### START OF DoRepoAction ######
###### ${1} = LOGICAL HOSTNAME ######
###### ${2} = REPO_SPEC ######
###### ${3} = ACTION ######
###### ${4} = PORT/PASSWORD ######
DoRepoAction()
{
if [[ $# >  2 ]];
then
        case $3 in
        repMount)
                echo "o I will try to mount $(echo ${2}| cut -d';' -f1 ) on port ${4}: "
                (clix repMount  ${1}  ${2} ${4}           2>&1) >/dev/null
        ;;
        repStart)
                echo "o I will try to start $(echo ${2}| cut -d';' -f1 ) :"
                (clix repStart ${1} ${2} Admin:${4}   -W ) 2>&1 >/dev/null
        ;;
        repStop)
                echo "o I will try to stop $(echo ${2}| cut -d';' -f1 ) :"
                (clix repStop   ${1} ${2} Admin:${4}) 2>&1 >/dev/null
        ;;
        repUnMount)
                echo "o I will try to unmount $(echo ${2}| cut -d';' -f1 ) :"
                (clix repUnMount ${1} ${2}) 2>&1 >/dev/null
        ;;
        repRepair)
                 echo "o I will try to check/repair $(echo ${2}| cut -d';' -f1 ) :"
                (clix repRepair ${1} ${2} Admin:${4}) 2>&1 >/dev/null

        ;;
        cpyArchive)
                 echo "o I will try to backup $(echo ${2}| cut -d';' -f1 ) in ${4} :"
                (clix cpyArchive ${1} ${2}) 2>&1 >/dev/null
        ;;
        *)
                echo "ERROR : ${3}:  ACTION NOT DEFINED!!!"
        ;;
        esac
        if [[ $? != 0 ]];
        then
                echo "  ERROR : I didn't manage to ${3} $(echo ${2}| cut -d';' -f1 )"
                return 3
        else
                echo "  OK: ${3} is done on $(echo ${2}| cut -d';' -f1 )"
                return 0
        fi
else
      echo "Missing args!!!"
      return 5
fi
}
###### END OF DoRepoAction ######





###### START OF MAIN ######
case $1 in
#############################
########## START  ###########
#############################
start)
status_db ${ORACLE_SID}
if [[ $? = 0 ]];
then
        status_sapservice ${SID_MDM} MDS${SYS_NUMBER_MDS} ${LOGICAL_HOSTNAME} ${OS_USER}
        if [[ $? != 0 ]];
        then
                echo "o Start of sapservices for MDS${SYS_NUMBER_MDS}: "
                start_sapservice ${SID_MDM} MDS${SYS_NUMBER_MDS} ${LOGICAL_HOSTNAME} ${OS_USER}
                if [[ $? = 0 ]];
                then
                         echo "OK";
                else
                        echo "KO";
                        exit 1
                fi
        fi
        status_sapservice ${SID_MDM} MDIS${SYS_NUMBER_MDIS} ${LOGICAL_HOSTNAME} ${OS_USER}
        if [[ $? != 0 ]];
        then
                echo "o Start of sapservices for MDIS${SYS_NUMBER_MDIS}: "
                start_sapservice ${SID_MDM} MDIS${SYS_NUMBER_MDIS} ${LOGICAL_HOSTNAME} ${OS_USER}
                if [[ $? = 0 ]];
                then
                         echo "OK";
                else
                        echo "KO";
                        exit 1
                fi
        fi
        status_MD ${SID_MDM} MDS${SYS_NUMBER_MDS} ${OS_USER}
        if [[ $? != 0 ]];
        then
                echo "o Start of MDS${SYS_NUMBER_MDS}:"
                (clix icGO ${LOGICAL_HOSTNAME} ${OS_USER} ${OS_PASSWORD} mds )  2>&1 >/dev/null
                if [[ $? = 0 ]];
                then
                        echo "OK";
                else
                        echo "KO";
                        exit 1
                fi
        fi
        status_MD ${SID_MDM} MDIS${SYS_NUMBER_MDIS} ${OS_USER}
        if [[ $? != 0 ]];
        then
                echo "o Start of MDIS${SYS_NUMBER_MDIS}:"
                ( clix icGO ${LOGICAL_HOSTNAME} ${OS_USER} ${OS_PASSWORD} mdis) 2>&1 >/dev/null
                if [[ $? = 0 ]];
                then
                        echo "OK";
                else
                        echo "KO";
                        exit 1
                fi
        fi
        repolist=$(GetRepolist ${ORACLE_SID} ${ORACLE_USER} ${ORACLE_PASSWORD})
        ports_list=""
        repo_to_be_mounted=""
        repo_to_be_started=""
        for i in  ${repolist}
        do
                GetRepoStatus ${LOGICAL_HOSTNAME} $i  ${DBHOST} ${DBPORT} ${ORACLE_SID} ${ORACLE_USER} ${ORACLE_PASSWORD} ${ADMIN_PASS_REPO} |  while  IFS='#' read  reponame bla1 status bla2 port bla3
                do
        ## see ttp://wiki.scn.sap.com/wiki/pages/viewpage.action?pageId=250644649 SAP MDM requires three consecutive ports for each repository.
                        ## if [[ $port != "" ]];
                        if [[  $port  =~ ^[0-9]+$ ]];
                        then
                                port2=$(echo ${port}"+1" | bc )
                                port3=$(echo ${port}"+2" | bc )
                                ports_list=$( echo ${port}" "${port2}" "${port3}" "${ports_list})
                        fi
                        if [[ ${status} = "NOT_MOUNTED" ]];
                        then
                                repo_to_be_mounted=$(echo ${repo_to_be_mounted} " " ${reponame})
                        else
                                        if [[ ${status} != "STARTED RUNNING" ]];
                                        then
                                                repo_to_be_started=$(echo ${repo_to_be_started} " " ${reponame})
                                        fi
                        fi
                done
        done
        echo "Used ports: "${ports_list}
        echo "Repo to be mounted: "${repo_to_be_mounted}
        echo "Repo already mounted but to be started: "${repo_to_be_started}
        if [[ ${repo_to_be_mounted} != "" ]];
        then
        echo "o Determination of free ports"
        free_ports=""
        for k in  $(seq 2000 3  10000 )
        do
                port2=$(echo ${k}"+1" | bc )
                port3=$(echo ${k}"+2" | bc )

                if ! [[ ${ports_list} =~ .*${k}.* ]];
                then
                         if ! [[ ${ports_list} =~ .*${port2}.* ]];
                        then
                                if ! [[ ${ports_list} =~ .*${port3}.* ]];
                                then
                                        free_ports=$(echo $free_ports $k)
                                fi
                        fi
                fi
        done

echo " "
echo "o Mounting of Repository  :"
        for j in ${repo_to_be_mounted}
        do
                myrep="${j};${DBHOST}:${DBPORT}/${ORACLE_SID};O;${ORACLE_USER};${ORACLE_PASSWORD}"
                new_port=$(echo ${free_ports} | awk '{ print $1} ')
                port2=$(echo ${new_port}"+1" | bc )
                port3=$(echo ${new_port}"+2" | bc )

                free_ports=$(echo ${free_ports} |sed "s/${new_port}//" )
                free_ports=$(echo ${free_ports} |sed "s/${port2}//" )
                free_ports=$(echo ${free_ports} |sed "s/${port3}//" )
                if [[ $2 = "ALL" ]]; then
                        DoRepoAction ${LOGICAL_HOSTNAME} $myrep repMount $new_port
                        repo_to_be_started=$(echo ${repo_to_be_started}" "${repo_to_be_mounted})
                else
                        print -n  "Are you sure you want to mount ${j}  (yes/no)?"
                        while read answer
                                do
                                        case  $answer in
                                                yes)
                                                DoRepoAction ${LOGICAL_HOSTNAME} $myrep repMount $new_port
                                                repo_to_be_started=$(echo ${repo_to_be_started}" "${j})
                                                break
                                                ;;
                                                no)
                                                echo "Nothing done"
                                                break
                                                ;;
                                                *)
                                                print -n  "Are you sure you want to mount ${j}  (yes/no)?"
                                                ;;
                                        esac
                                done
                fi
        done
        #repo_to_be_started=$(echo ${repo_to_be_started}" "${repo_to_be_mounted})
        fi
echo " "
if [[ $3 != "MOUNT" ]]; then
echo "o Starting of repository : "
        for l in ${repo_to_be_started}
        do
                myrep="${l};${DBHOST}:${DBPORT}/${ORACLE_SID};O;${ORACLE_USER};${ORACLE_PASSWORD}"
                if [[ $2 = "ALL" ]]; then
                        DoRepoAction ${LOGICAL_HOSTNAME} $myrep repStart ${ADMIN_PASS_REPO}
                else
                        print -n  "Are you sure you want to start ${l}  (yes/no)?"
                        while read answer
                                do
                                        case  $answer in
                                                yes)
                                                DoRepoAction ${LOGICAL_HOSTNAME} $myrep repStart ${ADMIN_PASS_REPO}
                                                break
                                                ;;
                                                no)
                                                echo "Nothing done"
                                                break
                                                ;;
                                                *)
                                                print -n  "Are you sure you start to mount ${l}  (yes/no)?"
                                                ;;
                                        esac
                                done
                fi
        done
fi
else
        echo "ERROR : start of  ${SID_MDM} Failed!! You must start db ${ORACLE_SID} before !!!"
fi
;;
#############################
########## STOP   ###########
#############################
stop)
status_db ${ORACLE_SID} 2>&1 >/dev/null
rcdb=$?
status_MD ${SID_MDM} MDS${SYS_NUMBER_MDS} ${OS_USER} 2>&1 >/dev/null
rcmdm=$?
if  [[ $rcdb = 0 ]] ;
then
repo_to_be_stopped=""
repo_to_be_unmounted=""
repolist=$(GetRepolist ${ORACLE_SID} ${ORACLE_USER} ${ORACLE_PASSWORD})
for i in  ${repolist}
do
        GetRepoStatus ${LOGICAL_HOSTNAME} $i  ${DBHOST} ${DBPORT} ${ORACLE_SID} ${ORACLE_USER} ${ORACLE_PASSWORD} ${ADMIN_PASS_REPO} | while IFS="#" read repo bla status bla2 port
        do
                if [[ ${status} = "STARTED RUNNING" ]];
                then
                        repo_to_be_stopped=$( echo ${repo}" "${repo_to_be_stopped})
                else
                        if [[ ${status} != "NOT_MOUNTED" ]];
                        then
                                repo_to_be_unmounted=$(echo ${repo}" "${repo_to_be_unmounted})
                        fi
                fi
        done
done
echo "Repo to be stopped and unmounted :"${repo_to_be_stopped}
echo "Repo to be unmounted :"${repo_to_be_unmounted}
echo " "
echo "o Stopping Repository : "
for i in ${repo_to_be_stopped}
do
        myrep="${i};${DBHOST}:${DBPORT}/${ORACLE_SID};O;${ORACLE_USER};${ORACLE_PASSWORD}"
        if [[ $2 = "ALL" ]]; then
                DoRepoAction ${LOGICAL_HOSTNAME} $myrep repStop ${ADMIN_PASS_REPO}
                repo_to_be_unmounted=$( echo ${repo_to_be_unmounted}" "${repo_to_be_stopped})
        else
        print -n  "Are you sure you want to stop ${i}  (yes/no)?"
        while read answer
        do
        case  $answer in
                yes)
                        DoRepoAction ${LOGICAL_HOSTNAME} $myrep repStop ${ADMIN_PASS_REPO}
                        repo_to_be_unmounted=$( echo ${repo_to_be_unmounted}" "${i})
                        break
                ;;
                no)
                        echo "Nothing done"
                        break
                ;;
                *)
                        print -n  "Are you sure you want to stop ${i}  (yes/no)?"
                ;;
                esac
        done
        fi

done
echo " "
echo " o Unmounting Repository : "
#repo_to_be_unmounted=$( echo ${repo_to_be_unmounted}" "${repo_to_be_stopped})
for i in ${repo_to_be_unmounted}
do
        myrep="${i};${DBHOST}:${DBPORT}/${ORACLE_SID};O;${ORACLE_USER};${ORACLE_PASSWORD}"
        if [[ $2 = "ALL" ]]; then
                DoRepoAction ${LOGICAL_HOSTNAME} $myrep repUnMount 1
        else
        print -n  "Are you sure you want to unmount ${i}  (yes/no)?"
        while read answer
        do
        case  $answer in
                yes)
                        DoRepoAction ${LOGICAL_HOSTNAME} $myrep repUnMount 1
                        break
                ;;
                no)
                        echo "Nothing done"
                        break
                ;;
                *)
                        print -n  "Are you sure you want to unmount ${i}  (yes/no)?"
                ;;
                esac
        done
        fi


done
repo_to_be_stopped=""
repo_to_be_unmounted=""
for i in  ${repolist}
do
        GetRepoStatus ${LOGICAL_HOSTNAME} $i  ${DBHOST} ${DBPORT} ${ORACLE_SID} ${ORACLE_USER} ${ORACLE_PASSWORD} ${ADMIN_PASS_REPO} | while IFS="#" read repo bla status bla2 port
        do
                if [[ ${status} = "STARTED RUNNING" ]];
                then
                        repo_to_be_stopped=$( echo ${repo}" "${repo_to_be_stopped})
                else
                        if [[ ${status} != "NOT_MOUNTED" ]];
                        then
                                repo_to_be_unmounted=$(echo ${repo}" "${repo_to_be_unmounted})
                        fi
                fi
        done
done
 echo $repo_to_be_stopped " "  $repo_to_be_unmounted
if [[ $repo_to_be_stopped = "" ]]  &&  [[ $repo_to_be_unmounted = "" ]];
then
        echo "o I will try to stop MDIS${SYS_NUMBER_MDIS} :"
        (clix icHalt ${LOGICAL_HOSTNAME} ${OS_USER} ${OS_PASSWORD} mdis -T 5) 2>&1 >/dev/null
        if [[ $? = 0 ]];
        then
                echo "  OK : MDIS${SYS_NUMBER_MDIS} Stopped"
        else
                echo "  ERROR : I didn't manage to  stop MDIS${SYS_NUMBER_MDIS} "
        fi
else
        echo "o Please unmount and stop repositories : ${repo_to_be_stopped} ${repo_to_be_unmounted} , if you want to stop mdm server!!!"
        exit 5
fi
status_MD ${SID_MDM} MDS${SYS_NUMBER_MDS} ${OS_USER} 2>&1 >/dev/null
if [[ $? = 0 ]];
then
        echo "o I will try to stop MDS${SYS_NUMBER_MDS} :"
        (clix icHalt ${LOGICAL_HOSTNAME} ${OS_USER} ${OS_PASSWORD} mds -T 5)  2>&1 >/dev/null
        if [[ $? = 0 ]];
        then
                echo "  OK : MDS${SYS_NUMBER_MDS} Stopped"
        else
                echo "  ERROR : I didn't manage to  stop MDS${SYS_NUMBER_MDS} "
        fi
else
        echo "o MDS${SYS_NUMBER_MDS} is already stopped"
fi
else
        echo " DB or MDS is DOWN"
fi
;;
#############################
########## REPAIR ###########
#############################
repair)
status_db ${ORACLE_SID} 2>&1 >/dev/null
rcdb=$?
status_MD ${SID_MDM} MDS${SYS_NUMBER_MDS} ${OS_USER} 2>&1 >/dev/null
rcmdm=$?
if  [[ $rcdb = 0 ]] && [[   $rcmdm = 0 ]];
then
repolist=$(GetRepolist ${ORACLE_SID} ${ORACLE_USER} ${ORACLE_PASSWORD})
repo_to_be_repaired=''
repo_to_be_stopped=''
for i in  ${repolist}
do
        GetRepoStatus ${LOGICAL_HOSTNAME} $i  ${DBHOST} ${DBPORT} ${ORACLE_SID} ${ORACLE_USER} ${ORACLE_PASSWORD} ${ADMIN_PASS_REPO} | while IFS="#" read repo bla status bla2 port
        do
                if [[ ${status} = "STOPPED" ]];
                then
                        repo_to_be_repaired=$( echo $repo_to_be_repaired $i)
                else
                        repo_to_be_stopped=$(echo $repo_to_be_stopped $i )
                fi
        done
done
for l in ${repo_to_be_stopped}
do

         myrep="${l};${DBHOST}:${DBPORT}/${ORACLE_SID};O;${ORACLE_USER};${ORACLE_PASSWORD}"
        if [[ $2 = "ALL" ]]; then
                 DoRepoAction ${LOGICAL_HOSTNAME} $myrep repStop ${ADMIN_PASS_REPO}
                 repo_to_be_repaired=$( echo $repo_to_be_repaired $l)
        else
                echo " WARNING :  You must stop the repo before repair it !!!"
                print -n  "Are you sure you want to Stop ${l}  (yes/no)?"
                                while read answer
                                do
                                case  $answer in
                                        yes)
                                                DoRepoAction ${LOGICAL_HOSTNAME} $myrep repStop ${ADMIN_PASS_REPO}
                                                repo_to_be_repaired=$( echo $repo_to_be_repaired $l)
                                                break
                                        ;;
                                        no)
                                                echo "Nothing done"
                                                break
                                        ;;
                                        *)
                                                print -n  "Are you sure you want to Stop ${l}  (yes/no)?"
                                        ;;
                                        esac
                                done

        fi


done
echo " "
rc=0
for k in ${repo_to_be_repaired}
do
         myrep="${k};${DBHOST}:${DBPORT}/${ORACLE_SID};O;${ORACLE_USER};${ORACLE_PASSWORD}"
        if [[ $2 = "ALL" ]]; then
                DoRepoAction ${LOGICAL_HOSTNAME} $myrep repRepair ${ADMIN_PASS_REPO}
                rc=$(echo $rc + $? | bc )
        else
                print -n  "Are you sure you want to Repair ${i}  (yes/no)?"
                                while read toto
                                do
                                case  $toto in
                                        yes)
                                                DoRepoAction ${LOGICAL_HOSTNAME} $myrep repRepair ${ADMIN_PASS_REPO}
                                                rc=$(echo $rc + $? | bc )
                                                break
                                        ;;
                                        no)
                                                echo "Nothing done"
                                                break
                                        ;;
                                        *)
                                                print -n  "Are you sure you want to Repair ${i}  (yes/no)?"
                                        ;;
                                        esac
                                done
        fi

done
return $rc
else
echo " DB or MDS is DOWN"
fi
;;
backup)
echo "BACKUP"
 pathname=$(grep "Archive Dir" /usr/sap/${SID_MDM}/MDS${SYS_NUMBER_MDS}/config/mds.ini | cut -d= -f2 )
repolist=$(GetRepolist ${ORACLE_SID} ${ORACLE_USER} ${ORACLE_PASSWORD})
rc=0
for i in  ${repolist}
do
   GetRepoStatus ${LOGICAL_HOSTNAME} $i  ${DBHOST} ${DBPORT} ${ORACLE_SID} ${ORACLE_USER} ${ORACLE_PASSWORD} ${ADMIN_PASS_REPO} | while IFS="#" read repo bla status bla2 port
        do
                 myrep="${i};${DBHOST}:${DBPORT}/${ORACLE_SID};O;${ORACLE_USER};${ORACLE_PASSWORD}"
                 filename=$(echo ${pathname}"/"${i}"_"$(date +%y%m%d)".a2a" )
                if [[ $status = "NOT_MOUNTED" ]]; then
                        echo "o WARNING : I can't do backup on NOT_MOUNTED repo. So, I will try to mount  ${i}"
                        rc=$(echo ${rc} + 2 | bc )
                elif [[ $status = "STARTED RUNNING" ]]; then

                        echo "o WARNING : Doing  backup on STARTED repo, could be not a good idea, but i will try to do it  for ${i} : "
                        DoRepoAction ${LOGICAL_HOSTNAME} $myrep cpyArchive ${filename}
                        rc=$(echo ${rc} + $? | bc )
                else
                        DoRepoAction ${LOGICAL_HOSTNAME} $myrep cpyArchive ${filename}
                        rc=$(echo ${rc} + $? | bc )
                fi
        done
done
;;

#############################
########## STATUS ###########
#############################
status)
echo "o STATUS OF DB :"
  status_db ${ORACLE_SID}
echo ""
echo "o STATUS OF SAPSERVICES :"
status_sapservice ${SID_MDM} MDS${SYS_NUMBER_MDS} ${LOGICAL_HOSTNAME} ${OS_USER}
status_sapservice ${SID_MDM} MDIS${SYS_NUMBER_MDIS} ${LOGICAL_HOSTNAME} ${OS_USER}
echo ""
echo "o STATUS OF MDM SERVER  :"
status_MD ${SID_MDM} MDS${SYS_NUMBER_MDS} ${OS_USER}
echo ""
echo "o STATUS OF MDM IMPORT SERVER  :"
status_MD ${SID_MDM} MDIS${SYS_NUMBER_MDIS} ${OS_USER}
echo ""

echo "o STATUS OF REPOSITORIES  :"
repolist=$(GetRepolist ${ORACLE_SID} ${ORACLE_USER} ${ORACLE_PASSWORD})
for i in  ${repolist}
do
GetRepoStatus ${LOGICAL_HOSTNAME} $i  ${DBHOST} ${DBPORT} ${ORACLE_SID} ${ORACLE_USER} ${ORACLE_PASSWORD} ${ADMIN_PASS_REPO}

done
;;
*)
        echo "usage: $0 {start|stop|status|repair} [ALL]"
;;
esac

###### END OF MAIN ######
