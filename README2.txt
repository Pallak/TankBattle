HOW IT WORKS:

ARTIFICIAL DELAYS
For each failed login attempted, the session stores a counter. We then use the date variable to keep track of the last login attempt, convert the the previous date variable to seconds and compare it to the current date variable (which we also convert to seconds). If the previous date + 2^n (where n is the amount of attempted logins) is less than the current date, then we proceed to do password/username validation. Otherwise we display the required wait time.

CAPTCHA
Added captcha for creating account, run standard form validation procedure and use the captcha library function provided. We output to the user any mistakes, otherwise we update the database with the new user.

TANK MOVEMENT
tap UPKEY - move in facing direction
tap DOWNKEY - move in opposite direction
tap LEFTKEY - rotate the tank left, relative to direction facing
tap RIGHTKEY - rotate the tank right, relative to direction facing.
we use kineticJS (confirmed with prof that we wouldn't be penalized) to create 2 tank objects. We store the centre of the tank in the database, as well as the position (1: tank faces north, 2: tank faces east, 3: tank faces south, 4: tank faces west). We check if there is a change in position, based on the change of position we use the kineticJS animation to rotate the tank respectively and update the database. If the user pressed forward key or backward key, we move in the respective direction

TURRET MOVEMENT
tap A - rotate turret to the left
tap D - rotate turret to the right
We move the turret relative to the tank base, we use D and A to rotate by incriments of 30 degree. We store the degree as an integer between 0-11, where any given angle can be given by angle*30. We used kineticJS library to animate the rotations

BULLET MOVEMENT
SPACEBAR - shoot bullet in the direction of the turret
We use the turret as the starting point. We know the required slope because of the turret's angle. When the user shoots the bullet we decide to incriment of distances of 10 pixels (Not too fast, but not too slow). We animate it using KineticJS using simple translates until the ball is of the canvas. We determine the x coordinate by incrimenting the current x with x*cos(TURRETANGLE), we determine the y coordinate by incrimenting the current y with y*sin(TURRETANGLE).

HIT DETECTION AND GAME TERMINATION

Each translation of the bullet, we check if it is with in the tank's 'box'. If it is, we update the database. Making both players available, updating the game's values (who won). We the transfer both players back to arcade with a pop notifying the player on their victory/defeat.




*************************************************PROBLEMS************************************************



-Minor delay between loading tanks, tanks are relatively large image and is 'heavylifting'

-Race conditions on the database causes jasonParse errors, we eliminated the majority of these errors by limiting the amount when it is possible to read/write to/from the database. As a result we had to sacrifice tank movements which may appear slow, but gives a real tank-like experience :).
