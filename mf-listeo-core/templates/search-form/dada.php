<?php
if(!empty($_GET[$data->name])) {
	$value = sanitize_text_field($_GET[$data->name]);
} else if(get_query_var($data->name)) {
	$value = sanitize_text_field($_GET[$data->name]);
} else{
	$value = '';
} 
?>
<div class="<?php if(isset($data->class)) { echo esc_attr($data->class); } ?> <?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?>">
	<div id="autocomplete-container">
		<input  autocomplete="off" name="<?php echo esc_attr($data->name);?>" id="<?php echo esc_attr($data->name);?>" type="text" placeholder="<?php echo esc_attr($data->placeholder);?>" value="<?php if(isset($value)){ echo $value;  } ?>"/>
	</div>
	<a href="#"><i title="<?php esc_html_e('Find My Location','listeo_core') ?>" class="tooltip left fa fa-map-marker"></i></a>
	<span class="type-and-hit-enter"><?php esc_html_e('type and hit enter','listeo_core') ?></span>
</div>
