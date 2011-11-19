#!/usr/bin/perl
BEGIN {
	use FindBin;
	unless ($ARGV[0]){
		print "Usage: $FindBin::Script appname\n";
		exit;
		
	}
	$appname=$ARGV[0];
}
use File::Path;
use File::Copy;

unless (-d $appname){
	print "Creating Directory $appname\n";
	mkpath($appname);
	$dir_to_copy=$FindBin::Dir . "/skel";
	$cmd="rsync -av $dir_to_copy/ $appname/";
	print "Copying files to the newly created app\n";
	print $cmd, "\n";
	system($cmd);
} else {
	print "Directory $appname already exists. Not doing anything destructive\n";
}
