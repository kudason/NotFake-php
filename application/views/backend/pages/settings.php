<form method="post" action="<?php echo base_url();?>index.php?admin/settings" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-6">
	        <div class="panel panel-primary">
				<div class="panel-heading">
					<div class="panel-title" style="height: 20px;">
						
					</div>
				</div>
				<div class="panel-body">
					<div class="form-group mb-3">
						<label for="simpleinput1"><?php echo get_phrase('website_name'); ?></label>
						<input type="text" class="form-control" id = "simpleinput1" name="site_name" value="<?php echo $site_name;?>">
					</div>
					<div class="form-group mb-3">
						<label for="simpleinput2"><?php echo get_phrase('website_email'); ?></label>
						<input type="text" class="form-control" id = "simpleinput2" name="site_email" value="<?php echo $site_email;?>">
					</div>

					<div class="form-group mb-3">
                        <label for="example-select"><?php echo get_phrase('trial_period_functionality'); ?></label>
                        <select class="form-control" id="example-select" name="trial_period">
							<option value="on" <?php if ($trial_period == 'on')echo 'selected';?>>On</option>
							<option value="off" <?php if ($trial_period == 'off')echo 'selected';?>>Off</option>
                        </select>
                    </div>

					<div class="form-group mb-3">
						<label for="simpleinput3"><?php echo get_phrase('trial_period_number_of_days'); ?></label>
						<input type="number" min="0" class="form-control" id = "simpleinput3" name="trial_period_days" value="<?php echo $trial_period_days;?>">
					</div>

					<!-- WEBSITE LANGUAGE SETTINGS -->
					<div class="form-group mb-3">
                        <label for="example-select2"><?php echo get_phrase('website_language'); ?></label>
                        <select class="form-control" id="example-select2" name="language">
							<?php foreach ($languages as $language): ?>
				                <option value="<?php echo $language; ?>" <?php if(get_settings('language') == $language) echo 'selected'; ?>><?php echo ucfirst($language); ?></option>
				             <?php endforeach; ?>
                        </select>
                    </div>

					<!-- WEBSITE TEMPLATE SETTINGS -->
					<div class="form-group mb-3">
                        <label for="example-select3"><?php echo get_phrase('website_theme'); ?></label>
                        <select class="form-control" id="example-select3" name="theme">
							<?php
								$themes = directory_map('./application/views/frontend/', 1);
								//print_r($themes);
								for($i = 0; $i < sizeof($themes) ; $i++) {
									if ($themes[$i] == 'index.php')
										continue;
									$themes[$i] = substr($themes[$i], 0, -1);
									?>
									<option value="<?php echo $themes[$i];?>" <?php if ($theme == $themes[$i])echo 'selected';?>>
										<?php echo $themes[$i];?></option>
									<?php
								}
							?>
                        </select>
                    </div>

					
	            </div>
	        </div>
	    </div>

		<div class="col-md-6">
	        <div class="panel panel-primary">
				<div class="panel-heading">
					<div class="panel-title" style="height: 20px;">
						
					</div>
				</div>
				<div class="panel-body">
	            	<div class="form-group mb-3">
		            	<div class="row">
		            		<div class="col-md-6">
								<label class="form-label">Website logo</label>
								<span class="help"></span>
								<div class="controls">
									<input type="file" class="form-control" name="logo" />
								</div>
							</div>
							<div class="col-md-6 text-center" style="padding-top: 20px;">
								<img src="<?php echo base_url();?>assets/global/logo.png" height="20" />
							</div>
						</div>
					</div>

					
	            </div>
	        </div>
	    </div>
		<div class="col-md-12 text-left">
				<input type="submit" class="btn btn-primary" value="Update Website Settings">
		</div>
	</div>
</form>
	
<hr>

