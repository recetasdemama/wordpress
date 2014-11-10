<?php
		if ( (function_exists( 'of_get_option' )) && (of_get_option('slidetitle5',true) !=1) ) {
		if ( ( of_get_option('slider_enabled') != 0 ) && ( (is_home() == true) || (is_front_page() == true) ) )  
			{ ?>
		<div class="slider-parent">	
		<div class="slider-wrapper theme-default container"> 
	    	<div class="ribbon"></div>    
	    		<div id="slider" class="nivoSlider">
	    			<?php
			  		$slider_flag = false;
			  		for ($i=1;$i<6;$i++) {
			  			$caption = ((of_get_option('slidetitle'.$i, true)=="")?"":"#caption_".$i);
						if ( of_get_option('slide'.$i, true) != "" ) {
							echo "<div class='slide'><a href='".esc_url(of_get_option('slideurl'.$i, true))."'><img src='".of_get_option('slide'.$i, true)."' title='".$caption."'></a></div>"; 
							$slider_flag = true;
						}
					}
					?>  
	    		</div><!--#slider-->
	    		<?php for ($i=1;$i<6;$i++) {
	    				$caption = ((of_get_option('slidetitle'.$i, true)=="")?"":"#caption_".$i);
	    				if ($caption != "")
	    				{
		    				echo "<div id='caption_".$i."' class='nivo-html-caption'>";
		    				echo "<a href='".esc_url(of_get_option('slideurl'.$i, true))."'><div class='slide-title'><span>".of_get_option('slidetitle'.$i, true)."</span></div></a>";
		    				if ( of_get_option('slidedesc'.$i, true) != "" ) {
		    					echo "<div class='slide-description'>".of_get_option('slidedesc'.$i, true)."</div>";
		    				}
		    				echo "</div>";
	    				}
	    			}	
	    	    
				?>
	    </div><!--.container-->	
		</div><!--.slider-parent-->
		<?php 
				}
			}
			?>	