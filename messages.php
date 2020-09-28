<?php
if($usermsg == "статы" OR $usermsg == "статистика") {
	$sql_connect = mysqli_connect($sql_host, $sql_user, $sql_pass, $sql_base) or die("Ошибка " . mysqli_error($sql_connect));
	$cs_statistic_query = "SELECT * FROM $sql_tabl WHERE vk_id = $user_id";
	$cs_statistic = $sql_connect->query($cs_statistic_query);
	// Проверка наличия статистики для данного пользователя ВК
	if($cs_statistic->num_rows == 0) {		
		SendMessageToUser($user_id,"{$user_name}, введите ваш Steam ID в формате \"STEAM_0:1:12345678\" для доступа статистики с сервера");
	} else {
		while($row = mysqli_fetch_array($cs_statistic)) {
			$cs_kill = $row['kills'];
			$cs_death = $row['deaths'];
			$cs_assists = $row['assists'];
			if($cs_death == 0) $cs_kd = ($cs_kill+$cs_assists)/1;
			else $cs_kd = ($cs_kill+$cs_assists)/$cs_death;
			$cs_kd = round($cs_kd, 2);
			$cs_points = $row['points'];
			$cs_round_max_kills = $row['round_max_kills'];
			$cs_playtime = round($row['playtime']/60,1);
			$cs_lastconnect = $row['lastconnect'];
			$cs_lastconnect = date('d/m G:i',$cs_lastconnect);
		}
		SendMessageToUser($user_id,"Статистика:<br>".
			"<br>"."&#128299; Убийства : {$cs_kill}<br>".
			"&#128128; Смерти   : {$cs_death}<br>".
			"&#128591; Помощи    : {$cs_assists}<br>".
			"&#37; Коэф. У/С: {$cs_kd}<br>".
			"&#128200; Очки ранга: {$cs_points}<br>".
			"&#128565; Макс. убийств за раунд: {$cs_round_max_kills}<br>".
			"&#9201; Времени в игре: {$cs_playtime} мин.<br>".
			"&#127939; Был в сети: {$cs_lastconnect}<br>");
	}
	mysqli_close($sql_connect);
}
else if($usermsg == "сервер"){
	require_once "../vkcover/monitoring.php";
	SendMessageToUser($user_id,"Наш сервер:<br>".
			"<br>"."Название : ".$server['name']."<br>".
			"Карта сейчас : ".$server['map']."<br>".
			"Игроков : ".$server['players']."/".$server['playersmax']."<br>".
			"Боты : ".$server['bots']."<br>".
			"IP-Адрес: 83.217.28.45:27015<br>");
}
else if(strpos($usermsg,'steam_0:1:') !== false){
	$sql_connect = mysqli_connect($sql_host, $sql_user, $sql_pass, $sql_base) or die("Ошибка " . mysqli_error($sql_connect));
	$steam_user = preg_replace('/steam_0:1:*/', '', $usermsg);
	$steam_user = $steam_user*2+1;
		
	//Выборка vk_id из таблици где vk_id = user_id
	$cs_statistic_query = "SELECT vk_id FROM $sql_tabl WHERE vk_id = $user_id";
	$cs_statistic = $sql_connect->query($cs_statistic_query);
	// Если не было найдено не одной записи
	if($cs_statistic->num_rows == 0){
		// В таблице $sql_table присвоить vk_id
		$set_vk_id = "UPDATE $sql_tabl SET vk_id = $user_id WHERE account_id = $steam_user"; 
		$set_vk_id_command = $sql_connect->query($set_vk_id);
		// Пишем об этом пользователю что произошло успешное связываение
		SendMessageToUser($user_id,"{$user_name}, Ваш профиль успешно связан со сервером.");
		}
	else SendMessageToUser($user_id,"{$user_name}, Вы уже привязывали свой Steam");
	mysqli_close($sql_connect);
}
else if($usermsg == "хочу игру" OR $usermsg == "хочу ключ" OR $usermsg == "дай игру" OR $usermsg == "хочу ключик"){
		require_once 'get_key.php';
}
else if(strpos($usermsg,'? ') !== false){
	SendMessageToUser(61502787,"Вам пришло сообщение от [id$user_id|$user_name] - $usermsg");
	SendMessageToUser($user_id,"Ваше сообщение отправлено Администратору!");
}
else if($usermsg == "команды"){
	SendMessageToUser($user_id,"
						Сервер - узнать состояние игрового сервера.
						
						Хочу игру - получение ключа игры для Steam.
						
						Погода - получение погоды в вашем городе 
						(город должен быть установлен в вашем профиле).
						
						Погода Город - узнать погоду в интересующим вас городе.
						
						Команды в разработке :)
						");
}
else if($usermsg == "погода"){
	if($user_city == "артем") $user_city = "артём";
	//SendMessageToUser($user_id,$user_city);
	$weaher_url = 'http://api.openweathermap.org/data/2.5/weather?q='.$user_city.'&units=metric&lang=ru&APPID='.API_WEATHER;
	if(get_http_response_code($weaher_url) != "200") SendMessageToUser($user_id,"Не верный запрос погоды. Проверьте название города.");
	else GetWeather($weaher_url, $user_id, $user_city);
}
else if(strpos($usermsg,'погода ') !== false){
	$user_city = preg_replace('/погода /', '', $usermsg);
	$weaher_url = 'http://api.openweathermap.org/data/2.5/weather?q='.$user_city.'&units=metric&lang=ru&APPID='.API_WEATHER;
	if(get_http_response_code($weaher_url) != "200") SendMessageToUser($user_id,"Не верный запрос погоды. Проверьте название города.");
	else GetWeather($weaher_url, $user_id, $user_city);
}
else if($usermsg == "🐓"){
	SendMessageToUser($user_id, "Это или P90 или Ваня");
}
else if($usermsg == "картинка"){
	SendMessageToUser($user_id, "https://sun9-70.userapi.com/c857532/v857532034/1905cc/v6OIlL2KOog.jpg");
}
else SendMessageToUser($user_id, "Команда не найдена &#128532; 
						Если есть вопрос или сообщение для Администрации, начните сообщение со знака  - ?
						Например: ? Хочу задать вопрос.
						
						Узнать все команды можно набрав - Команды.
						");
?>
