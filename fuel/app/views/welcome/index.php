		<h2 class="first">Version: <?php echo e(Fuel::VERSION); ?></h2>
		
		<?php if( ! is_null($user_id)): ?>
		<p>Welcome user: <?=$user_id;?></p>
		<?php else: ?>
		<p>The controller that is generating this page is located here:</p>
		
				<pre><code>APPPATH/classes/controller/welcome.php</code></pre>
		
				<p>This view can be located here:</p>
				
				<pre><code>APPPATH/views/welcome/index.php</code></pre>
		
				<h2>Now What?</h2>
		
				<p>Now that you have Fuel installed, here are a few links you might find useful:</p>
				
				<ul>
					<li><a href="http://fuelphp.com/docs" target="_blank">Documentation</a></li>
					<li><a href="http://fuelphp.com/" target="_blank">Official Website</a></li>
					<li><a href="http://github.com/fuel/fuel" target="_blank">GitHub Respository</a></li>
					<li><a href="http://fuelphp.com/contribute/issue-tracker" target="_blank">Issue Tracker</a></li>
				</ul>
		<?php endif; ?>