
<!DOCTYPE html>

<html>
	<head>
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
	<script>

		function drawTanks(user1, user2){
			var canvas = getDocumentById('canvas');
			var context = canvas.getContext('2d');

			context.clearRect(0, 0, canvas.width, canvas.height);
			var tank1 = new Image();
			var turret1 = new Image();
			
			var tank2 = new Image();
			var turret2 = new Image();

		    tank1.onload = function() {
		       context.drawImage(tank1, user1.x1, user1.y1);
		    };

		    tank2.onload = function(){
				context.drawImage(tank2, user2.x1, user2.y1);
		    };
		    
			tank1.src = <?php base_url();?> + "/images/green-tank.png";
			tank2.src = <?php base_url();?> + "/images/red-tank.png";
		}

		var otherUser = "<?= $otherUser->login ?>";
		var user = "<?= $user->login ?>";
		var status = "<?= $status ?>";
		var user1 = new Object;
		var user2 = new Object;
		
		$(function(){
			$('body').everyTime(2000,function(){
					if (status == 'waiting') {
						$.getJSON('<?= base_url() ?>arcade/checkInvitation',function(data, text, jqZHR){
								if (data && data.status=='rejected') {
									alert("Sorry, your invitation to battle was declined!");
									window.location.href = '<?= base_url() ?>arcade/index';
								}
								if (data && data.status=='accepted') {
									status = 'battling';
									$('#status').html('Battling ' + otherUser);
									console.log("**********************************************************");
								}
								
						});
					}
					var url = "<?= base_url() ?>combat/getMsg";
					$.getJSON(url, function (data,text,jqXHR){
						if (data && data.status=='success') {
							var conversation = $('[name=conversation]').val();
							var msg = data.message;
							if (msg.length > 0)
								$('[name=conversation]').val(conversation + "\n" + otherUser + ": " + msg);
						}
					});
			});

			$('form').submit(function(){
				var arguments = $(this).serialize();
				var url = "<?= base_url() ?>combat/postMsg";
				$.post(url,arguments, function (data,textStatus,jqXHR){
						var conversation = $('[name=conversation]').val();
						var msg = $('[name=msg]').val();
						$('[name=conversation]').val(conversation + "\n" + user + ": " + msg);
						});
				return false;
				});	
		});

		$(document).keydown(function(event){
			var keyCode = event.keyCode || event.which;
			
		});
	</script>
	</head> 
<body>  
	<h1>Battle Field</h1>
	
	<canvas id="canvas" height="500px" width="1000px" style="border:1px solid #000000;"></canvas>

	<div>
	Hello <?= $user->fullName() ?>  <?= anchor('account/logout','(Logout)') ?>  <?= anchor('account/updatePasswordForm','(Change Password)') ?>
	</div>
	
	<div id='status'> 
	<?php 
		if ($status == "battling")
			echo "Battling " . $otherUser->login;
		else
			echo "Wating on " . $otherUser->login;
	?>
	</div>
	
<?php 
	
	echo form_textarea('conversation');
	
	echo form_open();
	echo form_input('msg');
	echo form_submit('Send','Send');
	echo form_close();
	
?>
	
	
	
</body>

</html>

