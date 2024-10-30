<div id="c37-subscribers" class="c37-container-fluid">
	<div id="c37-subscribers-control" class="c37-col-md-6 c37-col-md-offset-6 pull-right">
		<button id="clean" title="clear all subscribers data for current form"><i class="fa fa-trash-o"></i> Clean</button>
		<button id="prev" title="previous items"><i class="fa fa-chevron-circle-left"></i> Prev</button>
		<button id="next" title="next items">Next <i class="fa fa-chevron-circle-right"></i></button>

	</div>
	<div id="c37-list-forms" class="c37-col-md-2">
		<h1>Forms</h1>
		<?php
			include_once plugin_dir_path(__FILE__).'/../inc/c37-form-manager.php';
			include_once plugin_dir_path(__FILE__).'/../inc/c37-form-subscribers-manager.php';


			//get all forms and show them here
			$forms = C37FormManager::getAllForms();
			echo '<ul>';
			foreach ($forms as $form)
			{ ?>
				<li class="c37-form-item" form-id="<?php echo $form['id']; ?>"> <?php echo $form['title']; ?> </li>
			<?php }
			echo '</ul>';
		?>
	</div>

	<div  class="c37-col-md-10">
		<table class="u-full-width pure-table"  id="c37-list-subscribers">

		</table>
	</div>


</div>