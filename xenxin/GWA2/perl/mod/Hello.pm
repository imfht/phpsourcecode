#!/usr/bin/perl -w

use strict;
use warnings;

package mod::Hello;

use parent 'inc::WebApp';
#extends 'mod::Hello'; # with Moose
use mod::Base62x;
#use mod::Base62x qw(base62x_encode base62x_decode);

our @ISA = qw(inc::WebApp); # for what?

my $hello_var = 'glad to see you @'.time().' from mod::Hello.';
my $base62x = undef;

my $self = {};

# override new of WebApp
sub new {
	my $class = shift;
	my $args = shift;
	#my $self = {@_};
	$self = {
		_firstname => shift ,
		_lastname => shift,
	};
	bless $self, $class;
	if(!defined($self->{_firstname})){
		$self->{_firstname} = 'Unnamed';	
	}
	print "\t\tmod/Hello.pm: init with firstname:[".$self->{_firstname}."] at time:[".time()."].\n\n";

	my %args = ("args"=>$args, "dbconf"=>"MasterDB");
	
	if(!defined($args{'dbconf'})){ $args{'dbconf'} = 'MasterDB'; }
	else{
		# other dbconf
	}
	
	# optional for multiple db,file,cache, etc. 
    $args{'unique_id'} = $self->getUniqueId();
	
	inc::WebApp->new(\%args);
	$base62x = Base62x->new();
	$self->setTbl('temptbl');

	return $self;
}

#
sub sayHi {
	my $self = $_[0];
	my $to = pop @_;
	print "\t\ti am in mod::Hello->say: _var:["
		.($self->{_firstname})
		."] to:[$to] _var:[$hello_var] parent-ver:["
		.$self->getEnv."].\n"; # getVer is inheriting from parent inc::WebApp

}

1;
