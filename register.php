<div id="registration">
	<form action="" method="post" onsubmit="checkReg(); return false;" id="regForm">
	<ul>
		<li><h2>Sign up</h2></li>
		<li style="height:auto;"><p>It only takes seconds</p></li>
		<li><span id="checkUsernameStatus"></span><input type="text" name="username" id="regUsername" placeholder="Your Username" onblur="checkUsername('registration #regUsername');" class="checkReg"></li>
		<li><span id="checkPassword"></span><input type="password" name="password" id="password" placeholder="Your Password" class="checkReg"></li>
		<li><input type="password" name="password" id="passwordRepeat" placeholder="Repeat Password" class="checkReg"></li>
		<li class="captchaContainer"></li>
		<li class="captchaContainer"><img src="inc/plugins/captcha/image.php" class="captcha"> <input type="text" name="captcha" class="border-radius checkReg" id="captcha" style="width:34px; margin-top: -20px;" placeholder="="></li>
		
		<li class="captchaContainer"><a href="#" onclick="$('.captcha').attr('src', 'inc/plugins/captcha/image.php?dummy='+ new Date().getTime()); return false">reload captcha</a></li>
		<li style="text-align:left; height: auto;"><input type="checkbox" class="checkRegBox">I accept the <a href="http://wiki.universeos.org/index.php?title=Policy" onclick="openUniverseWikiArticle('Policy'); return false;" target="blank">terms</a>.</li>
		<li><input type="submit" class="btn  btn-block btn-info" value="Sign Up"></li>
	</ul>
	</form>
	<div id="regLoad">
		<div id="circularG">
			<div id="circularG_1" class="circularG">
			</div>
			<div id="circularG_2" class="circularG">
			</div>
			<div id="circularG_3" class="circularG">
			</div>
			<div id="circularG_4" class="circularG">
			</div>
			<div id="circularG_5" class="circularG">
			</div>
			<div id="circularG_6" class="circularG">
			</div>
			<div id="circularG_7" class="circularG">
			</div>
			<div id="circularG_8" class="circularG">
			</div>
		</div> <!-- - See more at: http://cssload.net/#sthash.DdGahpKm.dpuf -->
		<h3 style="line-height:23px;">The universe is creating your keypair now, this may take some minutes</h3>
	</div>
</div>