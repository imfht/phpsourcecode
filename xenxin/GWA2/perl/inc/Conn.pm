package inc::Conn;

use strict;
use warnings;
use Cwd qw(abs_path realpath);
use File::Basename qw(dirname basename);
use utf8;
no warnings 'utf8';
binmode( STDIN,  ':encoding(utf8)' );
binmode( STDOUT, ':encoding(utf8)' );
binmode( STDERR, ':encoding(utf8)' );

#use parent 'inc::DBA';
use inc::Config;

my $_ROOT_ = "../";
if(my $tmpPath=abs_path($0)){
    $_ROOT_ = dirname($tmpPath);
}
my $isdbg = 1;

#
{
	package inc::Conn::MasterDB;

	sub new {
		my $class = shift;
		my $self = {}; #{@_};
		my $gconf = inc::Config->new(); 

		$self->{mDbHost} = $gconf->{'dbhost'};
		#print "\t\t\tinc::Conn: dbhost:".$self->{mDbHost}." 2:".$gconf->{'dbhost'}."\n";
		$self->{mDbPort} = $gconf->{'dbport'};
		$self->{mDbUser} = $gconf->{'dbuser'};
		$self->{mDbPassword} = $gconf->{'dbpassword'};
		$self->{mDbDatabase} = $gconf->{'dbname'};
		$self->{mDbSock} = $gconf->{'dbsock'};

		bless $self, $class;
		return $self;
	}

}

#
{
	package inc::Conn::SlaveDB;

	sub new {
		# @todo		
		my $class = shift;
		my $self = {}; #{@_};
		my $gconf = inc::Config->new(); 

		$self->{mDbHost} = $gconf->{'dbhost_stat'};
		#print "\t\t\tinc::Conn: dbhost:".$self->{mDbHost}." 2:".$gconf->{'dbhost'}."\n";
		$self->{mDbPort} = $gconf->{'dbport_stat'};
		$self->{mDbUser} = $gconf->{'dbuser_stat'};
		$self->{mDbPassword} = $gconf->{'dbpassword_stat'};
		$self->{mDbDatabase} = $gconf->{'dbname_stat'};
		$self->{mDbSock} = $gconf->{'dbsock_stat'};

		bless $self, $class;
		return $self;

	}
}

#
{
	package inc::Conn::FileSystem;

	sub new {
		# @todo		
		my $class = shift;
		my $self = {}; #{@_};
		my $gconf = inc::Config->new(); 

		$self->{uplddir} = defined($gconf->{'uploaddir'}) ? $gconf->{'uploaddir'} : './';
		#print "\t\t\tinc::Conn::FileSystem: upld:".$self->{uplddir}."\n";
		$self->{reuse} = $gconf->{'enable_filehandle_share'};

		bless $self, $class;
		return $self;

	}
}


1;
