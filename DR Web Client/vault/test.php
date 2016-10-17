<?php

$sr = ldap_search($ds, "CN=Inet_Access,OU=VISCO,DC=YUG,DC=TMN,DC=TRANSNEFT,DC=RU", "member=*"); 
echo "aaaa";
echo "Search result is " . $sr . "<br />";

$info = ldap_get_entries($ds, $sr); 

echo "Data for " . count($info[0]["member"]) . " items returned:<p>";
sort($info[0]["member"]);

for ($i=0; $i<(count($info[0]["member"])-1); $i++) 
{
echo $info[0]["member"][$i] . "<br />";
}
?> 
