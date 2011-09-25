<?php

class Model_OauthConsumer extends Orm\Model
{
	protected static $_properties = array(
		'id',
		'key',
		'secret',
		'active',
		'name',
		'description',
		'image',
		'homepage',
		'contact_email',
		'author',
		'platform',
		'callback_url',
		'user_id',
		'date_created',
		'created_by',
		'last_updated',
		'last_updated_by',
	);
	
	public function check_nonce($nonce)
	{
		$query = Model_OauthConsumerNonce::find()->where('nonce', $nonce);
		return ($query->count() === 1);
	}
	
	public function add_nonce( $nonce )
	{
		if ( ! $this->id) return false;
		
		$new_nonce = new Model_OauthConsumerNonce();
		$new_nonce->consumer_id = $this->id;
		$new_nonce->timestamp = date('Y-m-d H:i:s');
		$new_nonce->nonce = $nonce;
		$new_nonce->save();
		
		return $new_nonce->id;
	}
	
}