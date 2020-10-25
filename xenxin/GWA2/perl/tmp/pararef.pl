#!/usr/bin/perl -w

# refer to http://www.tek-tips.com/faqs.cfm?fid=427 
# tests on pass by value and/or pass by reference

use strict;

sub something {
	my $ref_scalar_a = shift;
	my $ref_array_a  = shift;
	my $ref_hash_a   = shift;

	${$ref_scalar_a}  = "z";   ### Must dereference the reference
	$ref_array_a->[0] = "zzz"; ### Changes the value of the 1st element("a")
								###   to "zzz".
	$ref_hash_a->{'a'} = "xyz"; ### Changes the value of the 1st element
								###   keyed by "a" to "xyz".
} ### end sub something

########## Main ###########
my $a           = "a";
my @array_a = ("a", "b", "c");
my %hash_a  = ("a" => "aaa",
		"b" => "bbb",
		"c" => "ccc");
print "\n";
print "before: scalar \$a = <$a>\n";
print "before: array \@array_a = " . join(', ', @array_a) . "\n";
foreach my $key (keys %hash_a) {
	print "before: hash key=<$key>, value=<$hash_a{$key}>\n";
}
### Call something subroutine ###
something(\$a, \@array_a, \%hash_a);

print "\n";
print "after : scalar \$a = <$a>\n";
print "after : array \@array_a = " . join(', ', @array_a) . "\n";
foreach my $key (keys %hash_a) {
	print "after : hash key=<$key>, value=<$hash_a{$key}>\n";
}
