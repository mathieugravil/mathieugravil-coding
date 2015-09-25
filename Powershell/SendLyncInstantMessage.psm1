#Start Conversation 
$msg = New-Object "System.Collections.Generic.Dictionary[Microsoft.Lync.Model.Conversation.InstantMessageContentType,String]" 
#Add the Message 
$msg.Add(0,$message) 
#choose Modality Type 
$Modality = $Conversation.Modalities[1] 
#Start the Dialog  
$null = $Modality.BeginSendMessage($msg, $null, $msg) 
#Send the Message  
$null = $Modality.BeginSendMessage($msg, $null, $msg)