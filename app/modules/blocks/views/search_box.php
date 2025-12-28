<form class="custom-search-form <?php echo $requests['class']; ?>" method="<?php echo $requests['method']; ?>" action="<?php echo $requests['action']; ?>">
	<div class="custom-search-container">
	  <div class="custom-search-wrapper">
	    <input type="text" class="custom-search-input" name="query" placeholder="<?=lang("Search_for_")?>" value="<?php echo get('query'); ?>">
	    <div class="custom-search-controls">
	     	<?php
	     		if (!get_role('user') && $data_search) {
	     	?>
	      	<select class="custom-search-select" name="search_type">
	      		<?php
	      			foreach ($data_search as $key => $row) {
	      		?>
		        <option value="<?php echo $key; ?>" <?php if(get('search_type') == $key) echo "selected"; ?>><?php echo $row; ?></option>
		        <?php }; ?>
	      	</select>
	        <?php }; ?>
	      	<button class="custom-search-button" type="submit"><i class="fe fe-search"></i></button>
	    </div>
	  </div>
	</div>
</form>