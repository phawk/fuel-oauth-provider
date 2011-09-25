<?php

class Controller_V1_User extends Controller
{
	
	
	public function action_profile()
	{
		$provider = new Oauth_provider();
		$provider->check_request();
		
		$user = Model_User::find($provider->get_user());
		
		$data = array(
			'id' => $user->id,
			'display_name' => $user->display_name,
			'full_name' => $user->full_name,
			'joined' => $user->date_created
		);
		
		$this->response->set_header('Content-Type', 'application/json');
		
		echo json_encode($data);
	}
	
}