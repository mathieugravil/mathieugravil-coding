$veeam_em="opgsf.cloud.corp.local"
$myuser="bobon"
$secpasswd = ConvertTo-SecureString “toto” -AsPlainText -Force
$mycreds = New-Object System.Management.Automation.PSCredential ($myuser, $secpasswd)






#https://helpcenter.veeam.com/docs/backup/rest/reports_summary.html?ver=95

$uri="http://"+$veeam_em+":9399/api/sessionMngr/?v=latest"

$response = Invoke-WebRequest –Uri $uri -Method "POST" -Credential $mycreds
$sessionId = $response.Headers["X-RestSvcSessionId"]
#([xml]$response.Content).LogonSession.Links.Link 

$uri ="http://"+$veeam_em+":9399/api/jobs"
$response = Invoke-WebRequest -Uri $uri -Method "GET" -Headers @{"X-RestSvcSessionId" = $sessionId}
([xml]$response.Content).EntityReferences.Ref | ft

$uri ="http://"+$veeam_em+":9399/api/reports/summary/overview"
$response = Invoke-WebRequest -Uri $uri -Method "GET" -Headers @{"X-RestSvcSessionId" = $sessionId}
([xml]$response.Content).OverviewReportFrame| ft

$uri ="http://"+$veeam_em+":9399/api/reports/summary/vms_overview"
$response = Invoke-WebRequest -Uri $uri -Method "GET" -Headers @{"X-RestSvcSessionId" = $sessionId}
([xml]$response.Content).VmsOverviewReportFrame| ft


$uri ="http://"+$veeam_em+":9399/api/catalog/vms"
$response = Invoke-WebRequest -Uri $uri -Method "GET" -Headers @{"X-RestSvcSessionId" = $sessionId}
([xml]$response.Content).EntityReferences|ft

$uri ="http://opgsfr-wpavea01.ostc.cloud.corp.local:9399/api/jobs/9feda679-6c1e-4d35-9229-f32dc17fbc60"
$response = Invoke-WebRequest -Uri $uri -Method "GET" -Headers @{"X-RestSvcSessionId" = $sessionId}
([xml]$response.Content).EntityRef|ft


$uri ="http://"+$veeam_em+":9399/api/reports/summary/job_statistics"
$response = Invoke-WebRequest -Uri $uri -Method "GET" -Headers @{"X-RestSvcSessionId" = $sessionId}
([xml]$response.Content).JobStatisticsReportFrame|ft



$uri ="http://"+$veeam_em+":9399/api/reports/summary/processed_vms"
$response = Invoke-WebRequest -Uri $uri -Method "GET" -Headers @{"X-RestSvcSessionId" = $sessionId}
([xml]$response.Content).ProcessedVmsReportFrame.Day|ft

$uri ="http://"+$veeam_em+":9399/api/querySvc"
$response = Invoke-WebRequest -Uri $uri -Method "GET" -Headers @{"X-RestSvcSessionId" = $sessionId}
([xml]$response.Content).QuerySvc.Links.Link | ft

$uri = ((([xml]$response.Content).QuerySvc.Links.Link | Where-object {$_.Type -eq 'BackupList'}).Href)
$uri ="http://opgsfr-wpavea01.ostc.cloud.corp.local:9399/api/query?type=Backup&format=Entities"
$response = Invoke-WebRequest -Uri $uri -Method "GET" -Headers @{"X-RestSvcSessionId" = $sessionId}
([xml]$response.Content).QueryResult.Entities.Backups.Backup | ft *


$uri = ((([xml]$response.Content).QuerySvc.Links.Link | Where-object {$_.Type -eq 'JobList'}).Href)
$uri ="http://opgsfr-wpavea01.ostc.cloud.corp.local:9399/api/query?type=Job&format=Entities"
$response = Invoke-WebRequest -Uri $uri -Method "GET" -Headers @{"X-RestSvcSessionId" = $sessionId}
([xml]$response.Content).QueryResult.Entities.Jobs.Job | Select Name, JobType, NextRun