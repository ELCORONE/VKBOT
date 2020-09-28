<?php
header('Content-Type: text/html; charset=utf-8');
if (!isset($_REQUEST)) {
	return;
}

require_once 'config.php'; // Файл настроек
require_once 'function.php'; // Файл настроек

$data = json_decode(file_get_contents('php://input'));	// Декодирования JSON-входящего сообщения с Вконтакте

if($data->secret != API_SECRET) {
	echo "Некоректный запрос";
	exit();
}

// Обработка событий по типу сообщения
switch ($data->type) {
	case 'confirmation':								// Сообщение подтверждения на странице настройки не удалять
		echo $confirmation_token;						// API Токен
		break;
	case 'message_new':									// Событие нового сообщения от пользователя
		// Данные получаемые из сообщения
		$user_ids = $data->object->from_id;				// ID Пользователя Вконтакте
		$user_id = $data->object->peer_id;
		$usermsg = $data->object->text;				// Текст сообщения
		
		$usermsg = mb_strtolower($usermsg);
		$usermsg = preg_replace('/\//', '', $usermsg);
		// Получение информации по пользователю
		$get_user_params['user_ids'] = $user_ids;
		$get_user_params['fields'] = 'city';
		$get_user_params['access_token'] = API_TOKEN;
		$get_user_params['v'] = VK_API_VERSION;
		// Запрос от сервера
		$user_params = http_build_query($get_user_params);// //Обработка массива в запрос и дальнейшая отправка
		$user_info = json_decode(file_get_contents('https://api.vk.com/method/users.get?'. $user_params));
		
		$user_name = $user_info->response[0]->first_name;
		if(empty($user_info->response[0]->city->title)) {
				$user_city = "Владивосток";
				$user_city_empty = "В вашем профиле не указан город, ниже показана погода для города паблика. 
									
									Если хотите получить погоду по своему городу
									1. Установите город в профиле
									2. Проверьте открыл ли профиль
									
									Или запросите погоду для определенного города:
									погода Владивосток";
			}
		else {
			$user_city = $user_info->response[0]->city->title;
			$user_city = mb_strtolower($user_city);
			$user_city_empty = "Мы определили, что Ваш город: ".$user_city;
		}
		require_once 'messages.php';					// Подключение файла обработки сообщений от пользователя
		echo('ok');		// Отправка в Callback API сообщения о том всё всё 'ок'
		break;
	case 'group_leave':									// Событие когда пользователь покинул группу, ебать он конечно псина ебучая
        $user_id = $data->object->user_id;				// ID Пользователя Вконтакте
		//GetUserInfo($user_id);
		SendMessageToUser($user_id,"Нам очень жаль, что Вы решили нас покинуть. <br> Будем рады видеть Вас.");
        echo('ok');										// Отправка в Callback API сообщения о том всё всё 'ок'
		break;
	case 'group_join':									//Событие когда пользователь присоеденился к группе
		$user_id = $data->object->user_id;				// ID Пользователя Вконтакте
		//GetUserInfo($user_id);
		SendMessageToUser($user_id,"Добро пожаловать в PIE Studio! \n Мы независимые разработчики <br>У нас есть сервер игры Counter-Strike: Global Offensive<br>Подключайтесь и играйте вместе с друзьями!<br>");
        echo('ok');										// Отправка в Callback API сообщения о том всё всё 'ок'
		break;
	// Событие инициализации сообщения от бота, бля тут пиздец ребятки, ебать того рот
	case  'message_reply': 
	echo('ok');		
	break;
}
?> 
