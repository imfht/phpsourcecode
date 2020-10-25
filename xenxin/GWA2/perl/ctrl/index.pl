#!/usr/bin/perl -w

# ctrl index

use strict;
use warnings;

use utf8;
no warnings 'utf8';
binmode( STDIN,  ':encoding(utf8)' );
binmode( STDOUT, ':encoding(utf8)' );
binmode( STDERR, ':encoding(utf8)' );

use mod::Hello;

# refer: 
#	http://www.ttlsa.com/nginx/perl-fastcgi-nginx/
# 	http://search.cpan.org/~ether/FCGI-0.78/FCGI.pm

# main body
my $argvsize = scalar @ARGV;
my %hmf = (); # runtime container
%hmf = %{$ARGV[$argvsize-1]};

my $i = $hmf{'i'}; 
my $mod = $hmf{'mod'}; # hello
my $act = $hmf{'act'}; 
my $out = $hmf{'out'};
my $r = $hmf{'r'}; # CGI

$hmf{'i_in_ctrl/index'} = $i;

#_exec_in_child_(); # this func

#_exec_(); # parent's func

print "\tctrl/index: i:[$i] am now at the end of ctrl/index.\n";

$out .= "\tEntering into ctrl/index @".time()."\n\n";
$hmf{'out'} = $out;

$ARGV[$argvsize-1] = \%hmf; # return to parent

#
sub _exec_in_child_ {
	my $hello = mod::Hello->new($ARGV[0], $ARGV[1]);
	print "\tctrl/index: I am a func from $0 in ctrl/index:[".$ARGV[0]."].\n";	
	$hmf{'var_in__exec_in_ctr/index'} = "_exec: time: ".time()."";
	#$hello->sayHi($0);
	#$hello->setTbl('materials');
	$hello->set('pagesize', 2);
	my $result = $hello->getBy("*", "1=1", 123);
	my %result = %{$result};
	print "\t\t0-getBy-result-state:[".$result{0}."] rtn:[".$result{1}."] now:[".time()."]\n";	
	$hello->set('orderby', 'rand()');
	$hello->setId(18);
	$result = $hello->getBy("*", "id<?");
	%result = %{$result};
	print "\t\t1-getBy-result-state:[".$result{0}."] rtn:[".$result{1}."] now:[".time()."]\n";	
	if($result{0}){
		print "\t\tctrl/index: getBy succ.\n";	
	}
	my @rows = @{$result{1}};
	my $rowCount = scalar @rows;
	for($i=0; $i<$rowCount; $i++){
		print "\t\ti:$i\n";	
		my %row = %{$rows[$i]};
		foreach(keys %row){
			print "\t\t\tk:$_ v:".$row{$_}."\n";
			last;
		}
	}
	#my %rows = %{$result{1}};
	#foreach my $k (sort { $a <=> $b } keys %rows){
	#	print "\t\tctrl/index: 0-hm-k:$k v:".$rows{$k}."\n";
		#my %row = %{$rows{$k}};
		#foreach my $k2 (keys %row){
		#	print "\t\t\t1-hm-k:$k2 v:".$row{$k2}."\n";
		#}
	#}

	my $k = 'iname';
	$hello->set($k, 'value-a'.time());
	$hello->set('engname', 'value-of-english-name');
	$hello->set('iauthor', 'gwa2-in-perl-'.time());
	$hello->setId(35);
	$hello->setTbl('temptbl');
	print "\t\t\tget-key-a:[".$hello->get($k)."] id:[".$hello->get('iauthor')."] tbl:[".$hello->getTbl()."]\n";
	$result = $hello->setBy('iname, engname, iauthor, inserttime, updatetime', '');
	%result = %{$result};
	print "\t\t2-setBy-result-state:[".$result{0}."] rtn:[".$result{1}."] now:[".time()."]\n";	
	if($result{0}){
		print "\t\tctrl/index: setBy succ.\n";	
	}
	my %rtn = %{$result{1}};
	print "\t\tctrl/index: insertid:[".$rtn{'insertid'}."] affectedrows:[".$rtn{'affectedrows'}."]\n";

	my $sql = "select * from ".$hello->getTbl(); 
	$hello->setId(24);
	$result = $hello->execBy($sql, 'id=?');
	%result = %{$result};
	print "\t\t2-setBy-result-state:[".$result{0}."] rtn:[".$result{1}."] now:[".time()."]\n";	
	if($result{0}){
		print "\t\tctrl/index: execBy succ.\n";	
	}
	#my %rtn = %{$result{1}};
	#print "\t\tctrl/index: insertid:[".$rtn{'insertid'}."] affectedrows:[".$rtn{'affectedrows'}."]\n";
	@rows = @{$result{1}};
	$rowCount = scalar @rows;
	for($i=0; $i<$rowCount; $i++){
		print "\t\ti:$i\n";	
		my %row = %{$rows[$i]};
		foreach(keys %row){
			print "\t\t\tk:$_ v:".$row{$_}."\n";
			#last;
		}
	}

	$hello->setId(32);
	#$result = $hello->rmBy('id=?');
	$result = $hello->rmBy();
	%result = %{$result};
	print "\t\t2-rmBy-result-state:[".$result{0}."] rtn:[".$result{1}."] now:[".time()."]\n";	
	if($result{0}){
		print "\t\tctrl/index: rmBy succ.\n";	
	}
	%rtn = %{$result{1}};
	print "\t\tctrl/index: insertid:[".$rtn{'insertid'}."] affectedrows:[".$rtn{'affectedrows'}."]\n";
	
}

1;
