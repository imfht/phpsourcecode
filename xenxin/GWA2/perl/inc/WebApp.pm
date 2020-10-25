package inc::WebApp;

#
# GWA2, General Web Application Architecture
# By Xenxin@ufqi.com, Wadelau@ufqi.com
# Main designs from -GWA2 in -PHP, -GWA2 in -Perl
# Since Sun Jan  1 22:56:46 CST 2017
# Update Wed Jan 25 11:11:49 CST 2017
# Update Nov 07, 2018, +multiple databases connections in a single session
# v0.20
#

use strict;
use warnings;
use Cwd qw(abs_path realpath);
use File::Basename qw(dirname basename);
use utf8;
no warnings 'utf8';
binmode( STDIN,  ':encoding(utf8)' );
binmode( STDOUT, ':encoding(utf8)' );
binmode( STDERR, ':encoding(utf8)' );
#use Try::Tiny;
use Scalar::Util qw(refaddr);

use parent 'inc::WebInterface';
use inc::Config qw(GConf);
use inc::Dba;
use inc::Filea;

my $_ROOT_ = "../";
if(my $tmpPath=abs_path($0)){
    $_ROOT_ = dirname($tmpPath);
}
use constant VER => 0.01;
my $dba = {};
my %hm = (); # []; is a reference
my $filea = {};
my %hmf = ();
my $isdbg = 1;
my $myId = 'id';
use constant {
	GWA2_ERR => 'gwa2_tag_error',
	GWA2_ID => 'gwa2_tag_id',
	GWA2_TBL => 'gwa2_tag_tbl',
	DBL_TBL => "\t\t",
};
my $GWA2_Rumtime_Env_List = ();

my $gconf = inc::Config->new();
my $self = {};
# the unique id of an instance object and/or its children
# see multiple databases in a single session
my $theUniqId = ''; 

#
sub new {
	my $class = shift @_;
	my $args = shift; # @_ may be omitted.
	my %args = %{$args};
	#print "\t\tclass:[$class] args:[".\%args."]\n";
	$self = {@_};
	bless $self, $class;
	if(1){
		if(!defined($args{'dbconf'})){ $args{'dbconf'} = 'MasterDB'; }
		$dba = inc::Dba->new($args{'dbconf'});
		
		if(defined($args{'unique_id'})){ $theUniqId = $args{'unique_id'}; }
		else{ $theUniqId = $self->getUniqueId(); }
		$self->set('dbconf'.$theUniqId, $args{'dbconf'});
	}
	if(1){
		if(!defined($args{'fileconf'})){ $args{'fileconf'} = 'FileSystem';  }
		$filea = inc::Filea->new($args{'fileconf'});	
	}

	bless $self, $class;
	return $self;
}

#
sub DESTROY {
	# @todo	
	#print "\t\t\tI am runing away from inc::WebApp->DESTROY...".time()."\n";
}

#
sub get($){
	#my $self = shift; # no param?
	my $k = pop @_; # @_[1]; # shift;
	return $hmf{$k};
}

# 
sub set($ $){
	# @_[0]:self module; @_[1]:args-1; @_[1]:args-2
	my $v = pop @_; # last one, in case of two (outer caller) or three (inner caller) arguments
	my $k = pop @_;
	$hmf{$k} = $v;
	return 1;
}

# 
sub getId{
	#my $self = shift;
	return get($myId);	
}

#
sub setId($){
	my $self = shift; # why ?
	my $v = pop @_; # @_[1];
	$self->set($myId, $v);
	return 1;
}

#
sub setMyId($){
	my $v = pop @_;
	$myId = $v;		
}

#
sub getTbl{
	#my $self = shift; # no param?
	return get(GWA2_TBL);	
}

#
sub setTbl($){
	my $v = pop @_; # @_[1];
	my $tblpre = $gconf->{'tblpre'};getConf('tblpre'); # use qw ?
	#print "\t\tinc::WebApp: setTbl: tblpre:[$tblpre]\n";
	set(GWA2_TBL, $v);
	return 1;
}

