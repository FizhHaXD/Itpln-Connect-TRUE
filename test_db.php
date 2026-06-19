<?php
$conn = mysqli_connect('localhost', 'root', '', 'kampus_connect');
$r = mysqli_query($conn, 'DESCRIBE communities');
echo "COMMUNITIES:\n";
while($row = mysqli_fetch_assoc($r)) echo $row['Field'].' - '.$row['Type']."\n";

echo "\nCOMMUNITY_MEMBERS:\n";
$r2 = mysqli_query($conn, 'DESCRIBE community_members');
while($row = mysqli_fetch_assoc($r2)) echo $row['Field'].' - '.$row['Type']."\n";
