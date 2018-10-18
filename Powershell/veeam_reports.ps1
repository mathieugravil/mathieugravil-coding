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
([xml]$response.Content).EntityReferences.Ref

$uri ="http://"+$veeam_em+":9399/api/reports/summary/overview"
$response = Invoke-WebRequest -Uri $uri -Method "GET" -Headers @{"X-RestSvcSessionId" = $sessionId}
([xml]$response.Content).OverviewReportFrame

$uri ="http://"+$veeam_em+":9399/api/reports/summary/vms_overview"
$response = Invoke-WebRequest -Uri $uri -Method "GET" -Headers @{"X-RestSvcSessionId" = $sessionId}
([xml]$response.Content).VmsOverviewReportFrame


$uri ="http://"+$veeam_em+":9399/api/catalog/vms"
$response = Invoke-WebRequest -Uri $uri -Method "GET" -Headers @{"X-RestSvcSessionId" = $sessionId}
([xml]$response.Content).EntityReferences

$uri ="http://opgsfr-wpavea01.ostc.cloud.corp.local:9399/api/jobs/9feda679-6c1e-4d35-9229-f32dc17fbc60"
$response = Invoke-WebRequest -Uri $uri -Method "GET" -Headers @{"X-RestSvcSessionId" = $sessionId}
([xml]$response.Content).EntityRef


$uri ="http://"+$veeam_em+":9399/api/reports/summary/job_statistics"
$response = Invoke-WebRequest -Uri $uri -Method "GET" -Headers @{"X-RestSvcSessionId" = $sessionId}
([xml]$response.Content).JobStatisticsReportFrame



$uri ="http://"+$veeam_em+":9399/api/reports/summary/processed_vms"
$response = Invoke-WebRequest -Uri $uri -Method "GET" -Headers @{"X-RestSvcSessionId" = $sessionId}
([xml]$response.Content).ProcessedVmsReportFrame.Day