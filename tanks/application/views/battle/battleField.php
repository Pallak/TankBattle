
<!DOCTYPE html>

<html>
	<head>
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
	<script src="http://d3lp1msu2r81bx.cloudfront.net/kjs/js/lib/kinetic-v4.5.5.min.js"></script>
	<script>

		function translateTank(layer, tank, arguments, turret){

			var url = "<?= base_url() ?>combat/postTankCoords";
			$.post(url,arguments, function (data,textStatus,jqXHR){
				//invitie
				meta = $.parseJSON(data);
				userCoords.x1 = meta.x1;
				userCoords.y1 = meta.y1;
				userCoords.x2 = meta.x2;
				userCoords.y2 = meta.y2;

				var duration = 1000;
				var anim = new Kinetic.Animation(function(frame) {
	                  if (frame.time >= duration) {
	                  	anim.stop();
	                  	isUserTankAnimated = false;
	                  } else{
		                tank.setX(userCoords.x1);
						tank.setY(userCoords.y1);
		                turret.setX(userCoords.x1);
						turret.setY(userCoords.y1);	                  
					  }
	                }, layer);
	            anim.start();
			});	
		}

		function rotateTank(layer, tank, arguments, isClockwise){
			var url = "<?= base_url() ?>combat/postTankCoords";
			$.post(url,arguments, function (data,textStatus,jqXHR){
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
							tank.rotate((-1)*(Math.PI/2 - sum));
	                    }
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

		function rotateTurret(layer, turret, arguments, isClockwise){
			var url = "<?= base_url() ?>combat/postTankCoords";
			$.post(url,arguments, function (data,textStatus,jqXHR){
				meta = $.parseJSON(data);
				userCoords.angle = meta.angle;

				var angularSpeed = Math.PI / 6;
				var duration = 1000;
				var sum = 0;
				var anim = new Kinetic.Animation(function(frame){
					if (frame.time > duration) {
	                    anim.stop();
	                    if(isClockwise){
							turret.rotate(Math.PI/6 - sum);
	                    } else{
							turret.rotate((-1)*(Math.PI/6 - sum));
	                    }
	                    layer.draw();
	                    isUserTankAnimated = false;
					} else {
						var angleDiff = frame.timeDiff * angularSpeed / 1000;
						if(isClockwise == 1){
							turret.rotate(angleDiff);
							sum += angleDiff;
						} else {
							turret.rotate((-1)*angleDiff);
							sum += angleDiff;
						}
					}
				}, layer);

				anim.start();

			});
		}

		function rotateOtherTank(layer, tank, isClockwise){
			angularSpeed = Math.PI / 2;
			var duration = 1000;
			var sum = 0;
			var anim = new Kinetic.Animation(function(frame){
				if(frame.time > duration) {
					anim.stop();
					if(isClockwise){
						tank.rotate(Math.PI/2 - sum);
					} else {
						tank.rotate((-1)*(Math.PI/2 - sum));
					}
					layer.draw();
				} else {
					var angleDiff = frame.timeDiff * angularSpeed / 1000;
					if (isClockwise == 1){
						tank.rotate(angleDiff);
						sum += angleDiff;
					} else {
						tank.rotate((-1)*angleDiff);
						sum += angleDiff;
					}
				}
			}, layer);

			anim.start();
		}

		function rotateOtherTurret(layer, turret, isClockwise){
            if(isClockwise){
				turret.rotateDeg(30);
            } else{
				turret.rotateDeg((-1)*30);
            }

			layer.draw();
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
		         * USER'S TANK
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

				  var imageObj3 = new Image();
				  	imageObj3.onload = function(){
						var turret = new Kinetic.Image({
							x: 0,
							y: 0,
							image: imageObj3,
							width: 42,
							height: 88,
							offset: [21, 44] 
						});
				  
				      	  tank.rotateDeg(userCoords.x2*90);
				          tank.move(userCoords.x1, userCoords.y1);
				          turret.rotateDeg(userCoords.angle*30);
				          turret.move(userCoords.x1, userCoords.y1);
				          // add the shape to the layer
				          layer.add(tank);
				          layer.add(turret);
				          stage.add(layer);
		
				          
				          window.addEventListener('keydown', function(event) {
			        		var keyCode = event.keyCode || event.which;
				            var keyMap = { left:37, up:38, right:39, down:40, a:65, d:68, spacebar:32};
							var arguments = {x1:userCoords.x1, y1:userCoords.y1, x2:userCoords.x2, y2:userCoords.y2, angle:userCoords.angle};
							
						  	if(isUserTankAnimated == false){
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
										translateTank(layer, tank, arguments, turret);
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
										translateTank(layer, tank, arguments, turret);
										break;
	
									case keyMap.a:
										isUserTankAnimated = true;
										arguments.angle = (parseInt(userCoords.angle) == 0) ?11 :(parseInt(userCoords.angle)-1);
										rotateTurret(layer, turret, arguments, 0);
										break;
			
									case keyMap.d:
										isUserTankAnimated = true;
										arguments.angle = (parseInt(userCoords.angle) == 11) ?0 :(parseInt(userCoords.angle)+1);
										rotateTurret(layer, turret, arguments, 1);
										break;
			
									case keyMap.spacebar:
											break;	
								}
						  	}
					     });
			  }

				imageObj3.src = "<?= base_url() ?>images/green-turret.png";
			  };
			        
		        imageObj.src = "<?= base_url() ?>images/green-tank.png";

		        /*
		         * OTHER USER'S TANK
		         * 
		         */

		        var imageObj2 = new Image();
		        imageObj2.onload = function() {
		          var otherTank = new Kinetic.Image({
		            x: 0,
		            y: 0,
		            image: imageObj2,
		            width: 50,
		            height: 64,
		            offset: [25, 32]
		          });

				  var imageObj4 = new Image();
				  	imageObj4.onload = function(){
						var otherTurret = new Kinetic.Image({
							x: 0,
							y: 0,
							image: imageObj4,
							width: 42,
							height: 88,
							offset: [21, 44] 
				  	});

		          otherTank.move(-100, -100);
		          otherTurret.move(-100, -100);
		          // add the shape to the layer
		          layer.add(otherTank);
		          layer.add(otherTurret);
		          layer.draw();


		          $('#container').everyTime(1000, function() {
		        	  var url = "<?= base_url() ?>combat/getTankCoords";
						$.getJSON(url, function (data,text,jqXHR){
							if (data && data.status=='success') {
								var coords = data.coords;
								if (coords.x1 != -1 && coords.y1 != -1 &&
									coords.x2 != -1 && coords.y2 != -1){
										otherUserCoords.x1 = coords.x1;
										otherUserCoords.y1 = coords.y1;

										if(isInitialRotation){
											otherUserCoords.x2 = coords.x2;
											otherUserCoords.angle = coords.angle;
											console.log("INITIAL ANGLE SET------------------"+coords.angle);
											isInitialRotation = false;
											otherTank.rotateDeg(parseInt(coords.x2)*90);
											otherTurret.rotateDeg(parseInt(coords.angle)*30);
										} else {
											if(coords.x2 != otherUserCoords.x2){
												if(otherUserCoords.x2 == 3 && coords.x2 == 0){
													rotateOtherTank(layer, otherTank, 1);
												} else if (otherUserCoords.x2 == 0 && coords.x2 == 3){
													rotateOtherTank(layer, otherTank, 0);
												} else if(otherUserCoords.x2 > coords.x2){
													rotateOtherTank(layer, otherTank, 0);
												} else {
													rotateOtherTank(layer, otherTank, 1);
												}
												otherUserCoords.x2 = coords.x2;
											}
											if(coords.angle != otherUserCoords.angle){
												if(otherUserCoords.angle == 11 && coords.angle == 0){
													rotateOtherTurret(layer, otherTurret, 1);
												} else if (otherUserCoords.angle == 0 && coords.angle == 11){
													rotateOtherTurret(layer, otherTurret, 0);
												} else if(parseInt(otherUserCoords.angle) > parseInt(coords.angle)){
													rotateOtherTurret(layer, otherTurret, 0);
												} else {
													rotateOtherTurret(layer, otherTurret, 1);
												}
												otherUserCoords.angle = coords.angle;												
											}
										}

										// translation for other tank and other turret
										otherTank.setX(otherUserCoords.x1);
										otherTank.setY(otherUserCoords.y1);
										otherTurret.setX(otherUserCoords.x1);
										otherTurret.setY(otherUserCoords.y1);
										layer.draw();
								}
							}
						});
		          });
				  }
			     imageObj4.src = "<?= base_url() ?>images/red-turret.png";
					
			  };

		        imageObj2.src = "<?= base_url() ?>images/red-tank.png";
		}
			
		function tankCoords(){
			this.x1 = -1;
			this.y1 = -1;
			this.x2 = -1;
			this.y2 = -1;
			this.angle = -1;
		}
		
		var otherUser = "<?= $otherUser->login ?>";
		var user = "<?= $user->login ?>";
		var status = "<?= $status ?>";
		var userCoords = new tankCoords();
		var otherUserCoords = new tankCoords();
		var isUserTankAnimated = false;
		var isInitialRotation = true;

		
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
									status = 'battling';
									
									$('#status').html('Battling ' + otherUser);
									//inviter
									//update tanks coords
									var arguments = {x1:'900', y1:'45', x2:'2', y2:'20', angle:'6'};
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
										drawTank();
									});
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

