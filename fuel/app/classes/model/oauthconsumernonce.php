<?php

class Model_OauthConsumerNonce extends Orm\Model
{
	protected static $_properties = array(
		'id',
		'consumer_id',
		'timestamp',
		'nonce',
	);
}