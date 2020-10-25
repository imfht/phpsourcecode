#!/usr/bin/perl -w

# comm/tools.function.pl

use strict;
use warnings;
use CGI;

my $argvsize = scalar @ARGV;
my %hmf = (); # runtime container
%hmf = %{$ARGV[$argvsize-1]}; # return from controller

my $i = $hmf{'i'}; 
my $out = $hmf{'out'};
my $fmt = $hmf{'fmt'};
my $r = $hmf{'r'};

# functions being used across the whole application
# see behaviours in -GWA2 in -PHP, -GWA2 in -Java
# Mon Feb 27 22:08:08 CST 2017
# refer: http://perldoc.perl.org/functions/ref.html
sub debug{ # ($ $)
	my $self = $_[0];
	my $argc = scalar @_;
	my @origarr = @_;
	my ($tgt, $tag) = ('', '');
	if($argc == 2){
		$tgt = pop @_;
	}
	else{ # 3
		$tag = pop @_;	
		$tgt = pop @_;
	}
	my $cont = ""; my $t = ref($tgt); 
	my $i = 0; my $tabs = DBL_TBL;
	if($t eq ''){ $t = ref(\$tgt); }
	if($t eq "ARRAY"){
		my @tgt = @{$tgt};
		for($i=0; $i<@{$tgt}; $i++){
			$cont .= $tabs."i:[$i] v:[".$tgt[$i]."]\n";
		}	
	}
	elsif($t eq "REF" || $t eq "HASH"){
		foreach my $k(keys %{$tgt}){
			$cont .= $tabs."k:[$k] v:[".$tgt->{$k}."]\n";	
		}
	}
	elsif($t eq "SCALAR"){
		for($i=0; $i<@origarr; $i++){
			$cont .= $tabs."i:[$i] v:[".$origarr[$i]."]\n";
			my $tt = ref($origarr[$i]);
			if($tt=~/HASH/){
				foreach my $ttk(keys %{$origarr[$i]}){
					$cont .= $tabs."\t$ttk => ".$origarr[$i]{$ttk}."\n";
				}	
			}
			elsif($tt=~/ARRAY/){
				for(my $tti=0; $tti<@{$origarr[$i]}; $tti++){
					$cont .= $tabs."\t$tti => ".$origarr[$i][$tti]."\n";
				}
			}
		}	
		$tag = 'var';
	}
	else{
		$cont .= $tabs."UNK type. 1702272109.";	
	}
	if($tag eq '' || $tag =~/0-9/){
		$tag = 'var';	
	}
	my ($packagep, $filenamep, $linep, $subrtp) = caller($i=1); # parent
	my ($package, $filename, $line, $subrt) = caller($i=0);
	print $tabs."comm/tools::debug: $tag:[".(\$tgt)."] type:[$t]\n".$cont.$tabs."line:[$line] file:[$filename]";
	if(defined($linep)){
		print " pline:[$linep] pfile:[$filenamep]";
	}
	print "\n";
}

# 
# %args = ('from'=>'', 'to'=>'', 'subject'=>'', 'content'=>'', 'attachfile'=>'', 'attachfiles'=>Array);
sub sendMail($){
    my $self = $_[0];
	my $args = pop @_;
    my %args = %{$args};

    my $rtn = 0;
    my $cont =  $args->{'content'}; 
    print "content:$cont\n";

    my %mailconfig = ('mailhost'=>'', 'hellohost'=>'', 'mailport'=>465, 
        'authuser'=>'1234@abc.com', 'authpass'=>'',
        'isssl'=>1, 'isdebug'=>0); 

    my $mailfrom = $args->{'from'};
    my @tmpHelloArr = split(/@/, $mailfrom);
    $mailconfig{'hellohost'} = $tmpHelloArr[1];

    my $mailto = $args->{'to'};
    my @toAddressList = split(/,/, $mailto);
    my $msubject = $args->{'subject'}; if($cont eq ''){ $cont = $msubject; }
    my $mailx = MIME::Lite->new(
            From    => $mailfrom,
            To      => @toAddressList,
            #Subject => Encode::encode("utf8", $msubject),
            Subject => $msubject,
            Type    => 'text/html;charset=UTF-8',
            charset => 'UTF-8',
            Data     => Encode::encode("utf8", $cont),
            Encoding => 'base64'
            );

    if(exists($args->{'attachfiles'})){
        my @filepath = @{$args->{'attachfiles'}};
        foreach my $tmpi (keys @filepath){
            my $tmpfilepath = $filepath[$tmpi];
            $mailx->attach('Type'=>'auto', 'Path'=>$tmpfilepath);
        }
    }
    elsif(exists($args->{'attachfile'})){
        if(1){
            my $tmpfilepath = $args->{'attachfile'};
            $mailx->attach('Type'=>'auto', 'Path'=>$tmpfilepath);
        }
    }

    #$mailx->send('smtp', $mailconfig{'mailhost'}); # no auth
    #$mailx->send('smtp', $mailconfig{'mailhost'}, 'Debug'=>$mailconfig{'isdebug'}, 'SSL'=>$mailconfig{'isssl'}, 
    #   'Port'=>465, 'AuthUser'=>$mailconfig{'authuser'}, 'AuthPass'=>$mailconfig{'authpass'});

    my $smtp = Net::SMTP->new($mailconfig{'mailhost'}, 'Hello'=>$mailconfig{'hellohost'}, 
        'SSL'=>$mailconfig{'isssl'}, 'Debug'=>$mailconfig{'isdebug'});
    if($smtp){
        if($smtp->auth($mailconfig{'authuser'}, $mailconfig{'authpass'})){
            print "smtp connected and authorized succ\n";
            $smtp->mail($mailfrom);
            $smtp->to(@toAddressList);
            $smtp->data();
            $smtp->datasend(Encode::encode("utf8", $mailx->as_string));
            $smtp->datasend("\r\n");
            $smtp->datasend();
            $smtp->quit();
            print "mail sent succ.\n";
            $rtn = 1;
        }
        else{
            print "smtp connected but authorized failed.\n";
        }
    }
    else{
        print "smtp server connection failed.\n";
    }

    return $rtn;

}

#
# just trim it
sub trim{
    my $str = shift;
    #$str = lc($str);
    $str =~ s/^\s+|\s+$//g;
    return $str;
}

$ARGV[$argvsize] = \%hmf; # return to parent

1;