#
# by Xenxin@ufqi.com since Sun Jan  1 22:54:54 CST 2017
sub getBy($ $ $) { # $fields, $conditions, $withCache
	my %result = (); 
	my $self = $_[0]; # ?
	#print "\t\tinc::WebApp: getBy: argc:".(scalar @_).", argv:@_ upld:[".$gconf->{'uploaddir'}."]\n";
	my $argc = scalar @_;
	my ($withCache, $conditions, $fields) = (0, '', ''); # pop @_;
	if($argc == 3){
		$conditions = pop @_;
		$fields = pop @_;
	}
	elsif($argc == 4){
		$withCache = pop @_;
		$conditions = pop @_;
		$fields = pop @_;
	}
	else{
		print "\t\tinc::WebApp::getBy: need parameters >= 3. 201709231051.";
	}
	### withCache @todo
	# read from db
	if(!($fields=~/:/)){
		my $sql = "";
		my %hm = ();
		my $haslimit1 = 0;
		my $pagenum = 1;
		my $pagesize = 0;
		if(exists($hmf{'pagenum'})){ $pagenum=$hmf{'pagenum'}; }
		if(exists($hmf{'pagesize'})){ $pagesize=$hmf{'pagesize'}; }
		$sql = "select $fields from ".$self->getTbl()." where ";
		my $idval = $self->getId(); $idval = defined($idval) ? '' : $idval;
		if($conditions eq ""){
			if($idval ne ""){
				$sql .= $myId."=? ";
				$haslimit1 = 1;
			}
			else{
				$sql .= "1=1 ";
			}
		}
		else{
			$sql .= $conditions;		
		}
		if(defined($hmf{'groupby'})){ $sql .= " group by ".$hmf{'orderby'}; }
		if(defined($hmf{'orderby'})){ $sql .= " order by ".$hmf{'orderby'}; }
		if($haslimit1 == 1){
			$sql .= " limit 1";	
		}
		else{
			if($pagesize == 0){ $pagesize = 99999; } # default maxium records per page
			$sql .= " limit ".(($pagenum-1)*$pagesize).", ".$pagesize;	
		}
		$dba = $self->_checkDbConn($dba, $self->get('dbconf'.$self->getUniqueId()));
		my $result = $dba->select($sql, \%hmf);
		%result = %{$result};
		print "\t\t\tinc::WebApp: getBy: sql:[$sql] result:".%result."\n";
	}	
	else{ # read from object
		my %conditions = %{$conditions};
		my $type = $fields;
		%result = %{$self->readObject($type, \%conditions)};		
	}
	return \%result;
}

