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
	copy("./skel/*", $appname);
}