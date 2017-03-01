<?php
/**
 * Created by PhpStorm.
 * User: danghongyang
 * Date: 2017/2/24
 * Time: 上午12:32
 */


require __DIR__ . '/../vendor/autoload.php';
require "Bmp.php";

use GifFrameExtractor\GifFrameExtractor;


function separateGIF2BMP($gifFilePath = '../img/target.gif', $Id)
{

    // check this is an animated GIF
    if (GifFrameExtractor::isAnimatedGif($gifFilePath)) {

        //输出
        mkdir("../output/{$Id}/", 0777, true);

        $gfe = new GifFrameExtractor();
        $gfe->extract($gifFilePath);


        foreach ($gfe->getFrames() as $index => $frame) {

            // The frame resource image var
            $img = $frame['image'];

            //调整宽度
            $img = imagescale($img, 144);

            //调转90度
            $img = imagerotate($img, 90, 0);

            imagebmp($img, "../output/{$Id}/bmp-$Id-$index.bmp", 24);
            unset($img);
        }
    }
}

function generateID()
{
    return time() * 1000 + rand();
}

$id = generateID();

if (isset($_FILES["gif"])) {

    $filename = $_FILES["gif"]["tmp_name"];

    separateGIF2BMP($filename, $id);

    //打包
    exec("cd .. &&  tar -zcf output/gif2bmp{$id}.tar.gz output/$id/");

    //删除文件
    exec("rm -rf ../output/{$id}/");

    header("Content-type: application/x-gzip");
    header("Content-Disposition: attachment; filename=gif2bmp{$id}.tar.gz");
    header("Content-Description: PHP3 Generated Data");

    $filename = '../output/gif2bmp{$id}.tar.gz';
    readfile("$filename");

} else {
    echo "empty upload";
}

