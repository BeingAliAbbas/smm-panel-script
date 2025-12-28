<<<<<<< HEAD
<form class="custom-search-form <?php echo $requests['class']; ?>" method="<?php echo $requests['method']; ?>" action="<?php echo $requests['action']; ?>">
	<div class="custom-search-container">
	  <div class="custom-search-wrapper">
	    <input type="text" class="custom-search-input" name="query" placeholder="<?=lang("Search_for_")?>" value="<?php echo get('query'); ?>">
	    <div class="custom-search-controls">
	     	<?php
	     		if (!get_role('user') && $data_search) {
	     	?>
	      	<select class="custom-search-select" name="search_type">
=======
<form class="<?php echo $requests['class']; ?>" method="<?php echo $requests['method']; ?>" action="<?php echo $requests['action']; ?>">
	<div class="form-group">
	  <div class="input-group">
	    <input type="text" class="form-control" name="query" placeholder="<?=lang("Search_for_")?>" value="<?php echo get('query'); ?>">
	    <div class="input-group-append">

	     	<?php
	     		if (!get_role('user') && $data_search) {
	     	?>
	      	<select class="form-control" name="search_type">
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
	      		<?php
	      			foreach ($data_search as $key => $row) {
	      		?>
		        <option value="<?php echo $key; ?>" <?php if(get('search_type') == $key) echo "selected"; ?>><?php echo $row; ?></option>
		        <?php }; ?>
	      	</select>
	        <?php }; ?>
<<<<<<< HEAD
	      	<button class="custom-search-button" type="submit"><i class="fe fe-search"></i></button>
=======
	      	<button class="btn btn-secondary btn-searchItem" type="submit"><i class="fe fe-search"></i></button>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
	    </div>
	  </div>
	</div>
</form>