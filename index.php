<?php
error_reporting(E_ALL);
//ini_set('display_errors',1);
$errors = array();

function customError($error_level,$error_message,$error_file,$error_line,$error_context) {
    global $errors;
    
    array_push($errors, "Line ".$error_line." - ".$error_message);
}
set_error_handler("customError");

$media_extensions = array("mp4", "mkv", "avi");
$root_directory = "/home/rtorrent/TV";

$series_list = array();

$list = scandir($root_directory);
foreach($list as $series) {
    if (is_dir($root_directory."/".$series) && ($series != ".") && ($series != "..")) {
        $media_files = NULL;
        scanForVideoFile($root_directory."/".$series,$media_files);
        $series_list[$series] = $media_files;
    }
}

function scanForVideoFile($dir, $media_files) { 
    if (!isset($media_files)) {
        $media_files = array();
    }
    
    $list = scandir($dir); 
    foreach($list as $file) { 
        $ext = pathinfo($dir."/".$file, PATHINFO_EXTENSION); 
        clearstatcache(); 
        if (is_dir($dir."/".$file) && ($file != ".") && ($file != "..")) { 
            scanForVideoFile($dir."/".$file, $media_files); 
        } else if (in_array($ext, $GLOBALS["media_extensions"])) { 
            $pathname = preg_replace('/\\.[^.\\s]{3,4}$/', '', $dir."/".$file);	
            array_push($media_files, $pathname); 
        } 
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container">
        <h1>Subs Uploader</h1>

        <blockquote>
            <p>Fine art is knowledge made visible</p>
            <footer>Peja</footer>
        </blockquote>
        
        <?php
if (isset($errors)) {
    foreach($errors as $error) {
        echo "<p class=\"bg-danger text-danger\">$error</p>";
    }
}
        ?>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Series Name</th>
                        <th>Series Episode</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
$series = array_keys($series_list);
foreach($series as $key) {
    $episode_list = $series_list[$key];
    $episode_size = count($episode_list);
    ?>
                    <tr>
                        <td>-</td>
                        <td colspan="2" rowspan="<?php echo $episode_size; ?>"><?php echo $key; ?></td>
                    </tr>
    <?php
    foreach($episode_list as $episode) {
?>
                    <tr>
                        <td>
                            <input type="radio" name="file" value="<?php echo $episode; ?>" />
                        </td>
                        <td>$episode</td>
                    </tr>
                    <?php
    }
}

if (count($series_list) == 0) {
?>
                    <tr>
                        <td colspan="3">No series found.</td>
                    </tr>
<?php
}
                    ?>
                </tbody>
            </table>
            <code>Found <?php echo count($series_list); ?> series.</code>
        </div>
    </div>
</body>

</html>