<?php

class Model_User extends Orm\Model
{
	protected static $_properties = array(
		'id',
		'display_name',
		'full_name',
		'email',
		'state',
		'password',
		'date_created',
		'created_by',
		'last_updated',
		'last_updated_by',
	);
	
	public function check_password($password)
	{
		$bcrypt = new Bcrypt(15);
		return $bcrypt->verify($password, $this->password);
	}
}