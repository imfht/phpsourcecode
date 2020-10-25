#!/usr/bin/perl -w

# comm/header.inc.pl

use strict;
use warnings;
use CGI;

use inc::Config;

use constant DBL_TBL => "\t\t";

my $r = CGI->new(); # @todo, refer: http://perldoc.perl.org/CGI.html

my $i = 0; 
my $argvsize = scalar @ARGV;
my %hmf = (); # runtime container
$hmf{'out'} = ''; 
$hmf{'fmt'} = '';
$hmf{'r'} = \$r;
$hmf{'i'} = $i;

print "\t".__FILE__.": starting....  @".time()."\n";
print "\t".__FILE__.": argc:$argvsize ARGV:\n";
for($i=0; $i<@ARGV; $i++){
	my $v = $ARGV[$i];
	print "\t\ti:$i v:[".$v."]\n";
}
my $params = $ARGV[0];
if(!defined($params)){ $params = ''; }
elsif($params=~/^\?/){ $params = substr($params,1); }
if($params=~/&amp;/){ $params = ~s/&amp;/&/g; }
my @param_list = split(/&/, $params);
my $param_size = scalar @param_list;
for($i=0; $i<$param_size; $i++){
	my $v = $param_list[$i];
	print "\t\t".__FILE__.": i:$i v:".$v."\n";	
	if($v=~/=/){
		my @v2 = split(/=/, $v);
		$v2[1] = defined($v2[1])?$v2[1]:'';
		$hmf{$v2[0]} = $v2[1];
	}
}

$ARGV[$argvsize] = \%hmf; # return to parent

# tools.func
require "./comm/tools.function.pl";

#debug(\%hmf);

$ARGV[$argvsize] = \%hmf; # return to parent

1;
