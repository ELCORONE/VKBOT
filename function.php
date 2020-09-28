<?php
//Функция отправки сообщения пользователю
function SendMessageToUser($user_id, $message) { 
	$params['message'] = $message;
	$params['peer_id'] = $user_id;
	$params['access_token'] = API_TOKEN;
	$params['v'] = VK_API_VERSION;
	// Запрос на сервер с сообщением
	$get_params = http_build_query($params);// //Обработка массива в запрос и дальнейшая отправка
	file_get_contents('https://api.vk.com/method/messages.send?'. $get_params); // Отправка сообщения пользователю  
} 
// Обработка ответов от других API
function get_http_response_code($url) {
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
}

function GetWeather($weaher_url, $user_id, $user_city){
	$weather_data = json_decode(file_get_contents($weaher_url));
	$weaher_temp = $weather_data->main->temp;
	$weaher_pressure = $weather_data->main->pressure;
	$weaher_humidity = $weather_data->main->humidity;
	$weather_text_ico = $weather_data->weather[0]->main;
	$weaher_description = $weather_data->weather[0]->description;
	$weather_city = $weather_data->name;
			
	if($weather_text_ico == "Clear") $weaher_ico = "&#9728;";
	if($weather_text_ico == "Snow") $weaher_ico = "&#127784;";
	if($weather_text_ico == "Clouds") $weaher_ico = "&#9729;";
	if($weather_text_ico == "Rain") $weaher_ico = "&#127783;";
	if($weather_text_ico == "Mist") $weaher_ico = "&#127787;";
	SendMessageToUser($user_id,"
				Погода - $weather_city
							
				$weaher_ico $weaher_description
				Температура : $weaher_temp °C
				Давление : $weaher_pressure гПа
				Влажность : $weaher_humidity %
				");
}
?> 