# 
sub setBy($ $){ # $$ # $fields, $conditions
	my %result = (); 
	#print "\t\tinc::WebApp: setBy: argc:".(scalar @_).", argv:@_\n";
	my $self = $_[0]; # ?
	my $conditions = pop @_;
	my $fields = pop @_;
	my $idval = $self->getId(); $idval = !defined($idval) ? '' : $idval;
	# write to db
	if(!($fields=~/:/)){
		my $sql = '';
		my $isupdate = 0;
		if($idval eq '' && ($conditions eq '')){
			$sql .= "insert into ".$self->getTbl()." set ";
		}
		else{
			$sql .= "update ".$self->getTbl()." set ";	
			$isupdate = 1;
		}
		my @fieldarr = split(/,/, $fields);
		my $fieldcount = scalar @fieldarr;
		my $field = '';
		for(my $i=0; $i<$fieldcount; $i++){
			$field = $self->trim($fieldarr[$i]);
			if($field eq 'updatetime' || $field eq 'inserttime' || $field eq 'createtime'){
				$sql .= "$field=NOW(), ";
				delete $hmf{$field};
			}	
			else{
				$sql .= "$field=?, ";	
			}
		}
		$sql = substr($sql, 0, length($sql)-2); #
		my $issqlready = 1;
		if($conditions eq ''){
			if($idval ne ''){
				$sql .= " where ".$myId."=? ";	
			}
			elsif($isupdate == 1){
				$issqlready = 0;
				$result{0} = 0;
				my %errhm = ("sayerror"=>"Unconditional update is forbidden. 1701232229.");
				$result{1} = %errhm; 
			}
		}
		else{
			$sql .= " where ".$conditions;	
		}
		print "\t\tinc::WebApp: setBy: sql:[$sql]\n";
		$dba = $self->_checkDbConn($dba, $self->get('dbconf'.$self->getUniqueId()));
		if($issqlready == 1){
			if($idval ne ''){ $hmf{'pagesize'} = 1; }	
			my $result = $dba->update($sql, \%hmf);
			%result = %{$result};
		}
	}	
	else{ # write to object
		my %conditions = %{$conditions};
		#foreach my $k(keys %conditions){
		#	print "\t\tinc::WebApp::setBy: k:$k v:[".$conditions{$k}."]\n";	
		#}
		my $type = $fields;
		%result = %{$self->writeObject($type, \%conditions)};	
	}	
	return \%result;
}

#
sub execBy($ $ $){ # $sql, $conditions, $withCache
	my %result = (); 
	#print "\t\tinc::WebApp: execBy: argc:".(scalar @_).", argv:@_\n";
	my $self = $_[0]; # ?
	my $argc = scalar @_;
	my ($withCache, $conditions, $sql) = (0, '', ''); # pop @_;
	if($argc == 2){
		$sql = pop @_;
	}
	elsif($argc == 3){
		$conditions = pop @_;
		$sql = pop @_;
	}
	elsif($argc == 4){
		$withCache = pop @_;
		$conditions = pop @_;
		$sql = pop @_;
	}
	# withCache? @todo 
	if(1){
		if(!defined($conditions)){ $conditions = ''; }
		my $pos = index($sql, 'select '); # case insensitive?
		if($pos == -1){
			$pos = index($sql, 'desc ');
			if($pos == -1){
				$pos = index($sql, 'show ');	
			}
		}
		if($conditions ne ''){
			if(index($sql, 'where ') > -1){
				$sql .= $conditions;	
			}	
			else{
				$sql .= " where $conditions";	
			}
		}
		print "\t\tinc::WebApp: execBy: sql:[$sql] pos:[$pos]\n";
		my $result = ();
		$dba = $self->_checkDbConn($dba, $self->get('dbconf'.$self->getUniqueId()));
		if($pos > -1){
			$result	= $dba->select($sql, \%hmf);
		}
		else{
			$result = $dba->update($sql, \%hmf);
		}
		# with cache? @todo 
		%result = %{$result};
	}
	return \%result;
}

#
sub rmBy($){
	my %result = (); 
	print "\t\tinc::WebApp: rmBy: argc:".(scalar @_).", argv:@_\n";
	my $self = $_[0]; # ?
	my $argc = scalar @_;
	my ($conditions, $sql) = ('', ''); # pop @_;
	if($argc == 2){
		$conditions = pop @_;
	}
	# rm from db
	$sql = "delete from ".$self->getTbl()." ";
	my $issqlready = 0;
	if(!defined($conditions) || $conditions eq ''){
		if($self->getId() ne ''){
			$sql .= " where $myId=?";
			$issqlready = 1;
		}
		else{
			my $err = "Unconditional deletion is strictly forbidden. sql:[$sql]. 1701251225.";	
			$result{0} = 0;
			my %errhm = ("sayerror"=>$err);
			$result{1} = \%errhm;
		}
	}
	else{
		if(index($conditions, 'where ') == -1){
			$sql .= " where ";	
		}
		$sql .= $conditions;	
		$issqlready = 1;
	}
	print "\t\t\tinc::WebApp: rmBy: sql:[$sql]\n";
	my $result = ();
	$dba = $self->_checkDbConn($dba, $self->get('dbconf'.$self->getUniqueId()));
	if($issqlready == 1){
		$result = $dba->update($sql, \%hmf);
		%result = %{$result};
	}
	return \%result;
}

