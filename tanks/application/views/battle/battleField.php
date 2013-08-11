
<!DOCTYPE html>

<html>
	<head>
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
	<script>

		function drawTanks(){		
			// draw on canvas
			var canvas = document.getElementById('canvas');
			var context = canvas.getContext('2d');

			context.clearRect(0, 0, canvas.width, canvas.height);
			context.fillStyle = '#000000';
			context.fillRect(0, 0, canvas.width, canvas.height);
			
			var tank1 = new Image();
			var tank2 = new Image();
			
			if(userCoords.x1!=-1 && userCoords.y1!=-1){
			    tank1.onload = function() {
			       context.drawImage(tank1, userCoords.x1, userCoords.y1);
			    };
		    }

			if(otherUserCoords.x1!=-1 && otherUserCoords.y1!=-1){
			    tank2.onload = function(){
					context.drawImage(tank2, otherUserCoords.x1, otherUserCoords.y1);
			    };
		    }

		    // TODO: changes these images!!!
			tank1.src = "<?= base_url() ?>images/green-tank.png";
			tank2.src = "<?= base_url() ?>images/red-tank.png";
		}
		
		function tankCoords(){
			this.x1 = -1;
			this.y1 = -1;
			this.x2 = -1;
			this.y2 = -1;
			this.angle = 0;
		}
		
		var otherUser = "<?= $otherUser->login ?>";
		var user = "<?= $user->login ?>";
		var status = "<?= $status ?>";
		var userCoords = new tankCoords();
		var otherUserCoords = new tankCoords();
		
		$(function(){
		
			// set up canvas for player who accepted the battle invite 
			if (status == 'battling'){
				
				// update tank coords
				var arguments = {x1:'20', y1:'400', x2:'480', y2:'430', angle:'0'};
				var url = "<?= base_url() ?>combat/postTankCoords";
				$.post(url,arguments, function (data,textStatus,jqXHR){
					//invitee
					meta = $.parseJSON(data);
					userCoords.x1 = meta.x1;
					userCoords.y1 = meta.y1;
					userCoords.x2 = meta.x2;
					userCoords.y2 = meta.y2;
					userCoords.angle = meta.angle;
					// set up canvas for player whose battle invite got accepted
					drawTanks();
				});
			}
			
			$('body').everyTime(100,function(){
					if (status == 'waiting') {
						$.getJSON('<?= base_url() ?>arcade/checkInvitation',function(data, text, jqZHR){
								if (data && data.status=='rejected') {
									alert("Sorry, your invitation to battle was declined!");
									window.location.href = '<?= base_url() ?>arcade/index';
								}
								if (data && data.status=='accepted') {
									status = 'battling';
									$('#status').html('Battling ' + otherUser);
									//inviter
									//update tanks coords
									var arguments = {x1:'900', y1:'20', x2:'70', y2:'20', angle:'180'};
									var url = "<?= base_url() ?>combat/postTankCoords";
									$.post(url,arguments, function (data,textStatus,jqXHR){
										//invitie
										meta = $.parseJSON(data);
										userCoords.x1 = meta.x1;
										userCoords.y1 = meta.y1;
										userCoords.x2 = meta.x2;
										userCoords.y2 = meta.y2;
										userCoords.angle = meta.angle;
										
										// set up canvas for player whose battle invite got accepted
										drawTanks();
									});
								}
								
						});
					} else if (status == 'battling'){
						// get tank coords
						var url = "<?= base_url() ?>combat/getTankCoords";
						$.getJSON(url, function (data,text,jqXHR){
							if (data && data.status=='success') {
								var coords = data.coords;
								if (coords.x1 != -1 && coords.y1 != -1 &&
									coords.x2 != -1 && coords.y2 != -1){
										otherUserCoords.x1 = coords.x1;
										otherUserCoords.y1 = coords.y1;
										otherUserCoords.x2 = coords.x2;
										otherUserCoords.y2 = coords.y2;
										drawTanks();										
								}
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
			var keyMap = { left:37, up:38, right:39, down:40 };
			var arguments = {x1:userCoords.x1, y1:userCoords.y1, x2:userCoords.x2, y2:userCoords.y2, angle:userCoords.angle};
			
			switch(keyCode){
				case keyMap.left:
					arguments.x1 = parseInt(userCoords.x1) - 20;
					arguments.x2 = parseInt(userCoords.x2) - 20;
					break;

				case keyMap.right:
					arguments.x1 = parseInt(userCoords.x1) + 20;
					arguments.x2 = parseInt(userCoords.x2) + 20;
					break;

				case keyMap.up:
					arguments.y1 = parseInt(userCoords.y1) - 20;
					arguments.y2 = parseInt(userCoords.y2) - 20;
					break;

				case keyMap.down:
					arguments.y1 = parseInt(userCoords.y1) + 20;
					arguments.y2 = parseInt(userCoords.y2) + 20;
					break;	
			}

			var url = "<?= base_url() ?>combat/postTankCoords";
			$.post(url,arguments, function (data,textStatus,jqXHR){
				//invitie
				meta = $.parseJSON(data);
				userCoords.x1 = meta.x1;
				userCoords.y1 = meta.y1;
				userCoords.x2 = meta.x2;
				userCoords.y2 = meta.y2;
				console.log(meta);
				// set up canvas for player whose battle invite got accepted
				drawTanks();
			});
		});
	</script>
	</head> 
	
	<body>  
		<h1>Battle Field</h1>
		
		<canvas id="canvas" height="500px" width="1000px" style="border:5px solid #ff0000;"></canvas>
	
		<div>
		Hello <?= $user->fullName() ?>  <?= anchor('account/logout','(Logout)') ?>  <?= anchor('account/updatePasswordForm','(Change Password)') ?>
		</div>
		
		<div id='status'> 
		<?php 
			if ($status == "battling")
				echo "Battling " . $otherUser->login;
			else
				echo "Waiting on " . $otherUser->login;
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

