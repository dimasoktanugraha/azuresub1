<?php
require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=dimassubstorage;AccountKey=AgbqW1RBD7a3qmHILT3lSh9EEk3azTJE9DAkwg9m6gJsitbNqYqWX+DESpXkjjGHq2Hr1lX1i0RT+SDlZEDnUw==;";

$containerName = "blobdimas";

$blobClient = BlobRestProxy::createBlobService($connectionString);

if (isset($_POST['submit'])) {

    //upload file
	$fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
	$content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
	header("Location: upload.php");
}

//tampilkan list blob
$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
?>

<!DOCTYPE html>
<html>
 <head>
 <!-- Bootstrap core CSS -->
 <link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet">
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
</head>
<body style="padding: 20;">

        <div style="background-color:grey; padding: 20;">
            <a style="color:blue;" href="/index.php">Registration</a> |
            <a style="color:white;" href="/upload.php">Upload</a>
        </div>
        <br>
        <br>
		<div>
			<form action="upload.php" method="post" enctype="multipart/form-data">
				<input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="">
				<input type="submit" name="submit" value="Upload">
			</form>
		</div>
		<br>
		<br>
		<h4>Total Files : <?php echo sizeof($result->getBlobs())?></h4>
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>URL</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
				do {
					foreach ($result->getBlobs() as $blob)
					{
						?>
						<tr>
							<td><?php echo $blob->getName() ?></td>
							<td><?php echo $blob->getUrl() ?></td>
							<td><a href="vision.php?url=<?php echo $blob->getUrl()?>" class="btn btn-primary">Analize</a>					
                            </td>
						</tr>
						<?php
					}
					$listBlobsOptions->setContinuationToken($result->getContinuationToken());
				} while($result->getContinuationToken());
				?>
			</tbody>
		</table>
</body>
</html>