# 
sub readObject($ $){
	my %result = (); 
	#print "\t\tinc::WebApp: readObject: argc:".(scalar @_).", argv:@_\n";
	my $argc = scalar @_;
	my $self = $_[0];
	my ($type, $args) = ('', undef); # pop @_;
	$args = pop @_;
	$type = pop @_;
	my %args = %{$args};
	if($type=~/file:/){
		my $target = $args{'target'}; 
		delete $args{'target'};
		%result = %{$filea->read($target, \%args)};
	}
	else{
		%result = (0=>0, 1=>"Unsupported readObject:[$type]. 201703012218.");	
	}
	return \%result;

}

#
sub writeObject($ $){
	my %result = (); 
	#print "\t\tinc::WebApp: writeObject: argc:".(scalar @_).", argv:@_\n";
	my $argc = scalar @_;
	my $self = $_[0]; # ?
	my ($type, $args) = ('', undef); # pop @_;
	$args = pop @_;
	$type = pop @_;
	my %args = %{$args};
	if($type=~/file:/){
		#foreach my $k (keys %args){
		#	print "\t\tinc::WebApp::writeObject: k:$k v:".$args{$k}."\n";	
		#}
		my $target = $args{'target'}; my $content = $args{'content'};
		delete $args{'target'}; delete $args{'content'};
		%result = %{$filea->write($target, $content, \%args)};
	}
	else{
		%result = (0=>0, 1=>"Unsupported writeObject:[$type]. 201702162109.");	
	}
	return \%result;
}

#
sub getEnv {
	#print "\t\tinc::WebApp::getEnv: argc:[".@_."]\n";
	#for(my $i=0; $i<@_; $i++){
		#print "\t\tinc::WebApp::getEnv: i:$i v:[".$_[$i]."]\n";
	#}
	my $self = shift;
	my $aref = pop @_;
	if(!defined($aref)){ 
		$aref = {}; # ();
	}
	else{ }
	my %pararef = %{$aref}; # for debug	
	return "ver:[".VER."] root:[".$_ROOT_."]";	
}

#
sub trim($){
	my $s = pop @_; # shift;
	$s =~ s/^\s+|\s+$//g;
	return $s;
}

#
sub getConf($){
	my $k = pop @_;
	#print "\t\tinc::WebApp::getConf: k:$k\n";
	#return inc::Config::get($k); # use qw ?
	return $gconf->{$k}; # ?
}

#
# get an unique id for an object
sub getUniqueId { 
    my $self = shift;
    my $uniqid = refaddr $self;
    #print "\t\tinc/WebApp: unique_id: $uniqid with self:$self\n";
    return $uniqid;
}

#
# check if current connection has same dbname with current object by unique id
sub _checkDbConn($ $){
    $self = $_[0];
    my $dba = $_[1];
    my $objDb = $_[2];
    if(defined($dba) && defined($objDb)){
        my $currentDb = $dba->getConf();
        if(defined($currentDb)){
            if($currentDb=~/$objDb/){
                # same db
                #print "\t\tinc/WebApp: _checkDbConn: dba:$dba same db: objDb:$objDb currentDb:$currentDb\n";
            }
            else{
                $dba = inc::Dba->new($objDb);	
                print "\t\tinc/WebApp: _checkDbConn: dba:$dba switch diff db: objDb:$objDb currentDb:$currentDb\n";
            }
        }
        else{
            print "\t\tinc/WebApp: _checkDbConn: dba:$dba  currentDb error!\n";
        }
    }
    else{
        print "\t\tinc/WebApp: _checkDbConn: dba error or no specified dbname.\n";
    }
    return $dba;
}

1;
