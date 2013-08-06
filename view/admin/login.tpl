import View\Admin\Index

{{REPLACE body}}
	<form action="/admin" method="post">
		<fieldset>
			<legend>Login</legend>
			<div class="form-group">
				<label>Username</label>
				<input type="text" class="form-control" name="username">
			</div>
			<div class="form-group">
				<label>Password</label>
				<input type="password" class="form-control" name="password">
			</div>
			<input type="submit" value="Login" class="btn btn-default">
		</fieldset>
	</form>
{{ENDREPLACE}}