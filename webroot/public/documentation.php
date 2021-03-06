<?php
require_once '../../define_root.php';
require_once INCLUDE_ROOT."Model/Site.php";

require_once INCLUDE_ROOT . "View/header.php";
require_once INCLUDE_ROOT . "View/public_nav.php";


require_once INCLUDE_ROOT . "View/header.php";
require_once INCLUDE_ROOT . "View/public_nav.php";
?>
<div class="row">
	<div class="col-md-8">
		<h2>formr documentation</h2>
		<p class="lead">
			chain simple forms into longer runs,
			use the power of R to generate pretty feedback and complex designs
		</p>
		<p>
			Most documentation is inside formr – you can just get going and it will be waiting for you where you need it.
		</p>
	</div>
</div>
<div class="row">
	<div class="col-md-8">
	
		<ul class="nav nav-tabs">
		  <li><a href="#run_module_explanations" data-toggle="tab">Run modules</a></li>
		  <li><a href="#sample_survey_sheet" data-toggle="tab">Survey spreadsheet</a></li>
		  <li><a href="#sample_choices_sheet" data-toggle="tab">Choices spreadsheet</a></li>
		  <li><a href="#available_items" data-toggle="tab">Item types</a></li>
		  <li class="active"><a href="#features" data-toggle="tab">Features</a></li>
		</ul>
	
		<div class="tab-content">
			<div class="tab-pane fade" id="run_module_explanations">
					<?php
					require INCLUDE_ROOT.'View/run_module_explanations.php';	
					?>
			</div>
			<div class="tab-pane fade" id="sample_survey_sheet">
				<?php
				require INCLUDE_ROOT.'View/sample_survey_sheet.php';	
				?>
			</div>
			<div class="tab-pane fade" id="sample_choices_sheet">
				<?php
				require INCLUDE_ROOT.'View/sample_choices_sheet.php';	
				?>
			</div>
			<div class="tab-pane fade" id="available_items">
				<?php
				require INCLUDE_ROOT.'View/item_types.php';	
				?>

			</div>
			<div class="tab-pane fade in active" id="features">
				<h2>
					Features
				</h2>
				<h4>
					Good already:
				</h4>
				<ul class="fa-ul-more-padding">
					<li>
						does diary studies with automated reminders
					</li>
					<li>
						generates pretty feedback "live", including ggplot2 plots
					</li>
					<li>
						looks nice on your phone
					</li>
					<li>
						you can use R to do basically anything that R can do (complicated at times)
					</li>
					<li>
						manage access and eligibility to studies
					</li>
					<li>
						longitudinal studies
					</li>
					<li>
						easily share, swap and combine surveys (they're simply spreadsheets with survey questions)
					</li>
					<li>
						works on all somewhat modern devices and degrades gracefully where it doesn't
					</li>
					
				</ul>
				<h4>
					Plans:
				</h4>
				<ul class="fa-ul-more-padding">
					<li>
						send text messages
					</li>
					<li>
						work offline on mobile phones and other devices with intermittent internet access (in the meantime <a href="https://enketo.org/">enketo</a> is pretty good and free too, but geared towards humanitarian aid)
					</li>
					<li>
						easily share, swap and combine runs (in the future higher-level components like filters or diaries can be added with one click)
					</li>
					<li>
						a better API (some basics are there)
					</li>
					<li>
						file, image, video, sound uploads for users and admins (admins can simply use existing services in the meantime, everything can be embedded from elsewhere)
					</li>
					<li>
						offline condition evaluation - at the moment you need to submit a form to see conditional items, there is no Javascript evaluation
					</li>
					<li>
						social networks, round robin studies - at the moment they are a bit bothersome to implement, but possible. There is a dedicated module already which might also get released as open source if there's time. 
					</li>
				</ul>
				<h4>
					Might be nice:
				</h4>
				<ul class="fa-ul-more-padding">
					<li>
						use as app on Apple and Android devices, thus be able to use more OS functionality
					</li>
					<li>
						supporting Pushover's API (or something similar) to send push messages to a phone. You can already do this in an R call
					</li>
					<li>
						using <a href="https://github.com/ajaxorg/ace">Ace</a> for editing Markdown/Knitr in runs.
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
