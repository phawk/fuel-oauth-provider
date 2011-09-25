<?php

class Model_OauthToken extends Orm\Model
{
	protected static $_properties = array(
		'id',
		'type',
		'consumer',
		'token',
		'token_secret',
		'callback',
		'verifier',
		'user',
		'state',
		'date_created',
		'created_by',
		'last_updated',
		'last_updated_by',
	);
}