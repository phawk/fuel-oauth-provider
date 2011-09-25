<?php

class Controller_Oauth extends Controller_Template {
	
	public function action_register()
	{
		$val = Validation::factory();
		$val->add_field('email', 'Your email', 'required|valid_email');
		$val->add_field('display_name', 'Display name', 'required');
		$val->add_field('full_name', 'Full name', 'required');
		$val->add_field('password', 'Your password', 'required|min_length[6]|max_length[50]');
		$val->add_field('conf_pass', 'Confirm password', 'required|match_field[password]');
		$val->add_field('terms', 'Terms of use', 'required');
		
		if ($val->run())
		{
		    // process your stuff when validation succeeds
		    $user = new Model_User();
		    
		    // Create the bcrypt hash 15 rounds
		    $bcrypt = new Bcrypt(15);
		    
		    $user->display_name = $val->input('display_name');
		    $user->full_name = $val->input('full_name');
		    $user->email = $val->input('email');
		    $user->state = 0;
		    $user->password = $bcrypt->hash($val->input('password'));
		    $user->date_created = date('Y-m-d H:i:s');
		    $user->last_updated = date('Y-m-d H:i:s');
		    $user->save();
		    
		    Response::redirect('/welcome/login');
		}
		else
		{
		    // validation failed
		    $this->template->errors = $val->errors();
		    $this->template->title = 'User Registration';
		    $this->template->heading = 'Signup for a free account below';
			$this->template->content = View::factory('welcome/register', array(
				'errors' => $val->errors(),
				'inputs' => $val->input()
			));
		}
	}
	
	public function action_login()
	{
		$val = Validation::factory();
		$val->add_field('email', 'Your email', 'required|valid_email');
		$val->add_field('password', 'Your password', 'required');
		
		if ($val->run())
		{
			// Get the request token
			$get_request_token = Input::get('oauth_token');
		    
		    // Get the user
		    $query = Model_User::find()->where('email', $val->input('email'));
		    $user = $query->get_one();
		    
		    // Check user returned and check password
		    if ($query->count() == 0 || ! $user->check_password($val->input('password'))) {
				Session::set_flash('login_failed', 'Your email / password was wrong');
		    	Response::redirect(Uri::current());
		    	exit;
		    }
		    
		    // Get the request token
		    $query_request_token = Model_OauthToken::find()->where('token', $get_request_token);
		    $request_token = $query_request_token->get_one();
		    
		    // Get the consumer
		    $consumer = Model_OauthConsumer::find($request_token->consumer);
		    
		    $provider = new Oauth_provider();
		    
		    // Update the request token
		    $request_token->verifier = $provider->generate_verifier();
		    $request_token->user = $user->id;
		    $request_token->save();
		    
		    // Build callback url
		    $callback_url = $request_token->callback . "?&oauth_token=" . $request_token->token . "&oauth_verifier=" . $request_token->verifier;
		    
		    // Build data array
		    $data = array();
		    $data['user'] = $user;
		    $data['consumer'] = $consumer;
		    $data['callback_url'] = $callback_url;
		    $data['verifier'] = $request_token->verifier;
		    
		    
		    // Show the grant / deny access page
			$this->template->title = 'Hi '.$user->display_name;
			$this->template->heading = 'The application '.$consumer->name.' has requested access to your account.';
			$this->template->content = View::factory('oauth/access', $data);
		}
		else
		{
		    // validation failed
		    $this->template->errors = $val->errors();
		    $this->template->title = 'Welcome';
		    $this->template->heading = 'Sign in';
			$this->template->content = View::factory('oauth/login', array(
				'errors' => $val->errors(),
				'inputs' => $val->input()
			));
		}
	}
	
}