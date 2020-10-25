#!/usr/bin/perl -w

# see desc at the bottom
# invoke by cli:
# /path/to/perl -w /path/to/index.pl "?mod=hello&act=say&fmt=json"
# or
# ./index.pl "?mod=hello&act=say&fmt=json"

use lib '../'; # @todo
use lib './';

use strict;
use Cwd qw(abs_path realpath);
use File::Basename qw(dirname basename);
use Time::HiRes qw(sleep time);
use Fcntl qw(:flock);
use POSIX qw(strftime);
use Encode qw(decode_utf8 encode_utf8);
#use Date::Parse;
#use JSON;
use autodie;
use CGI; # @todo

use utf8;
no warnings 'utf8';
binmode( STDIN,  ':encoding(utf8)' );
binmode( STDOUT, ':encoding(utf8)' );
binmode( STDERR, ':encoding(utf8)' );

my $mydir = dirname(abs_path($0));
my $basename = basename($0,(".pl"));
my $singlerun = 0;
chdir($mydir);

# main body

# header
require("./comm/header.inc.pl");

my $argvsize = scalar @ARGV;
my %hmf = (); # runtime container
%hmf = %{$ARGV[$argvsize-1]}; # return from controller
$hmf{'workdir'} = $mydir;

my $i = $hmf{'i'};
my $mod = $hmf{'mod'}; # hello
my $act = $hmf{'act'}; 
my $r = $hmf{'r'}; # CGI
my $out = $hmf{'out'};

# single instance
print "workdir:[".$mydir."]\tbasename:[".$basename."]\n";
if($singlerun == 1){
	my $gotoNext = 1;
	open(LOCK,">".$mydir."/".$basename.".".$mod.".".$act.".lock") || die $!;
	flock(LOCK,LOCK_EX|LOCK_NB) || warn ("Another $basename?mod=$mod&act=$act is running, exit...".($gotoNext=0)."\n");
	if($gotoNext == 0){
		print "Script stopping....\n";
		exit(0);
	}
}

$hmf{'out'} = $out;
$ARGV[$argvsize-1] = \%hmf; # $hmf; prepare for controller
if(!defined($mod)){ $mod = ''; }
if($mod eq ''){ $mod = 'index'; }

#debug(\@ARGV);

# mod, ctrl
require "./ctrl/$mod.pl";

# footer
require("./comm/footer.inc.pl");

if($singlerun == 1){
	close(LOCK);
	unlink($mydir."/".$basename.".".$mod.".".$act.".lock") or warn "remove lock file failed:[$!].\n";
}
