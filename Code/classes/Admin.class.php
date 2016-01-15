<?php

class Admin {

	public static function adminDash(){
		return ['dashboard' => Sql::dashboard()];
	}
}