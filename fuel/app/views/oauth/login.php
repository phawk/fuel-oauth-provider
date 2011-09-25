		<p class="login_error"><?=Session::get_flash('login_failed');?></p>
		<form action="<?=Uri::current();?>?oauth_token=<?=Input::get('oauth_token');?>" method="post">
			<ol>
				<li>
					<label for="email">Email</label>
					<input type="email" name="email" id="email" value="<?=Input::post('email');?>" class="textfield" />
				</li>
				<li>
					<label for="password">Password</label>
					<input type="password" name="password" id="password" class="textfield" />
				</li>
				<li>
					<input type="submit" value="Register" class="button" />
				</li>
			</ol>
		</form>