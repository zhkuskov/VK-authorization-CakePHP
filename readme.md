/**
 * Configures VK authorization
 * bootstrap.php
 *
 * component Auth must on
 */
Configure::write('vkAuth', array(
	'app_id' => '1231231',
	'app_secret' => 'abcdABCD123abcdABCD',
 	'urlGetCode' => 'http://oauth.vk.com/authorize',
 	'urlGetToken' => 'https://oauth.vk.com/access_token',
 	'urlGetUserInfo' => 'https://api.vk.com/method/users.get',    
));
