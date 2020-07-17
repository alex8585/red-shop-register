
<div class="row">
	<div class="col-6">
		<?php if(!is_user_logged_in() ) : ?>
			<?php if($errors): 	?>
				<?php foreach($errors->get_error_messages() as $error ): ?>
				<div class="alert alert-danger" role="alert"> <?php echo $error ?> </div>
				<?php endforeach; ?>

				<?php if($not_confirmed): ?>
					<a href="<?php echo $confirm_page_url ?>"><?php _e( 'Confirm page url', RSR)?> </a>
				<?php endif;?>
			<?php endif; ?>

			<?php if($messages): ?>
				<?php foreach($messages as $message ): ?>
				<div class="alert alert-success" role="alert"> <?php echo $message ?> </div>
				<?php endforeach; ?>
			<?php else:?>
				<form id="login" action="<?php echo wp_login_url( $redirect ); ?>" method="post">
					<input type="hidden" name="rsr_action" value="login_form" >
					<p>
						<label for="log">
							<?php   _e( 'E-mail', 'red-shop-register')?>
							<input required type="email" name="log" id="log" class="input" value="<?php echo $email ?>" >
						</label>
					</p>
					<p>
						<label for="pwd">
							<?php _e( 'Password', 'red-shop-register')?> 
							<input required type="password" name="pwd" id="pwd" class="input" value="<?php echo $password ?>" >
						</label>
					</p>
					<p class="submit"><input type="submit" name="wp-submit" 
							id="wp-submit" class="button button-primary button-large" value="<?php _e( 'Login', RSR)?>"></p>
				</form>
			<?php endif; ?>
		<?php else:?>
			<h3>
				<?php _e( 'You already logged in', 'red-shop-register') ?>
			</h3>
		<?php endif; ?>
	</div>
</div>
	
