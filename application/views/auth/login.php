<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voltronix Login</title>
	<link rel="icon" href="<?php echo base_url('assets/photos/logo/favicon.png'); ?>" type="image/x-icon">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
		<div class="login-container">
			<div class="login-logo">
				<img src="<?= base_url('assets/photos/logo/voltronix_logo.png') ?>" alt="Voltronix Logo" style="width: 180px;">
			</div>
			<!-- <h2>Welcome to Voltronix</h2> -->
			<?php if (isset($errors)): ?>
				<div class="alert alert-danger"><?php echo $errors; ?></div>
			<?php endif; ?>
			
			<form method="POST" action="<?php echo site_url('web/Login/authenticate'); ?>">
				<div class="form-group">
					<label for="username">Username:</label>
					<input type="text" name="username" class="form-control" placeholder="Username" value="<?php echo set_value('username'); ?>" required>
					<?php echo form_error('username', '<div class="text-danger">', '</div>'); ?>
				</div>
				<div class="form-group">
					<label for="password">Password:</label>
					<input type="password" name="password" placeholder="Password" class="form-control" required>
					<?php echo form_error('password', '<div class="text-danger">', '</div>'); ?>
				</div>
				<button type="submit" class="btn btn-primary2 mt-2">Login</button>
			</form>
		</div>
	</div>
</body>
</html>
