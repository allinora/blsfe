#!/usr/bin/perl
BEGIN {
	use FindBin;
	unless ($ARGV[0]){
		print "Usage: $FindBin::Script module model id search_id\n";
		exit;
		
	}
	$name=$ARGV[0];
	$model=$ARGV[1];
	$id=$ARGV[2];
	$search=$ARGV[3];
}
use File::Path;
use File::Copy;

unless (-d $name){
	print "Creating Directory $name\n";
	mkpath($name);
	$dir_to_copy=$FindBin::Dir . "/coreskel";
	$cmd="rsync -av $dir_to_copy/ $name/";
	print "Copying files to the newly created app\n";
	docmd($cmd);
	docmd("perl -pi -e 's!%name%!@{[ucfirst($name)]}!g' $name/controllers/index.php");
	docmd("perl -pi -e 's!%name%!@{[ucfirst($name)]}!g' $name/controllers/admin.php");
	docmd("perl -pi -e 's!%model%!$model!g' $name/controllers/admin.php");
	docmd("perl -pi -e 's!%id%!$id!g' $name/controllers/admin.php");
	docmd("perl -pi -e 's!%search%!$search!g' $name/controllers/admin.php");
} else {
	print "Directory $name already exists. Not doing anything destructive\n";
}

sub docmd{
	my $cmd=shift;
	print $cmd, "\n";
	system($cmd);
}