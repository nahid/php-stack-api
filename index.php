<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
	<title>Stackoverflow</title>
</head>
<body>
<a href="https://stackexchange.com/oauth?client_id=<?= $_config['client_id']; ?>&redirect_uri=<?= $_config['redirect_uri']; ?>&scope=">Authectication</a>
<a href="https://stackexchange.com/oauth/dialog?client_id=<?= $_config['client_id']; ?>&key=<?= $_config['key']; ?>&redirect_uri=<?= $_config['redirect_uri']; ?>">Authectication</a>

</body>
</html>