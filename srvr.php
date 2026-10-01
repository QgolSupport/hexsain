<?php
// Retired server-control and filesystem-download endpoints are not part of this website.
http_response_code(404);
exit;

$remoteServerIp = '35.154.190.213';
$remoteUsername = 'Qtlive';
$remotePassword = '1Qt_entry';
$xamppPath = 'C:\xampp';

// Test PsExec connectivity first
$testCommand = "psexec \\\\$remoteServerIp -u $remoteUsername -p $remotePassword hostname";
exec($testCommand, $testOutput, $testReturnCode);

if ($testReturnCode !== 0) {
    die("PsExec failed. Check credentials/firewall. Error: " . implode("\n", $testOutput));
}

// Command 1: Start Apache as a service (if installed)
$command = "psexec \\\\$remoteServerIp -u $remoteUsername -p $remotePassword net start Apache";
exec($command, $output, $returnCode);

// If service fails, try starting httpd.exe directly
if ($returnCode !== 0) {
    $command = "psexec \\\\$remoteServerIp -u $remoteUsername -p $remotePassword \"$xamppPath\\apache\\bin\\httpd.exe\"";
    exec($command, $output, $returnCode);
}

if ($returnCode === 0) {
    echo "Apache started successfully! Output: " . implode("\n", $output);
} else {
    echo "Failed to start Apache. Error: " . implode("\n", $output);
}
?>