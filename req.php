<?php 
switch ($_SERVER['HTTP_ORIGIN']) {
    case 'http://1c-crm.tilda.ws': case 'https://1c-crm.tilda.ws':
    header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    break;
}

$data=[];
$myCurl = curl_init();
curl_setopt_array($myCurl, array(
    CURLOPT_URL => 'https://1cfresh.com/a/httpextreg/hs/ExternalRegistration/register',
    CURLOPT_RETURNTRANSFER => true,
	CURLOPT_HEADER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query(array('promouser'=>$_POST['promouser'],'name'=>$_POST['name'],'phone'=>$_POST['phone'],'email'=>$_POST['email'],'g-recaptcha-response'=> $_POST['g-recaptcha-response'])),
	array('Content-Type: application/x-www-form-urlencoded')
));
$response = curl_exec($myCurl);
curl_close($myCurl);

$res_f = explode("\n",$response);
if(trim($res_f[0]) == 'HTTP/1.1 500 Internal server error'){
	$to = 'good.evgesh@yandex.ru , novoeo@novoeo.ru';
	$subject = "Данные при повторной регистрации";
	$message = 'Имя - ' .$_POST['name']; 
	$message .= '<br>Телефон - ' .$_POST['phone']; 
	$message .= '<br>Email - ' .$_POST['email']; 
	
	$from_mail = 'admin@1c-crm.ru';
	
	$headers  = "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n";
    $headers .= "From: $from_mail\r\n";
    mail($to, $subject, $message, $headers);
	
	$data['res'] = 'error';
	echo json_encode($data);
	exit;
}else{
	foreach($res_f as $res){
		if(preg_match('/Location:(.*)/',$res)){
			$data['res'] = 'sucss';
			preg_match('/Location:(.*)/',$res,$matches);
			$data['txt'] = $matches[1];
			echo json_encode($data);
			exit;
		}
	}
	
}

