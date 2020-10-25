package inc::Dba;

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
use inc::MySQL;
use inc::Conn;

my $_ROOT_ = "../";
if(my $tmpPath=abs_path($0)){
    $_ROOT_ = dirname($tmpPath);
}
my $conf = ();
my $dbconn = {};
my %Sql_Operator_List = (' '=>1,'^'=>1,'~'=>1,':'=>1,'!'=>1,'/'=>1,
	'*'=>1,'&'=>1,'%'=>1,'+'=>1,'='=>1,'|'=>1,
	'>'=>1,'<'=>1,'-'=>1,'('=>1,')'=>1,','=>1);

my $self = {};
	
#
sub new {
	my $class = shift @_;
	$self = {}; # {@_}

	$conf = shift;
	if($conf ne ""){ $conf = "inc::Conn::$conf"; }
	else{ $conf = 'MasterDB'; }
	#print "\t\t\tinc::Dba: conf:[$conf] in inc::Dba\n";
	my $confo = $conf->new();
	if(1){ # assume default is mysql
		$dbconn = inc::MySQL->new($confo); # swith to other drivers @todo
	}

	bless $self, $class;
	return $self;
}

#
sub DESTROY {

	# @todo	

}

#
# Xenxin@ufqi.com, Sun Jan  1 22:51:55 CST 2017
sub select($ $) {
	my %rtnhm = ();
	my $self = shift;
	my %hmvars = %{pop @_};
	my $sql = pop @_;		
	my @idxarr = $self->_sortObject($sql, \%hmvars);
	my %result =  (); 
	my $haslimit1 = 0;
	#print "\t\tinc::Dba: select: sql:[$sql]\n";
	if((defined($hmvars{'pagesize'}) && $hmvars{'pagesize'} == 1)
		|| index($sql, 'limit 1') > -1){
		%result = $dbconn->readSingle($sql, \%hmvars, \@idxarr);	
		$haslimit1 = 1;
	}
	else{
		%result = $dbconn->readBatch($sql, \%hmvars, \@idxarr);
	}
	if($result{0}){
		$rtnhm{0} = 1;
		$rtnhm{1} = $result{1};
	}
	else{
		$rtnhm{0} = 0;
		$rtnhm{1} = $result{1};
	}
	#print "\t\t\tinc::Dba: ret:".%rtnhm."\n";
	return \%rtnhm;
}

#
sub update($ $) {
	my %rtnhm = ();
	my $self = shift;
	my %hmvars = %{pop @_};
	my $sql = pop @_;		
	#print "\t\tinc::Dba: update sql:[$sql]\n";
	my @idxarr = $self->_sortObject($sql, \%hmvars);
	my %result =  (); 
	%result = $dbconn->query($sql, \%hmvars, \@idxarr);
	my @rows = @{$result{1}};
	my %tmp = (); 
	if($result{0}){
		$rtnhm{0} = 1;
		%tmp = ("affectedrows"=>$rows[0], "insertid"=>$rows[1]);
	}
	else{
		$rtnhm{0} = 0;
		%tmp = ('sayerror'=>$rows[0]);
	}
	$rtnhm{1} = \%tmp;
	return \%rtnhm;
}

# sort object
# @todo, need to be updated with its Java or PHP version.
sub _sortObject($ $){
	my @rtn = (); # [] as a ref, same as {} to hash as a ref
	my $self = shift;
	my %hmvars = %{pop @_};
	my $sql = pop @_;
	my @tmparr = ();
	my $wherepos = index($sql, " where ");
	my $selectpos = index($sql, "select ");
	my %sqloplist = %Sql_Operator_List;
	my ($k, $ki, $v, $preK, $aftK, $keyLen, $keyPos) = ('', 0, '', '', '', 0, 0);
	if(1 || ref %hmvars eq ref {}){
	foreach(keys %hmvars){
		my $k = $_;
		my $v = $hmvars{$k};
		if($k eq ''){
			next;	
		}
		#print "\t\t\tinc::Dba: sortObject: k:$k v:$v\n";
		$keyLen = length($k);
		$keyPos = index($sql, $k);
		if($keyPos > -1){
			while($keyPos > -1){
				$preK = substr($sql, $keyPos-1, 1);
				$aftK = substr($sql, $keyPos+$keyLen, 1);
				if(exists($sqloplist{$preK}) && exists($sqloplist{$aftK})){
					if($selectpos > -1){
						if($keyPos > $wherepos){
							$tmparr[$keyPos] = $k;	
						}
						else{
							# select field	
						}
					}
					else{
						$tmparr[$keyPos] = $k;	
					}
				} 
				else{
					# illegal key preset	
				}
				$keyPos = index($sql, $k, $keyPos+$keyLen);
			}	
		}
		else{
			# no such key in sql	
		}
	}
	}
	else{
		# not a valid hmvars	
		print "\t\tinc::Dba: sortObject: type of %hmvars:[".(ref %hmvars)."] sql:[$sql]\n";
	}
	my $sqlLen = length($sql);
	my $tmpi = 0;
	my %kSerial = ();
	for(my $i=0; $i<$sqlLen; $i++){
		if(exists($tmparr[$i])){
			$k = $tmparr[$i];
			$ki = exists($kSerial{$k}) ? $kSerial{$k} : '';
			$rtn[$tmpi] = $tmparr[$i].($ki eq ''?'':'.'.($ki+1));
			$tmpi++;
			$kSerial{$k}++;
		}	
		else{
			# no such index num	
		}
	}
	#print "\t\tinc::Dba: sortObject: rtn:[@rtn], sql:[$sql]\n";
	my $arrsize = scalar @rtn;
	#for(my $i=0; $i<$arrsize; $i++){
	#	print "\t\t\t$i: ".$rtn[$i]."\n";	
	#}
	return @rtn;
}

#
# return current dbconf/dbname
sub getConf(){
	print "\t\tinc::Dba: conf:$conf\n"
	return $conf;
}

1;
