<?php include_once plugin_dir_path(__FILE__) .'../inc/encrypt.php'; $crypt = new C37_Crypt(); ?>

<div id="c37-form-settings-page">

	<div id="tabs">
		<ul id="tab-pills">
			<li><a href="#main">Main</a></li>
			<li><a href="#recaptcha">ReCaptcha</a> </li>
			<li><a href="#email-settings">Email</a></li>

		</ul>

		<div id="main">
			<h1>Thanks for choosing Core37 form builder</h1>
			<p>If you have any question, please send a message here: <a target="_blank" href="http://core37.com/contact">contact us</a></p>
			<p>Please spend a few minutes watching how to use the form <a target="_blank" href="https://www.youtube.com/playlist?list=PLlMUKQq5jx5-VhzRWuZsJtQwzQBjRn2cK">here</a></p>
			<p>Also, please check our FAQ page to find the answers for common questions: <a target="_blank" href="http://core37.com/ufaq-category/form-builder/">FAQ page</a></p>


			<!-- PRO version activation -->
<!--			<h2>Please activate pro features</h2>-->
<!--			<label>Your PayPal email <small style="color: #888;">(or the email you used when making the purchase)</small></label>-->
<!--			<input type="text" placeholder="please enter your PayPal email" id="user-email" name="email" />-->
<!--			<label>Your license key <small style="color: #888;">(the one we sent to your email, please check the spam folder if you don't see it)</small></label>-->
<!--			<input type="text" name="license_key" id="license-key" placeholder="please enter your license key (we sent to your email)" />-->
<!--			<p>If you can't find your license key, you can <a href="" target="_blank">lookup here</a> or <a target="_blank" href="">contact us</a></p>-->
<!--			<button id="activate-button" class="primary">Activate</button>-->

			<!-- PRO version activation -->


		</div>

		<div id="recaptcha">
			<h2>ReCaptcha keys</h2>
			<div>
				<label for="site_key">Site key:</label>
				<input id="site_key" type="text" placeholder="enter recaptcha SITE key" value="<?php echo get_option('c37_recaptcha_site_key'); ?>" />
			</div>

			<div>
				<label for="secret_key">Secret key:</label>
				<input id="secret_key" type="text" placeholder="enter recaptcha SECRET key" value="<?php echo get_option('c37_recaptcha_secret_key'); ?>" />
			</div>

			<div>
				<button id="save_captcha_settings">Save captcha settings</button>
			</div>
		</div>
		<div id="email-settings">


			<div class="c37-col-md-6">
				<h2 class="section-header">Mail sender</h2>
				<label>Select a sender</label>
				<p>Mail senders are services used to you auto-reply to people who submit your forms and send notification to your email </p>

				<div><label><input id="default-mail" <?php echo in_array(get_option('c37_form_mail_sender'), array('default', false))? 'checked' : ''  ?> type="radio" name="sender" value="default" /> Default</label></div>
				<div><label><input id="gmail-mail" <?php echo get_option('c37_form_mail_sender') == 'gmail' ? 'checked' : '' ?> type="radio" name="sender" value="gmail" /> Gmail</label></div>
				<div><label><input id="smtp-mail" <?php echo get_option('c37_form_mail_sender') == 'smtp' ? 'checked' : '' ?> type="radio" name="sender" value="smtp" /> SMTP</label> </div>

			</div>

			<div class="c37-col-md-6">
				<div class="<?php echo in_array(get_option('c37_form_mail_sender'), array('default', false))? '' : 'hidden'  ?>" id="default-sender">
					<h3>Default option</h3>
					<p>Use WordPress' default mail settings. There is no configuration needed. However, you mails may end up in SPAM box</p>
					<button id="use-default-mail">Use default mail</button>

				</div>

				<div id="gmail-sender" class="<?php echo (get_option('c37_form_mail_sender') == 'gmail'? '': 'hidden') ?>">
					<h3>Gmail Settings</h3>
					<p>Use your Gmail account. This option has best chance to deliver mails to inbox</p>

					<label>Sender's name</label>
					<input type="text" value="<?php echo get_option('c37_form_gmail_sender_name'); ?>" id="gmail-sender-name" placeholder="enter the name you want to display as sender" />

					<label>Gmail username</label>
					<input type="text" value="<?php echo $crypt->decrypt(get_option('c37_form_gmail_username')); ?>" id="gmail-username" placeholder="enter your gmail address" />

					<label>Gmail password</label>
					<input type="password" value="<?php echo $crypt->decrypt(get_option('c37_form_gmail_password')); ?>" id="gmail-password" placeholder="enter your gmail password" />

					<button id="save-gmail-settings">Save Gmail Settings</button>
				</div>


				<div id="smtp-sender" class="<?php echo (get_option('c37_form_mail_sender') == 'smtp'? '': 'hidden') ?>">
					<h3>SMTP Settings</h3>
					<p>Custom SMTP settings. You can use your host SMTP settings or use third-party services such as sendgrid</p>

					<label>Sender's name</label>
					<input type="text" value="<?php echo get_option('c37_form_smtp_sender_name'); ?>" id="smtp-sender-name" placeholder="enter the name you want to display as sender" />

					<label>SMTP username</label>
					<input value="<?php  echo $crypt->decrypt((get_option('c37_form_smtp_username'))); ?>" id="smtp-username" type="text" placeholder="SMTP username" />

					<label>SMTP password</label>
					<input value="<?php echo $crypt->decrypt(get_option('c37_form_smtp_password')); ?>" id="smtp-password" type="password" placeholder="SMTP password" />

					<label>SMTP host</label>
					<input value="<?php echo get_option('c37_form_smtp_host'); ?>" id="smtp-host" type="text" placeholder="SMTP host" />

					<label>SMTP port (default 25)</label>
					<input value="<?php echo get_option('c37_form_smtp_port'); ?>" id="smtp-port" type="number" placeholder="SMTP port number" />

					<button id="save-smtp-settings">Save SMTP Settings</button>


				</div>


			</div>


			<h2 class="section-header">Notification</h2>
			<div>
				<label for="receiving-email">Email to receive notification</label>
				<input value="<?php echo get_option('c37_receiving_email')?>" type="text" id="receiving-email" placeholder="enter your email">
				<button id="save-receiving-email">Save email</button>
			</div>


		</div>
	</div>

</div>
