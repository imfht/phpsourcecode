package inc::MySQL;

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

use DBI;
use DBD::mysql;
use Try::Tiny;

#use parent 'inc::DBA';
use inc::Config;

my $_ROOT_ = "../";
if(my $tmpPath=abs_path($0)){
    $_ROOT_ = dirname($tmpPath);
}
my ($m_host, $m_port, $m_user, $m_password, $m_name, $_link, $m_sock)
	= ('', '', '', '', '', '', '');
my ($dbh, $sth, $sql, $rs, $row, %datasource) = (undef, undef, 0, 0, 0, ());
my $isdbg = 1;

#
sub new {
	my $class = shift;
	my $self = {}; # {@_};
	my $conf = shift;
	my %conf = %{$conf};
	$self->{m_host} = $m_host = $conf{'mDbHost'};
	#print "\t\t\tinc::MySQL: m_host:$m_host\n";
	$self->{m_port} = $m_port = $conf{'mDbPort'};
	$self->{m_user} = $m_user = $conf{'mDbUser'};
	$self->{m_password} = $m_password = $conf{'mDbPassword'};
	$self->{m_name} = $m_name = $conf{'mDbDatabase'};
	$self->{m_sock} = $m_sock = $conf{'mDbSock'};

	bless $self, $class;
	
	my $conftag = 'cftg';
	foreach my $ck (keys %conf){
		$conftag .= ':'.$conf{$ck};	
	}
	$dbh = $datasource{$conftag};
	if(!defined($dbh)){
		$dbh = $self->_initConnection();	
		$datasource{$conftag} = $dbh;
	}
	print "inc::MySQL::new: args:[@_] dbh:[$dbh] conf:[".$conf."] conftag:[$conftag]\n";
	
	return $self;
}

#
# refined by Xenxin@ufqi.com, Mon Jan 23 22:35:23 CST 2017
sub query($ $ $) {
	my %rtn = (); # [] as a ref
	my $self = shift;
	my @idxarr = @{pop @_};
	my %hmvars = %{pop @_};
	my $sql = pop @_;
	if(!defined($dbh)){
		$dbh = $self->_initConnection();	
	}
	$sql = $self->_enSafe($sql, \%hmvars, \@idxarr);
	$sth = $dbh->prepare($sql);
	my $arrsize = scalar @idxarr;
	for(my $i=0; $i<$arrsize; $i++){
		#print "\t\tinc::MySQL: query: $i: ".$idxarr[$i]."\n";
		$sth->bind_param($i+1, $hmvars{$idxarr[$i]});
	}
	my $result = 0 ;
	try{
		$result = $sth->execute();
	}
	catch{
		print "sql:[$sql] read failed in inc/MySQL.... continue?\n";
	};
	my @rows = (); # [] as a ref to array
	my $rtnflag = 0;
	if($result){
		$rows[0] = $sth->rows; # affected rows
		$rows[1] = $dbh->last_insert_id(undef, undef, undef, undef); 
		# refer to http://search.cpan.org/~timb/DBI-1.636/DBI.pm#execute 
		#print "\t\t\tinc::MySql: update: lastid:[".$rows[1]."] affectedrows:[".$rows[0]."].\n";
		$rtnflag = 1;
	}
	else{
		$rows[0] = "Query failed for sql:[$sql]. 1701250954.";	
		$rows[1] = 0;
	}
	$sth->finish();
	return ('0'=>$rtnflag, '1'=>\@rows);
}

#
# by Xenxin@ufqi.com since Sun Jan  1 22:54:18 CST 2017
sub readSingle($ $ $) {
	my %rtnhm = ();
	my $self = shift;
	my @idxarr = @{pop @_};
	my %hmvars = %{pop @_};
	my $sql = pop @_;
	if(!defined($dbh)){
		$dbh = $self->_initConnection();	
	}
	$sql = $self->_enSafe($sql, \%hmvars, \@idxarr);
	$sth = $dbh->prepare($sql);
	my $arrsize = scalar @idxarr;
	for(my $i=0; $i<$arrsize; $i++){
		$sth->bind_param($i+1, $hmvars{$idxarr[$i]});
	}
	my $result = 0 ;
	try{
		$result = $sth->execute();
	}
	catch{
		print "sql:[$sql] read failed in inc/MySQL.... continue?\n";
	};
	my @rows = []; my $rtnflag = 0;
	if($result){
		if(my $ref = $sth->fetchrow_hashref()){
			$rows[0] = $ref;
			$rtnflag = 1;
		}
		else{
			my %hmtmp = (fail=>'No data for sql:['.$sql.']. 1703192107.');
			$rows[0] = \%hmtmp;
		}
	}
	else{
		my %hmtmp = (fail=>"Query failed for sql:[$sql]. 1701251034.");
		$rows[0] = \%hmtmp;
	}
	$sth->finish();
	return ('0'=>$rtnflag, '1'=>\@rows);
}

# by Xenxin@ufqi.com since  Mon Jan 23 21:07:09 CST 2017
sub readBatch($ $ $) {
	my %rtnhm = ();
	my $self = shift;
	my @idxarr = @{pop @_};
	my %hmvars = %{pop @_};
	my $sql = pop @_;
	if(!defined($dbh)){
		$dbh = $self->_initConnection();	
	}
	$sql = $self->_enSafe($sql, \%hmvars, \@idxarr);
	print "\t\tinc::MySql: readBatch: sql:[$sql] vars:[".%hmvars."]\n";
	$sth = $dbh->prepare($sql);
	my $arrsize = scalar @idxarr;
	for(my $i=0; $i<$arrsize; $i++){
		$sth->bind_param($i+1, $hmvars{$idxarr[$i]});
		#print "binding: i:$i k:[".$idxarr[$i]."] v:[".$hmvars{$idxarr[$i]}."]\n";
	}
	my $result = 0 ;
	try{
		$result = $sth->execute();
	}
	catch{
		print "sql:[$sql] read failed in inc/MySQL.... continue?\n";
	};
	my @rows = (); my $i = 0; my $rtnflag = 0;
	if($result){
		while(my $ref = $sth->fetchrow_hashref()){
			$rows[$i++] = $ref;		
		}
		if($i == 0){
			my %hmtmp = ('fail','No data for sql:['.$sql.']. 1703192105.');
			$rows[0] = \%hmtmp;
		}
		else{
			$rtnflag = 1;
		}
	}
	else{
		my %hmtmp = ("fail", "Query failed for sql:[$sql]. 1701251041.");
		$rows[0] = \%hmtmp;
	}
	$sth->finish();
	return ('0'=>$rtnflag, '1'=>\@rows);
}

# 
# Xenxin@ufqi.com, Sun Jan  1 22:52:34 CST 2017
sub _initConnection {
	$dbh = DBI->connect_cached("DBI:mysql:database=$m_name;host=$m_host;port=$m_port", 
		$m_user, 
		$m_password, 
		{'RaiseError'=>1, 'mysql_enable_utf8'=>1, 'AutoCommit'=>1}) 
		or warn "cannot connect to mysql server. errno:["
			.$dbh->err."] errmsg:[".$dbh->errstr."]. 17011201556.";		
	#print "\t\t\tinc::MySQL: initConnection....".time()."\n";
	return $dbh;
}

#
sub _enSafe($ $ $){
	my @idxarr = @{pop @_};
	my %hmvars = %{pop @_};
	my $sql = pop @_;

		# @todo
		# $sth->bind_param, instead of, refer to GWA2 in Java

	return $sql;
}

1;
