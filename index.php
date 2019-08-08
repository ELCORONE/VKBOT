<?php
/*

Генеально простой БОТ Вконтакте на чистейшем PHP
Версия 1.2.0708
Автор: Илья Корон

*/
if (!isset($_REQUEST)) {
	return;
}
require_once 'config.php'; // Файл настроек

$data = json_decode(file_get_contents('php://input'));	// Декодирования JSON-входящего сообщения с Вконтакте

//Функция отправки сообщения пользователю
function SendMessageToUser($user_id, $message) { 
	$params['message'] = $message;
	$params['user_id'] = $user_id;
	$params['access_token'] = API_TOKEN;
	$params['v'] = VK_API_VERSION;
	// Отправка на сервер
	$get_params = http_build_query($params);// //Обработка массива в запрос и дальнейшая отправка
	file_get_contents('https://api.vk.com/method/messages.send?'. $get_params); // Отправка сообщения пользователю  
} 

// Обработка событий по типу сообщения
switch ($data->type) {
	case 'confirmation':								// Сообщение подтверждения на странице настройки не удалять
		echo $confirmation_token;						// API Токен
		break;
	case 'message_new':									// Событие нового сообщения от пользователя
		$user_id = $data->object->user_id;				// ID Пользователя Вконтакте
		$usermsg = $data->object->body;					// Текст сообщения
		$user_info = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$user_id}&access_token={$token}&v=5.0"));
		$user_name = $user_info->response[0]->first_name;
		require_once 'messages.php';					// Подключение файла обработки сообщений от пользователя
		echo('ok');										// Отправка в Callback API сообщения о том всё всё 'ок'
		break;
	case 'group_leave':									// Событие когда пользователь покинул группу, ебать он конечно псина ебучая
        $user_id = $data->object->user_id;				// ID Пользователя Вконтакте
		$user_info = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$user_id}&access_token={$token}&v=5.0"));
		$user_name = $user_info->response[0]->first_name;
		SendMessageToUser($user_id,"Нам очень жаль {$user_name}, что Вы решили нас покинуть. <br> Будем рады видеть Вас.");
        echo('ok');										// Отправка в Callback API сообщения о том всё всё 'ок'
        break;
	case 'group_join':									//Событие когда пользователь присоеденился к группе
		$user_id = $data->object->user_id;				// ID Пользователя Вконтакте
		$user_info = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$user_id}&access_token={$token}&v=5.0"));
		$user_name = $user_info->response[0]->first_name;
		SendMessageToUser($user_id,"Добро пожаловать на паблик сервера Запретная Зона VDK.");
        echo('ok');										// Отправка в Callback API сообщения о том всё всё 'ок'
        break;
	case 'message_reply':								// Событие инициализации сообщения от бота, бля тут пиздец ребятки, ебать того рот
	    echo('ok');
        break;
} 
?> 
