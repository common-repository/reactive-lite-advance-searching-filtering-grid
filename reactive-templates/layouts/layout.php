<?php

add_action('reactive_preview_template', 'reactive_preview_template_plugin');

function reactive_preview_template_plugin() {

?>

	<script type="text/html" id="tmpl-grid_simple-template">
		<div class="reactive-container-fluid">
		  <# if(data.view == 'list') { #>
		  <div class="reactive-row reactiveGridBlock {{ data.listClass }}">
		    <# } else { #>
		    <div class="reactive-row reactiveGridBlock">
		      <# } #>
		      <# _.each(data.posts, function( post ) { #>
		        <# if(data.view == 'list') { #>
			      <div key=src={{ post.ID }} data-uid={{ post.ID }} class="{{data.listColumnClass}} reativeinfoWindowPopUp reactiveGridType-Simple fadeIn" data-wow-duration=".5s" data-wow-delay={{ post.delay}} >
			    <# } else { #>
			      <div key=src={{ post.ID }} data-uid={{ post.ID }} class="{{data.columnClass}} reativeinfoWindowPopUp reactiveGridType-Simple fadeIn" data-wow-duration=".5s" data-wow-delay={{ post.delay}} >
			    <# } #>
		          <div class="reactiveGridImage">
							<# if(post.thumb_url) { #>
		                      <img src={{ post.thumb_url }} alt="Image">
		                    <# } else {#>
		                      <img src={{ data.gridPlaceHolder }} alt="Image">
		                    <# } #>
		              <h3 class="reactiveTitle">{{ post.post_title }}</h3>
					<span class="reactiveDate">{{post.post_formated_date}}</span>
		         </div>
				</div>
			<# }) #>
		   </div>
		</div>
	</script>

<?php }
