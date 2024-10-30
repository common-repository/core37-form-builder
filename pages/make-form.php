 <!--Form settings bar-->
    <div class="c37-col-xs-12" id="form-general-settings">
        <div class="c37-col-xs-3" id="logo"><h4><i class="fa fa-bank"></i> C37 Form Builder</h4></div>
	    <div class="c37-col-xs-7 pull-right c37-col-md-offset-2">
		    <ul id="c37-top-menu">
			    <li id="c37-go-pro">
				    <a style="color: #ff7c25;" href="http://core37.com/get-core37-form-builder-pro-ultimate-wordpress-contact-us-form-builder/" target="_blank"><i class="fa fa-credit-card"></i> Get PRO</a>
			    </li>

			    <li>
				    <a id="open-form-settings" href="#"><i class="fa fa-gear"></i> &nbsp;Form</a>
			    </li>

			    <li>
				    <a id="save-form" href="#"><i class="fa fa-save"></i> &nbsp;Save</a>
			    </li>

			    <li>
				    <a id="get-forms" href="#"><i class="fa fa-pencil"></i> &nbsp;Edit</a>
			    </li>

			    <li>
				    <a href="<?php echo admin_url(); ?>" target="_blank"><i class="fa fa-dashboard"></i> &nbsp;Dashboard</a>
			    </li>

		    </ul>

	    </div>
    </div>
    <div class="c37-builder c37-fb">
            <!--List of elements-->
            <!--data-original: if the element is on the panel or not-->
            <div id="elements-panel">
                <h4>Most common</h4>
                <div>
                    <div data-original="true" data-c37-type="row" data-c37-layout="12" class="c37-container-element c37-row"><i class="fa fa-reorder"></i> Row</div>

                    <div data-original="true" data-c37-type="text" class="c37-form-element c37-item-element"><i class="fa fa-i-cursor"></i> Input field</div>

                    <div data-original="true" data-c37-type="checkbox" class="c37-form-element c37-item-element"><i class="fa fa-check-square"></i> Checkboxes</div>
                    <div data-original="true" data-c37-type="radio" class="c37-form-element c37-item-element"><i class="fa fa-dot-circle-o"></i> Radio buttons</div>
                    <div data-original="true" data-c37-type="label" class="c37-form-element c37-item-element"><i class="fa fa-bookmark-o"></i> Label</div>
                    <div data-original="true" data-c37-type="textarea" class="c37-form-element c37-item-element"><i class="fa fa-text-width"></i> Textarea</div>
                    <div data-original="true" data-c37-type="image" class="c37-form-element c37-item-element c37-premium"><i class="fa fa-image"></i> Image</div>
                    <div data-original="true" data-c37-type="stars" class="c37-form-element c37-item-element c37-premium"><i class="fa fa-star"></i> Stars</div>
	                <div data-original="true" data-c37-type="button" class="c37-form-element c37-item-element"><i class="fa fa-toggle-right"></i> Button</div>
                </div>

                <h4>Other</h4>
                <div>
                    <div data-original="true" data-c37-type="heading" class="c37-form-element c37-item-element"><i class="fa fa-font"></i> Heading</div>
                    <div data-original="paragraph" data-c37-type="paragraph" class="c37-form-element c37-item-element"><i class="fa fa-newspaper-o"></i> Paragraph</div>
                    <div data-original="true" data-c37-type="date" class="c37-form-element c37-item-element"><i class="fa fa-calendar"></i> Date input</div>
                    <div data-original="true" data-c37-type="select" class="c37-form-element c37-item-element"><i class="fa fa-list-ul"></i> Select box</div>
                    <div data-original="true" data-c37-type="file" class="c37-form-element c37-item-element"><i class="fa fa-cloud-upload"></i> File upload</div>
                    <div data-original="true" data-c37-type="recaptcha" class="c37-form-element c37-item-element"><i class="fa fa-unlock-alt"></i> ReCaptcha</div>
                    <div data-original="true" data-c37-type="acceptance" class="c37-form-element c37-item-element"><i class="fa fa-check-square-o"></i> Acceptance</div>
                </div>
<!--	            <h4>Multi-Step</h4>-->
<!--	            <div>-->
<!--		            <div data-original="true" data-c37-type="step" class="c37-form-multi-element"><i class="fa fa-step-forward"></i> Step</div>-->
<!--		            <div data-original="true" data-c37-type="progress-bar" class="c37-form-multi-element"><i class="fa fa-signal"></i> Progress Bar</div>-->
<!--	            </div>-->

            </div>

            <!--Main builder-->
            <div id="construction-site">
                <form style="width: 500px; background-color: #ffffff;" class="c37-form c37-container c37-form-style-1">

					<!--c37-style-1 is the default style of form. We have more than just one style-->
                    <div class="c37-step c37-container"> <!-- Since we supports multiple-step forms, each c37-step is a single step. All elements (rows) are inside this class -->
						<!--data-c37-layout: row layout-->
	                    <div class="c37-row" id="c37-row-0" data-c37-layout="12">
                            <!--place a default box to drop element-->
                            <div class="c37-box c37-col-md-12"></div>
                        </div>
                    </div>

                </form>

            </div>

			<!-- display the setting windows of current element -->
			<!-- options-window contains element settings, row settings, step settings, form
			settings, column settings-->
		    <div id="options-window">

		    </div>

        </div>
	<!-- this part print out server settings such as captcha keys and other stuffs -->
	<script>
		var serverSettings = {
			recaptchaSiteKey: "<?php echo get_option('c37_recaptcha_site_key'); ?>",
			mailTemplatesURL: "<?php echo admin_url('admin.php?page=core37-form-builder-mail-templates')?>",
			subscribersURL: "<?php echo admin_url('admin.php?page=core37-form-builder-mail-templates')?>"
		}
	</script>