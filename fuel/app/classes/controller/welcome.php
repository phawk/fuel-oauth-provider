<?php

/**
 * The Welcome Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 * 
 * @package  app
 * @extends  Controller
 */
class Controller_Welcome extends Controller_Template {

	/**
	 * The index action.
	 * 
	 * @access  public
	 * @return  void
	 */
	public function action_index()
	{
		$this->template->title = 'fuel';
		$this->template->heading = 'You have successfully installed the Fuel PHP Framework.';
		$this->template->content = View::factory('welcome/index', array(
			'user_id' => Session::get('user_id')
		));
	}

	/**
	 * The 404 action for the application.
	 * 
	 * @access  public
	 * @return  void
	 */
	public function action_404()
	{
		$messages = array('Aw, crap!', 'Bloody Hell!', 'Uh Oh!', 'Nope, not here.', 'Huh?');

		// Set a HTTP 404 output header
		$this->response->status = 404;
		$this->template->title = $messages[array_rand($messages)];
		$this->template->heading = 'The page you are looking for cannot be found.';
		$this->template->content = View::factory('welcome/404');
	}
	
}

/* End of file welcome.php */