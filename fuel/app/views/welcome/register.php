		<form action="<?=Uri::current();?>" method="post">
			<ol>
				<li>
					<label for="display_name">Display name</label>
					<input type="text" name="display_name" id="display_name" value="<?=Input::post('display_name');?>" class="textfield" />
				</li>
				<li>
					<label for="full_name">Full name</label>
					<input type="text" name="full_name" id="full_name" value="<?=Input::post('full_name');?>" class="textfield" />
				</li>
				<li>
					<label for="email">Email</label>
					<input type="email" name="email" id="email" value="<?=Input::post('email');?>" class="textfield" />
				</li>
				<li>
					<label for="password">Password</label>
					<input type="password" name="password" id="password" class="textfield" />
				</li>
				<li>
					<label for="conf_pass">Confirm</label>
					<input type="password" name="conf_pass" id="conf_pass" class="textfield" />
				</li>
				<li>
					<input type="checkbox" name="terms" id="terms" value="accepted" />
					<label for="terms" class="inline">Please read and agree to the terms of use</label>
				</li>
				<li>
					<input type="submit" value="Register" class="button" />
				</li>
			</ol>
		</form>