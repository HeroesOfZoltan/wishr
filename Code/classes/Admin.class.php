<?php

class Admin {

	public static function start(){
		return ['admin' => TRUE, 'dashboard' => Sql::dashboard()];
	}
}