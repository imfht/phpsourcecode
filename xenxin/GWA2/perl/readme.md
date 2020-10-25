
# -GWA2 in -Perl

Implement GWA2 architecture and Design in Perl with OOP. 

Since 07:19 16 November 2016

First release Wed Jan 25 12:48:07 CST 2017

Refer to GWA2 in PHP running in command line mode. 

    @todo



# Dependencies

JSON

DBI

DBD::mysql

It is better to install these modules via system control panel, e.g.

YaST for openSuSE.


# Features
First success with Database access with GWA2 in Perl was made on 

Sun Jan  1 22:59:36 CST 2017

# Programs Written in GWA2 with Perl

##0. data
create table prefix_dummytbl(
    id int(12) not null auto_increment, 
    iname char(255) not null default ”,
primary key (id),
unique index uk1(iname));

##1. object
mod/DummyModule.pm
use parent ‘inc::WebApp’;

##2. controller
ctrl/dummy.pl
use mod::DummyModule;
    $act=listen
    $act=speak
    $act=remember
….

##3. view
view/default/dummy.html

##4. routing
/path/to/perl /path/to/project/index.pl “?mod=dummy&act=listen&fmt=json”
