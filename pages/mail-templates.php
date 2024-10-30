<div id="c37-mail-templates">
	<div class="c37-col-xs-3">
		<label>List of forms</label>
		<select id="c37-forms-list">
			<?php
			$forms = C37FormManager::getAllForms();

			echo '<option></option>';
			foreach ($forms as $form)
			{ ?>
				<option class="c37-form-item" value="<?php echo $form['id']; ?>"> <?php echo $form['title']; ?> </option>
			<?php }


			?>
		</select>

		<label>List of fields</label>
		<ul id="c37-list-of-fields"></ul> <!-- list of fieds in a form -->
	</div> <!-- store the list of form and list of fields -->

	<div class="c37-col-xs-9" id="list-of-templates">

		<ul>
			<li><a href="#to-you">To you</a></li>
			<li><a href="#to-subscribers">To Subscribers</a></li>
		</ul>

		<div id="to-you">
			<input type="text" id="to-you-mail-title" placeholder="to you email subject" class="u-full-width" />
			<?php wp_editor("enter email content to you here", "to-you-email-editor", array(
				'media_buttons' => false,
				'editor_height' => '200'
			)); ?>

			<button class="save-email button-primary" id="save-to-you-email">Save to you email</button>

		</div>

		<div id="to-subscribers">
			<input type="text" id="to-subscribers-mail-title" placeholder="to subscribers email subject" class="u-full-width" />
			<?php wp_editor("enter email content to subscribers here", "to-subscribers-email-editor", array(
				'media_buttons' => false,
				'editor_height' => '200'
			)); ?>

			<button class="save-email button-primary" id="save-to-subscribers-email">Save to subscribers email</button>
		</div>
	</div>





</div>