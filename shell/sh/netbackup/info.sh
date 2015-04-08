
#bperror -U -backstat -s info | grep ADHOC  | tail -1
#STATUS CLIENT        POLICY           SCHED      SERVER      TIME COMPLETED

# bperror -U -backstat -s info -d 03/01/15 00:00:00 >log.txt
 for i in `bppllist.exe`
 do
    policy=$(bppllist.exe $i -L )
	R=$(echo "$policy"| grep "Residence:" | grep -v "("| cut -d: -f2)
 if [[ `echo "$R" | grep STK | wc -l` -ge 1 ]]; then
	Client=$(echo "$policy"|grep "HW"|cut -d: -f2 | awk '{print $1 }')
    status=$(echo "$policy"|grep "Active:" | cut -d: -f2 )
	lastjob=$(cat log.txt| grep $i | tail -1| awk '{ print $6 }')
	echo $i ";" $R ";" $Client ";" $status ";" $lastjob >>list_pol_client8.csv
 
  fi


 done
