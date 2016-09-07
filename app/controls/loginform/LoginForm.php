<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 19/7/16
 * Time: 6:24 PM
 */
class LoginForm
{
	public function view() {
		$ctrlID = uniqid();
		?>
		<form class="login-form" id="control-<?php echo $ctrlID; ?>" action="#" method="POST">
			<div class="input-group">
				<span class="input-group-addon"><i class="fa fa-user"></i></span>
				<input type="text" class="form-control" name="username" placeholder="Username">
			</div>
			<br/>
			<div class="input-group">
				<span class="input-group-addon"><i class="fa fa-lock"></i></span>
				<input type="password" class="form-control" name="password" placeholder="Password">
			</div>
			<br/>
			<input type="submit" value="Login" class="btn btn-block btn-success">
		</form>
		<script>
			var ctrlID = '<?php echo $ctrlID; ?>';
			console.log("Control " + ctrlID + " bound as a form");
			$("#control-"+ctrlID).submit(function(e) {
				e.preventDefault();
				$.ajax({
					url: '/ajax/auth_login',
					method: 'POST',
					data: $(this).serialize(),
					success: function(response) {
						var data = $.parseJSON(response);
						if(data.success) {
							console.log("Logged in!");
							window.location.href = '/';
						} else {
							console.log(data);
							//window.location.href = '/';
						}
					}
				})
			});
		</script>
		<?php
	}
}