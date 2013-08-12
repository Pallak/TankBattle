
<!DOCTYPE html>

<html>
	
	<head>
	<link href="<?= base_url()?>css/template.css" rel="stylesheet">
	
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
	<script>
		$(function(){
			$('#availableUsers').everyTime(500,function(){
					$('#availableUsers').load('<?= base_url() ?>arcade/getAvailableUsers');

					$.getJSON('<?= base_url() ?>arcade/getInvitation',function(data, text, jqZHR){
							if (data && data.invited) {
								var user=data.login;
								var time=data.time;
								if(confirm('Battle ' + user)) 
									$.getJSON('<?= base_url() ?>arcade/acceptInvitation',function(data, text, jqZHR){
										if (data && data.status == 'success')
											window.location.href = '<?= base_url() ?>combat/index'
									});
								else  
									$.post("<?= base_url() ?>arcade/declineInvitation");
							}
						});
				});
			});
	
	</script>
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
			<h1>Tank Battle</h1>
		
			<div>
				Hello!! <?= $user->fullName() ?>  <?= anchor('account/logout','(Logout)') ?>  <?= anchor('account/updatePasswordForm','(Change Password)') ?>
				</div>
				
			<?php 
				if (isset($errmsg)) 
					echo "<p>$errmsg</p>";
			?>
				<h2>Available Users</h2>
				<div id="availableUsers">
				</div>
			</div>	
		</div>
</body>

</html>

