<?php

function ftp_rmdirr($h, $dir) { // (FTP CONNECTION, DIR) RECURSIVE
	foreach (ftp_nlist($h, $dir) as $file) {
		if ("." != $file && ".." != $file) {
			if (1 < count(ftp_nlist($h, $dir."/".$file))) {
				ftp_rmdirr($h, $dir."/".$file);
			} else {
				ftp_delete($h, $dir."/".$file);
			}
		}
	}
	if (2 == count(ftp_nlist($h, $dir))) {
		ftp_rmdir($h, $dir);
	}
}
