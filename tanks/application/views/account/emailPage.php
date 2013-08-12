
<!DOCTYPE html>

<html>
	<head>
		<link href="<?= base_url()?>css/template.css" rel="stylesheet">
	</head> 

	<body>  
		<div id = "loginContent">
			<div id = "loginHeader">
				<!-- TODO: change later to add image -->
				<h1>
					Tank Battle!!
				</h1>
			</div>
			
			<div id = "loginForm">
				<h1>Password Recovery</h1>
				
				<p>Please check your email for your new password.
				</p>
				
				
				<?php 
					if (isset($errorMsg)) {
						echo "<p>" . $errorMsg . "</p>";
					}
				
					echo "<p>" . anchor('account/index','Login') . "</p>";
				?>	
			</div>	
		</div>
	</body>
</html>

