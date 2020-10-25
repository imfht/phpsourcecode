#!/usr/bin/perl -w

# ctrl index

use strict;
use warnings;
use CGI;

use Date::Parse qw(str2time);
use utf8;
no warnings 'utf8';
binmode( STDIN,  ':encoding(utf8)' );
binmode( STDOUT, ':encoding(utf8)' );
binmode( STDERR, ':encoding(utf8)' );

use mod::AdOffer;

# main body
my $argvsize = scalar @ARGV;
my %hmf = (); # runtime container
%hmf = %{$ARGV[$argvsize-1]};

my $i = $hmf{'i'}; 
my $mod = $hmf{'mod'}; # hello
my $act = $hmf{'act'}; 
my $out = $hmf{'out'};
my $r = $hmf{'r'}; # CGI
my $workdir = $hmf{'workdir'};

my $adoffer = mod::AdOffer->new($ARGV[0], $ARGV[1]);

# act
if($act eq 'active_hour_log'){
	print "\t".__FILE__.": getting into act:[$act]\n";
	$adoffer->setTbl("mytbl");
	my $thedate = strftime("%Y-%m-%d", gmtime()); 
	my $thedate_nodash = strftime("%Y%m%d", gmtime()); 
	$adoffer->set('thedate', $thedate);
	$adoffer->set('pagesize', 1000000);
	my $hm = $adoffer->getBy('id,uid,ihour', 'thedate=? and icountry=""');
	my %hm = %{$hm};
	if($hm{0}){
		my @rows = @{$hm{1}};
		my @data = (); $i=0;
		push(@data, "id\tuid\tihour");
		foreach my $row (@rows){
			my %row = %{$row};
			push(@data, $row{'id'}."\t".$row{'uid'}."\t".$row{'ihour'});
		}
		my $datalist = join("\n", @data); 
		$out .= "read data count:[".(scalar @rows)."] for date:[".$thedate."] succ.\n";
		#$out .= ($datalist)."\n\n";

		my $file = $workdir.'/'.$adoffer->getConf('uploaddir').'/mycache'.$thedate_nodash.".txt";

		my %args = ('content'=>$datalist, 
			'target'=>$file);
		my $hmw = $adoffer->setBy('file:', \%args);
		my %hmw = %{$hmw};
		if($hmw{0}){
			print "\t".__FILE__.": write file:[".$args{'target'}."], content:[".$datalist."] succ.\n";	
			$out .= "write file:[".$file."] succ.\n";
		}
		else{
			print "\t".__FILE__.": write file:".$args{'target'}.", fail.\n";	
		}

		# print test
		#$adoffer->getEnv(\%args);
		# debug test 
		#my @arr = (1,2,3,9,12);
		#debug(\@arr, '@arr');
		#debug(\%args);

		my $hmr = $adoffer->getBy('file:', \%args); 
		my %hmr = %{$hmr};
		if($hmr{0}){
			print "\t".__FILE__.": read file:[".$file."] succ.\n";
			#print "\t".$hmr{1}{'content'}."\n";
		}
		else{
			print "\tread file fail.\n";	
		}
	}
	else{
		$out .= "read data failed. 201702151634.";	
	}
}
else{
	$out .= "Oooops! Unknown act:[$act]\n\n";	
}

$hmf{'out'} = $out;
$ARGV[$argvsize-1] = \%hmf; # return to parent

1;
