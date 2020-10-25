#!/usr/bin/perl -w

# comm/header.inc.pl

use strict;
use warnings;
use CGI; # @todo

my $i = 0; 
my $argvsize = scalar @ARGV;
my %hmf = (); # runtime container
my $mod = $hmf{'mod'}; # hello
my $act = $hmf{'act'}; 
my $out = $hmf{'out'};
my $r = $hmf{'r'}; # CGI
%hmf = %{$ARGV[$argvsize-1]};
$i = $hmf{'i'};

# main body
$out = $hmf{'out'};

print "\t".__FILE__.": finializing..... @".time()."\n";
print "\t".__FILE__.": <!-- output bgn -->\n";

print $out;

print "\t".__FILE__.": <!-- output end -->\n";

1;
