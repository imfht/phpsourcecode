#
# Xenxin@ufqi.com, Thu Feb 16 21:38:03 CST 2017
#
package inc::Filea;

use strict;
use warnings;
use Cwd qw(abs_path realpath);
use File::Basename qw(dirname basename);
use utf8;
no warnings 'utf8';
binmode( STDIN,  ':encoding(utf8)' );
binmode( STDOUT, ':encoding(utf8)' );
binmode( STDERR, ':encoding(utf8)' );

use inc::Config;
use inc::Conn;
use inc::FileSys;

my $_ROOT_ = "../";
if(my $tmpPath=abs_path($0)){
    $_ROOT_ = dirname($tmpPath);
}
my $conf = ();
my $filehdl = {};

#
sub new {
	my $class = shift @_;
	my $self = {}; # {@_}

	$conf = shift;
	if($conf ne ""){ $conf = "inc::Conn::$conf"; }
	else{ $conf = 'FileSystem'; }
	#print "\t\t\tinc::Filea: conf:[$conf] in inc::Filea\n";
	my $confo = $conf->new();
	if(1){ # assume default is linuxfs
		$filehdl = inc::FileSys->new($confo); # swith to other drivers @todo
	}

	bless $self, $class;
	return $self;
}

#
sub DESTROY {
	# @todo	
	my $self = $_[0]; # shift
	$self->close();
}

#
sub read($ $){ # target, %args
	my %result = ();
	my $argc = scalar @_;
	my $self = $_[0]; 
	my ($target, $args) = ('', undef);
	$args = pop @_;
	$target = pop @_;
	my %args = %{$args};
	%result = %{$filehdl->readf($target, \%args)};

	return \%result;
}

#
sub write($ $ $){ # target, content, %args
	my %result = ();
	my $argc = scalar @_;
	my $self = $_[0]; # ?
	my ($target, $content, $args) = ('', '', undef);
	$args = pop @_;
	$content = pop @_;
	$target = pop @_;
	my %args = %{$args};
	#print "\t\tinc::Filea::write: target:[$target] content:[$content]\n";
	%result = %{$filehdl->writef($target, $content, \%args)};

	return \%result;
}

# 
sub close(){
	my $self = $_[0]; # shift
	#print "\t\tinc::Filea: close: self:[$self]\n";
	# @todo
	#$filehdl->closef(); # closed by default DESTROY
}

1;
