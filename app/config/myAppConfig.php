<?php

function myAppConfig()
{
	return array(
					'debug'		=>	true,
					'it_email'	=>	'granados.carlos91@gmail.com',
					'db'		=>	array(
											'host'			=> 	'db1.launch3.net',
											'username'		=>	'l3t_user',
											'password'		=>	'4zgePHV3F55QdV2FLp',
											'database'		=>	'launch3_yii'
									),
					'log'		=>	realpath(__DIR__ . '/../logs/'),
					'uploads'	=>	realpath(__DIR__ . '/../uploads/'),
					'salt'		=>	'eW0@V@we(Rw.Jbg`FH_*o|R/_B/j8vQcW~%0,d1ye".m`&>_XNI!wzk_-bp!Cko',
					'require'	=> array(
											realpath(__DIR__) . '/app/models/sql/'; //Include all PHP files in directory
										)
				);
}