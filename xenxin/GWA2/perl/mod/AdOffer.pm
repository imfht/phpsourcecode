#!/usr/bin/perl -w

use strict;
use warnings;

package mod::AdOffer;

use parent 'inc::WebApp';
#extends 'mod::WebApp'; # with Moose

our @ISA = qw(inc::WebApp); # for what?

# @override new of WebApp
sub new {
	my $class = shift;
	my $self = {};
	my $args = pop @_;
	bless $self, $class;
	
	my %args = %{$args};
	inc::WebApp->new(\%args);
	
	$self->setTbl('un_offers');

	return $self;
}

#
sub sayHi {
	my $self = $_[0];
	my $to = pop @_;
	print "\t\ti am in mod::AdOffer->sayHi: parent-ver:["
		.$self->getEnv."].\n"; # getVer is inheriting from parent inc::WebApp

}

1;
