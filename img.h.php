<?php

	function img($source, $width = 200, $height = 150, $filename = "", $watermark = "", $wm_x = 10, $wm_y = 10) {

        // .GIF .JPG .PNG

        // $filename > WRITE IMAGE TO FILE
        // IF NOT SET > OUTPUT TO SCREEN

		if (! isset($width)) {$width = 200;}
		if (! isset($height)) {$height = 150;}

		$src_img = imagecreatefromjpeg($source);

	    // CALCULATE PROPORTION

		$src_w = imagesx($src_img);
		$src_h = imagesy($src_img);

        $scale_on = ($src_w >= $src_h) ? "x" : "y";

		if ("x" == $scale_on) {
			if ($src_w > $width) {
				$dst_w = $width;
				$ratio = $dst_w / $src_w;
				$dst_h = round($src_h * $ratio);
			} else {
				$dst_w = $src_w;
				$dst_h = $src_h;
			}
		} else {
			if ($src_h > $height) {
				$dst_h = $height;
				$ratio = $dst_h / $src_h;
				$dst_w = round($src_w * $ratio);
			} else {
				$dst_w = $src_w;
				$dst_h = $src_h;
			}
		}

	    // CREATE COPY

		$dst_img = imagecreatetruecolor($dst_w, $dst_h);
		imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
	
	    // WATERMARK

		if ($watermark) {
			$stamp = imagecreatefrompng($watermark);
			$sx = imagesx($stamp);
			$sy = imagesy($stamp);
			imagecopy($dst_img, $stamp, imagesx($dst_img) - $sx - $wm_x, imagesy($dst_img) - $sy - $wm_y, 0, 0, imagesx($stamp), imagesy($stamp));
		}

        // OUTPUT

		if ($filename) {
			imagejpeg($dst_img, $filename, 100);
		} else {
			header("Content-Type:image/jpeg");
			imagejpeg($dst_img);
		}

		imagedestroy($dst_img);
	}
