<?php
if($usermsg == "Привет" OR $usermsg == "Статы" OR $usermsg == "статы") {
	$sql_connect = mysqli_connect($sql_host, $sql_user, $sql_pass, $sql_base) or die("Ошибка " . mysqli_error($sql_connect));
	
	$dz_statistic_query = "SELECT * FROM $sql_tabl WHERE vk_id = $user_id";
	$dz_statistic = $sql_connect->query($dz_statistic_query);
	
	if($dz_statistic->num_rows == 0) {		
		SendMessageToUser($user_id,"{$user_name} введите ваш Steam ID в формате \"STEAM_0:1:12345678\" для доступа статистики с сервера");
	} else {
		while($row = mysqli_fetch_array($dz_statistic)) {
			$dz_kill = $row['kill'];
			$dz_death = $row['death'];
			$dz_kd = $dz_kill/$dz_death;
			$dz_kd = round($dz_kd, 2);
			$dz_win = $row['win'];
		}
		SendMessageToUser($user_id,"Статистика:<br>".
			"<br>"."&#128299; Убийства: {$dz_kill}<br>".
			"&#128128; Смерти: {$dz_death}<br>".
			"&#128137; К/Д   : {$dz_kd}<br>".
			"&#127941; Побед : {$dz_win}<br>");
	}
	mysqli_close($sql_connect);
} else if(strpos($usermsg,'STEAM_0:1:') !== false){
		$sql_connect = mysqli_connect($sql_host, $sql_user, $sql_pass, $sql_base) or die("Ошибка " . mysqli_error($sql_connect));
		$steam_user = preg_replace('/STEAM_0:1:*/', '', $usermsg);
		$steam_user = $steam_user*2+1;
		
		//Выборка vk_id из таблици где vk_id = user_id
		$dz_statistic_query = "SELECT vk_id FROM $sql_tabl WHERE vk_id = $user_id";
		$dz_statistic = $sql_connect->query($dz_statistic_query);
		// Если не было найдено не одной записи
		if($dz_statistic->num_rows == 0){
			// В таблице $sql_table присвоить vk_id
			$set_vk_id = "UPDATE $sql_tabl SET vk_id = $user_id WHERE steamid = $steam_user"; 
			$set_vk_id_command = $sql_connect->query($set_vk_id);
			// Пишем об этом пользователю что произошло успешное связываение
			SendMessageToUser($user_id,'Ваш профиль успешно связан со сервером');
		}
		else {
		SendMessageToUser($user_id,'Вы уже привязывали свой Steam');
		}
		mysqli_close($sql_connect);
	}
	else if($usermsg == "Тест")
	{
	SendMessageToUser($user_id,'Что тут хочешь протестировать?');
	}
else {
	SendMessageToUser($user_id,"Не известный запрос, дождитесь ответа Администрации");
	}
?>
