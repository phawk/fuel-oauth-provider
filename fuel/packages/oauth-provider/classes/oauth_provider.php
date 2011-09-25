<?php
/**
 * Oauth Provider package for Fuel PHP
 *
 * @package    Oauth Provider
 * @version    1.0
 * @author     Pete Hawkins <pete@phawk.co.uk>
 * @license    MIT License
 * @copyright  2011 Pete Hawkins
 * @link       http://phawk.co.uk/
 */

namespace Oauthprovider;

class Oauth_provider
{
	private $auth_url;
	private $token_length;
	
	private $provider;
	private $consumer;
	private $token;
	
	private $user;
	
	private $oauth_error = false;
	
	
	public static function create_consumer()
	{
		$data['key'] 	= sha1(\OAuthProvider::generateToken($this->token_length, true));
		$data['secret']	= sha1(\OAuthProvider::generateToken($this->token_length, true));
		
		return $data;
	}
	
	public function __construct()
	{
		// Set authentication url and token length
		\Config::load('oauth', true);
		$this->auth_url = \Config::get('oauth.authentication_url');
		$this->token_length = \Config::get('oauth.token_length');
		
		try {
			// Create a Provider instance
			$this->provider = new \OAuthProvider();
			
			// Setup callbacks
			$this->provider->timestampNonceHandler(array($this, 'check_timestamp_nonce'));
			$this->provider->consumerHandler(array($this, 'check_consumer'));
			$this->provider->tokenHandler(array($this, 'check_token'));
		}
		catch (OAuthException $e) {
			echo \OAuthProvider::reportProblem($e);
			$this->oauth_error = true;
			return $e;
		}
	}
	
	
	
	public function set_to_request_token()
	{
		$this->provider->isRequestTokenEndpoint(true); 
		$this->provider->addRequiredParameter('oauth_callback');
	}
	
	
	
	public function generate_verifier()
	{
		return sha1(\OAuthProvider::generateToken(20, true));
	}
	
	
	
	public function check_request()
	{
		try {
			// Check our oauth request
			$this->provider->checkOAuthRequest();
		}
		catch (OAuthException $e) {
			echo \OAuthProvider::reportProblem($e);
			$this->oauth_error = true;
			return $e;
		}
	}
	
	
	public function generate_token($type = 'request')
	{
		if ($this->oauth_error) return false;
		
		// Set the callback uri
		$callback_uri = $this->provider->callback;
		
		// Create a new token model
		$new_token = new \Model_OauthToken();
		
		// Generate a request token
		$token  = sha1(\OAuthProvider::generateToken($this->token_length, true));
		$secret = sha1(\OAuthProvider::generateToken($this->token_length, true));
		
		// Build the return string
		$return = "oauth_token=" . $token . "&oauth_token_secret=" . $secret;
		
		switch ($type)
		{
			case 'request':
				// Create the token
				$new_token->type = 'request';
				$new_token->consumer = $this->consumer->id;
				$new_token->token = $token;
				$new_token->token_secret = $secret;
				$new_token->callback = $callback_uri;
				
				$new_token->save();
				
				$return = 'authentication_url=' . $this->auth_url . '&' . $return;
			break;
			
			case 'access':
				// Invalidate the request token
				$query = \Model_OauthToken::find()->where('token', $this->provider->token);
				$old_token = $query->get_one();
				$old_token->state = 2;
				$old_token->save();
				
				// Store the access token in the db
				$new_token->type = 'access';
				$new_token->consumer = $this->consumer->id;
				$new_token->token = $token;
				$new_token->token_secret = $secret;
				$new_token->user = $old_token->user;
				$new_token->state = 0; // 0 = Active
				
				$new_token->save();
			break;
		}
		
		return $return;
	}
	
	
	
	public function check_timestamp_nonce($provider)
	{
		if ($this->provider->timestamp < time() - 5*60)
		{
			$this->oauth_error = true;
			return OAUTH_BAD_TIMESTAMP;
		}
		elseif ($this->consumer->check_nonce($provider->nonce))
		{
			$this->oauth_error = true;
			return OAUTH_BAD_NONCE;
		}
		
		$this->consumer->add_nonce($this->provider->nonce);
		
		return OAUTH_OK;
	}
	
	
	
	public function check_consumer($provider)
	{
		$query = \Model_OauthConsumer::find()->where('key', $provider->consumer_key);
		
		if ($query->count() == 0)
		{
			return OAUTH_CONSUMER_KEY_UNKNOWN;
		}
		
		// Get the consumer from $query
		$this->consumer = $query->get_one();
		
		if ($this->consumer->active != 0) // 0 is active, 1 is throttled, 2 is blacklisted
		{
			return OAUTH_CONSUMER_KEY_REFUSED;
		}
		
		// Pass the provider the consumers secret
		$provider->consumer_secret = $this->consumer->secret;
		
		return OAUTH_OK;
	}
	
	
	
	public function check_token($provider)
	{
		// Lookup token from database
		$query = \Model_OauthToken::find()->where('token', $provider->token);
		
		$this->token = $query->get_one();
		
		if ( ! $this->token->id )
		{
			// Token not found
			$this->oauth_error = true;
			return OAUTH_TOKEN_REJECTED;
		}
		elseif ($this->token->type == 'access' && $this->token->state == 1)
		{
			// Token has been revoked by user
			$this->oauth_error = true;
			return OAUTH_TOKEN_REVOKED;
		}
		elseif ($this->token->type == 'request' && $this->token->state == 2)
		{
			// Token has already been used and access token sent
			$this->oauth_error = true;
			return OAUTH_TOKEN_USED;
		}
		elseif ($this->token->type == 'request' && $this->token->verifier != $provider->verifier)
		{
			// Verifier is not the verifier that requested the token
			$this->oauth_error = true;
			return OAUTH_VERIFIER_INVALID;
		}
		
		if ($this->token->type == 'access')
		{
			// if this is an access token we register the user to this class for use in our api
			$this->user = $this->token->user;
		}
		
		$provider->token_secret = $this->token->token_secret;
		
		return OAUTH_OK;
	}
	
	
	
	public function get_user()
	{
		if ($this->user)
		{
			return $this->user;
		}
		else
		{
			$this->oauth_error = true;
			throw new \Exception("User not authenticated.");
		}
	}

}