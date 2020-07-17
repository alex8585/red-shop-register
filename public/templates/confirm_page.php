<div class="row">
	<div class="col-6">
        <?php if($errors): 	?>
            <?php foreach($errors->get_error_messages() as $error ): ?>
                <div class="alert alert-danger" role="alert"> <?php echo $error ?> </div>
            <?php endforeach; ?>
           
        <?php endif; ?>

        <?php if($messages): ?>
            <?php foreach($messages as $message ): ?>
                <div class="alert alert-success" role="alert"> <?php echo $message ?> </div>
            <?php endforeach; ?>
        <?php else: ?>
            <form id="registerform" action="" method="post">
                <input type="hidden" name="rsr_action" value="confirm_email_send" >   
                <p>
                    <label for="user_email">
                        <?php  _e( 'E-mail', 'red-shop-register') ?> 
                        <input type="email" name="user_email" id="user_email" class="input" value="" >
                    </label>
                </p>
                <p class="submit">
                    <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" 
                        value="<?php   _e( 'Send', 'red-shop-register')?>">
                </p>
            </form>
        <?php endif; ?>    
	</div>
</div>