# VK OAuth authorization from CakePHP 2.x with AuthComponent #

### Features ###

It is very simple controller from users. 

If you need to added other social network, you can use method 'addUser'. Call it in your specific method when OAuth provider return user data.

### How do I get set up? ###

Copy controller.

[Create](https://vk.com/editapp?act=create) your app in vk.com.

Add in your configures file (app/Config/bootstrap.php): 

```php

/**
 * Configures VK authorization
 */
Configure::write('vkAuth', array(
	'app_id' => 'app_id_for_your_app',
	'app_secret' => 'app_secret_for_your_app',
 	'urlGetCode' => 'http://oauth.vk.com/authorize',
 	'urlGetToken' => 'https://oauth.vk.com/access_token',
 	'urlGetUserInfo' => 'https://api.vk.com/method/users.get',    
));

```

Create table:

```sql
CREATE TABLE `users` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`first_name` VARCHAR(255) NOT NULL,
	`last_name` VARCHAR(255) NOT NULL,
	`email` VARCHAR(255) NOT NULL,
	`vk_key` INT(11) UNSIGNED NOT NULL,
	`gender` VARCHAR(1) NOT NULL,
	`birthday` DATE NOT NULL,
	`photo` VARCHAR(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=3
;
```

Add link /users/auth_vk/ in your app.
