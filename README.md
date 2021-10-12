# upload-file

Upload File Library For Backend/ServerSide PHP ( Simple &amp; Easy For Use ), Support Multiple Upload


```
$upload = new Upload;

$config = array(
    // define input file from html
    "file"=> ['file_user','image_user'],
    // target directory 
    "path"=> "power",
    // set image only or all files
    "only_image" => false,
    // skip upload when something is wrong
    "skip_error" => false
 );

$upload->init($config)
       ->start();
```


For client side use javascript.

https://github.com/lamhotsimamora/upload-js