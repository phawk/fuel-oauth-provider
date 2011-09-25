<?php

class Controller_V1_Auth extends Controller
{
	
	
	public function action_request_token()
	{
		$provider = new Oauth_provider();
		$provider->set_to_request_token();
		$provider->check_request();
		
		echo $provider->generate_token('request');
	}
	
	public function action_access_token()
	{
		$provider = new Oauth_provider();
		$provider->check_request();
		
		echo $provider->generate_token('access');
	}
	
}