<?php
include('Net/SFTP.php');

$sftp = new Net_SFTP('195.20.116.5');
if (!$sftp->login('TotalPOC', 'Wa3174s3Dm')) {
echo'<pre>';
print_r("erreur");
echo'</pre>';

    exit('Login Failed');
}
echo'<pre>';
print_r($sftp->nlist()); // == $sftp->nlist('.')
print_r($sftp->rawlist()); // == $sftp->rawlist('.')
echo'</pre>';

// copies filename.remote to filename.local from the SFTP server

//$sftp->get('install_131-rhel_8621.pdf', 'UKM/install_131-rhel_8621.pdf');
//$sftp->get('product_desc_external.pdf', 'UKM/product_desc_external.pdf');
//$sftp->get('redhat_installation_external.pdf', 'UKM/redhat_installation_external.pdf');
//$sftp->get('admin_manual_external.pdf', 'UKM/admin_manual_external.pdf');
//$sftp->get('User_Portal_100-205-Installation.pdf', 'UKM/User_Portal_100-205-Installation.pdf');

//failed $sftp->get('install_ukm.mp4', 'UKM/install_ukm.mp4');
//failed $sftp->get('install_userportal.mp4', 'UKM/install_userportal.mp4');

$sftp->get('sshmgr-user-portal-1.0.0-205.tar', 'UKM/sshmgr-user-portal-1.0.0-205.tar');
//$sftp->get('sshmgr-1.3.1-8621-redhat.tar', 'UKM/sshmgr-1.3.1-8621-redhat.tar');



//$sftp->get('sshmgr-eval-license-total-2013-12.dat', 'UKM/sshmgr-eval-license-total-2013-12.dat');



?>