<?php
namespace Smail\Mime\Nntp;
/*
 * Stores the Header of an article
 */
class headerType {
  var $number; // the Number of an article inside a group
  var $id;     // Message-ID
  var $from;   // eMail of the author
  var $name;   // Name of the author
  var $subject; // the subject
  var $newsgroups;  // the Newsgroups where the article belongs to
  var $followup;
  var $date;
  var $organization;
  var $xnoarchive;
  var $references;
  var $content_transfer_encoding;
  var $mime_version;
  var $content_type;   // array, Content-Type of the Body (Index=0) and the
                       // Attachments (Index>0)
  var $content_type_charset;  // like content_type
  var $content_type_name;     // array of the names of the attachments
  var $content_type_boundary; // The boundary of an multipart-article.
  var $answers;
  var $isAnswer;
  var $username;
  var $user_agent;
  var $isReply;
}