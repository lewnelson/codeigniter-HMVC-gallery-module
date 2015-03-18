<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

?>

<div id="content">
	<?php

	foreach($module as $index=>$value)
	{
		echo Modules::run($module[$index]."/".$method[$index]);
	}
	
	?>
</div>