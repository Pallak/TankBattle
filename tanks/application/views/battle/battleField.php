
<!DOCTYPE html>

<html>
	<head>
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
	<script src="http://d3lp1msu2r81bx.cloudfront.net/kjs/js/lib/kinetic-v4.5.5.min.js"></script>
	<script>

		function translateTank(layer, tank, arguments){

			var url = "<?= base_url() ?>combat/postTankCoords";
			$.post(url,arguments, function (data,textStatus,jqXHR){
				//invitie
				meta = $.parseJSON(data);
				userCoords.x1 = meta.x1;
				userCoords.y1 = meta.y1;
				userCoords.x2 = meta.x2;
				userCoords.y2 = meta.y2;

				var duration = 100;
				var anim = new Kinetic.Animation(function(frame) {
	                  if (frame.time >= duration) {
	                  	anim.stop();
	                  	isUserTankAnimated = false;
	                  } else{
		                tank.setX(userCoords.x1);
						tank.setY(userCoords.y1);
	                  }
	                }, layer);
	            anim.start();
			});	
		}

		function rotateTank(layer, tank, arguments, isClockwise){

			var url = "<?= base_url() ?>combat/postTankCoords";
			$.post(url,arguments, function (data,textStatus,jqXHR){
				//invitie
				meta = $.parseJSON(data);
				userCoords.x1 = meta.x1;
				userCoords.y1 = meta.y1;
				userCoords.x2 = meta.x2;
				userCoords.y2 = meta.y2;

                var angularSpeed = Math.PI / 2;
                var duration = 1000;
                var sum = 0;
                var anim = new Kinetic.Animation(function(frame) {
	                  if (frame.time > duration) {
	                    anim.stop();
	                    if(isClockwise){
							tank.rotate(Math.PI/2 - sum);
	                    } else{
							tank.rotate((-1)*(Math.PI/2 - sum))
	                    }
	                    console.log("***********************************");
		                console.log(sum);
		                layer.draw();
		                isUserTankAnimated = false;
	                  } else{
	                    var angleDiff = frame.timeDiff * angularSpeed / 1000;
	                    if (isClockwise == 1){
		                    tank.rotate(angleDiff);
		                    sum += angleDiff;
	                    } else{
		                    tank.rotate((-1)*angleDiff);
		                    sum += angleDiff;
	                    }
	                  }
                }, layer);

                anim.start();
			});	
		}
		
		function drawTank(){
		        var stage = new Kinetic.Stage({
		          container: 'container',
		          x:0,
		          y:0,
		          width: 1000,
		          height: 600
		        });
		        var layer = new Kinetic.Layer();

		        /*
		         * USER TANK
		         * 
		         */

		        var imageObj = new Image();
		        imageObj.onload = function() {
		          var tank = new Kinetic.Image({
		            x: 0,
		            y: 0,
		            image: imageObj,
		            width: 50,
		            height: 64,
		            offset: [25, 32]
		          });


		      	  tank.rotateDeg(userCoords.x2*90);
		          tank.move(userCoords.x1, userCoords.y1);
		          // add the shape to the layer
		          layer.add(tank);
		          stage.add(layer);

		          
		          window.addEventListener('keydown', function(event) {
			        if(isUserTankAnimated == false){
			            var keyCode = event.keyCode || event.which;
			            var keyMap = { left:37, up:38, right:39, down:40 };
						var arguments = {x1:userCoords.x1, y1:userCoords.y1, x2:userCoords.x2, y2:userCoords.y2, angle:userCoords.angle};
	
						switch(keyCode){
						case keyMap.left:
							isUserTankAnimated = true;
							arguments.x2 = (parseInt(userCoords.x2) == 0) ?3 :(parseInt(userCoords.x2)-1);
							rotateTank(layer, tank, arguments, 0);
							break;
	
						case keyMap.right:
							isUserTankAnimated = true;
							arguments.x2 = (parseInt(userCoords.x2) == 3) ?0 :(parseInt(userCoords.x2)+1);
							rotateTank(layer, tank, arguments, 1);
							break;
	
						case keyMap.up:
							isUserTankAnimated = true;
							switch(userCoords.x2){
								case "0":
									arguments.y1 = parseInt(userCoords.y1) - 20;
									break;
								case "1":
									arguments.x1 = parseInt(userCoords.x1) + 20;
									break;
								case "2":
									arguments.y1 = parseInt(userCoords.y1) + 20;
									break;
								case "3":
									arguments.x1 = parseInt(userCoords.x1) - 20;
									break;
							}
							translateTank(layer, tank, arguments);
							break;
							
						case keyMap.down:
							isUserTankAnimated = true;
							switch(userCoords.x2){
								case "0":
									arguments.y1 = parseInt(userCoords.y1) + 20;
									break;
								case "1":
									arguments.x1 = parseInt(userCoords.x1) - 20;
									break;
								case "2":
									arguments.y1 = parseInt(userCoords.y1) - 20;
									break;
								case "3":
									arguments.x1 = parseInt(userCoords.x1) + 20;
									break;
							}
							translateTank(layer, tank, arguments);
							break;	
						}
			        }	            
			     });
			  };
			        
		        imageObj.src = "<?= base_url() ?>images/green-tank.png";
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
		var isUserTankAnimated = false;
		var isOtherTankAnimated = false;
		
		$(function(){
		
			// set up canvas for player who accepted the battle invite 
			if (status == 'battling'){
				
				// update tank coords
				var arguments = {x1:'75', y1:'420', x2:'0', y2:'430', angle:'0'};
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
					drawTank();
				});
			}
			
			$('body').everyTime(1000,function(){
					if (status == 'waiting') {
						$.getJSON('<?= base_url() ?>arcade/checkInvitation',function(data, text, jqZHR){
								if (data && data.status=='rejected') {
									alert("Sorry, your invitation to battle was declined!");
									window.location.href = '<?= base_url() ?>arcade/index';
								}
								if (data && data.status=='accepted') {
									console.log(status == 'waiting');
									status = 'battling';
									
									$('#status').html('Battling ' + otherUser);
									//inviter
									//update tanks coords
									var arguments = {x1:'900', y1:'45', x2:'2', y2:'20', angle:'180'};
									var url = "<?= base_url() ?>combat/postTankCoords";
									$.post(url,arguments, function (data,textStatus,jqXHR){
										//invitie
										//console.log("***************GOT INVITED***************************************");
										meta = $.parseJSON(data);
										userCoords.x1 = meta.x1;
										userCoords.y1 = meta.y1;
										userCoords.x2 = meta.x2;
										userCoords.y2 = meta.y2;
										userCoords.angle = meta.angle;
										
										// set up canvas for player whose battle invite got accepted
										drawTank();
									});
								}
								
						});
					} else if (status == 'battling'){
						var z = 0;
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
										//drawTanks();										
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
	</script>
	</head> 
	
	<body>  
		<h1>Battle Field</h1>
		
		<div id="container" style = "background-color:black; width:1000px; height:600px; "></div>
	
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

