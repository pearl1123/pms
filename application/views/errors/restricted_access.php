<?php
// application/views/errors/restricted_access.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Restricted</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>">
    <style>
        body { background: #f8f9fa; }
        .restricted-container {
            max-width: 500px;
            margin: 80px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 40px 30px;
            text-align: center;
        }
        .restricted-container h1 {
            font-size: 2.5rem;
            color: #dc3545;
        }
        .restricted-container p {
            margin: 20px 0 30px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="restricted-container">
        <h1>Access Restricted</h1>
        <p>You do not have permission to access this page.<br>
        Please contact your administrator if you believe this is an error.</p>
        <a href="javascript:history.back()" class="btn btn-primary">Go Back</a>
    </div>
</body>
</html>
