<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Voltronix Login</h2>
        <?php if (isset($errors)): ?>
            <div class="alert alert-danger"><?php echo $errors; ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo site_url('web/Login/authenticate'); ?>">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" class="form-control" value="<?php echo set_value('username'); ?>" required>
                <?php echo form_error('username', '<div class="text-danger">', '</div>'); ?>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" class="form-control" required>
                <?php echo form_error('password', '<div class="text-danger">', '</div>'); ?>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>
