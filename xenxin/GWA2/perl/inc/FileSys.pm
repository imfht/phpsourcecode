#
# Xenxin@ufqi.com, Thu Feb 16 21:48:49 CST 2017
#
package inc::FileSys;

use strict;
use warnings;
use Cwd qw(abs_path realpath);
use File::Basename qw(dirname basename);
use utf8;
no warnings 'utf8';
binmode( STDIN,  ':encoding(utf8)' );
binmode( STDOUT, ':encoding(utf8)' );
binmode( STDERR, ':encoding(utf8)' );
use autodie;

use IO::Handle; # @todo
use File::Path qw(make_path remove_tree);

#use parent 'inc::DBA';
use inc::Config;

my $_ROOT_ = "../";
if(my $tmpPath=abs_path($0)){
    $_ROOT_ = dirname($tmpPath);
}
my $isdbg = 1;
my $file = '';
my $fh = undef;
my $handlelist = ();
my $uplddir = '';
my $reuse = 0;
my $isclosed = 0;

#
sub new {
	my $class = shift;
	my $self = $_[0];
	my $conf = shift;
	my %conf = %{$conf};
	#$self->{m_host} = $m_host = $conf{'mDbHost'};
	#print "\t\t\tinc::FileSys: uplddir:[".$conf{'uplddir'}."]\n";
	$uplddir = $conf{'uplddir'};

	bless $self, $class;
	return $self;
}

#
sub DESTROY {
	# @todo	
	my $self = $_[0];
	$self->closef();

}

# more methods
#
sub readf($ $){
	my %result = ();
	my $argc = scalar @_;
	my $self = $_[0]; # ?
	my ($file, $args) = ('', '', undef);
	$args = pop @_;
	$file = pop @_;
	my %args = %{$args};

	# @todo, %args, %handlelist

	$fh = $self->openf($file, \%args);
	my $cont = '';
	if(defined($fh)){
		while(<$fh>){
			$cont .= $_;	
		}
	}
	# @todo, reuse
	if(1){
		close($fh);	
		$isclosed = 1;
	}	

	%result = ('0'=>1, '1'=>{'content'=>$cont});	

	return \%result;
}

#
sub writef($ $ $){
	my %result = ();
	my $argc = scalar @_;
	my $self = $_[0]; # ?
	my ($content, $args) = ('', undef);
	$args = pop @_;
	$content = pop @_;
	$file = pop @_;
	my %args = %{$args};
	if(!defined($args{'accessmode'})){
		$args{'accessmode'} = 'w';	
	}

	# @todo, %args, %handlelist

	$fh = $self->openf($file, \%args);
	#print "\t\tinc::FileSys::writef: file:[$file] content:[$content] fh:[$fh]\n";
	print $fh $content;

	# @todo reuse

	if(1){
		if(defined($fh)){
			close($fh);	
			$isclosed = 1;
		}
	}	
	%result = ('0'=>1, '1'=>{'content'=>'success'});
	
	return \%result;
}

#
# avoid built-in func open(fd, expr)
sub openf($ $){
	my $rtn = 1;
	my $argc = scalar @_;
	my $self = $_[0]; # ?
	my ($args) = (undef);
	$args = pop @_;
	$file = pop @_;
	my %args = %{$args};

	# @todo, %args

	if(-e $file){
			
	}
	else{
		my $dir = dirname($file);
		make_path($dir, {mode=>0777, error=>\my $direrr});
		if(@$direrr){
			print "\t\tinc::FileSys::openf: direrr! @$direrr.\n";	
		}
		else{
			print "\t\tinc::FileSys::openf: dir is okay.\n";
		}
	}
	my $am = 'r';
	if(defined($args{'accessmode'})){ $am = $args{'accessmode'}; }
	my $amm = '<';
	if($am eq "w"){
		$amm = '>';
	}
	elsif($amm eq 'w+'){
		$amm = '>>';
	}
	if(-e $file){
		open($fh, $amm, $file) or warn "\t\tinc::FileSys::openf: cannot open file:[$file] 1702281906.";
	}
	else{
		print "inc::FileSys::openf: file:$file not exist... contd...\n";
	}
	return $fh;
}

#
sub closef(){
	if(!$isclosed){
		if(defined($fh)){
			close($fh) or warn "inc::FileSys:: closef: filehd:$fh failed. 1808290716.";
		}
	}
	#print "\t\tinc::FileSys::closef: $fh is closing.\n";
}

1;
