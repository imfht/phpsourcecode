<?php
class zip
{
 public $total_files = 0;
 public $total_folders = 0; 
 public $out_text; 

 function Extract ( $zn, $to, $index = Array(-1) )
 {
   $ok = 0; $zip = @fopen($zn,'rb');
   if(!$zip) return(-1);
   $cdir = $this->ReadCentralDir($zip,$zn);
   $pos_entry = $cdir['offset'];

   if(!is_array($index)){ $index = array($index);  }
   for($i=0; $index[$i];$i++){
   		if(intval($index[$i])!=$index[$i]||$index[$i]>$cdir['entries'])
		return(-1);
   }
   for ($i=0; $i<$cdir['entries']; $i++)
   {
     @fseek($zip, $pos_entry);
     $header = $this->ReadCentralFileHeaders($zip);
     $header['index'] = $i; $pos_entry = ftell($zip);
     @rewind($zip); fseek($zip, $header['offset']);
     if(in_array("-1",$index)||in_array($i,$index))
     	$stat[$header['filename']]=$this->ExtractFile($header, $to, $zip);
   }
   fclose($zip);
   return $stat;
 }

  function ReadFileHeader($zip)
  {
    $binary_data = fread($zip, 30);
    $data = unpack('vchk/vid/vversion/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len', $binary_data);

    $header['filename'] = fread($zip, $data['filename_len']);
    if ($data['extra_len'] != 0) {
      $header['extra'] = fread($zip, $data['extra_len']);
    } else { $header['extra'] = ''; }

    $header['compression'] = $data['compression'];$header['size'] = $data['size'];
    $header['compressed_size'] = $data['compressed_size'];
    $header['crc'] = $data['crc']; $header['flag'] = $data['flag'];
    $header['mdate'] = $data['mdate'];$header['mtime'] = $data['mtime'];

    if ($header['mdate'] && $header['mtime']){
     $hour=($header['mtime']&0xF800)>>11;$minute=($header['mtime']&0x07E0)>>5;
     $seconde=($header['mtime']&0x001F)*2;$year=(($header['mdate']&0xFE00)>>9)+1980;
     $month=($header['mdate']&0x01E0)>>5;$day=$header['mdate']&0x001F;
     $header['mtime'] = mktime($hour, $minute, $seconde, $month, $day, $year);
    }else{$header['mtime'] = time();}

    $header['stored_filename'] = $header['filename'];
    $header['status'] = "ok";
    return $header;
  }

 function ReadCentralFileHeaders($zip){
    $binary_data = fread($zip, 46);
    $header = unpack('vchkid/vid/vversion/vversion_extracted/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len/vcomment_len/vdisk/vinternal/Vexternal/Voffset', $binary_data);

    if ($header['filename_len'] != 0)
      $header['filename'] = fread($zip,$header['filename_len']);
    else $header['filename'] = '';

    if ($header['extra_len'] != 0)
      $header['extra'] = fread($zip, $header['extra_len']);
    else $header['extra'] = '';

    if ($header['comment_len'] != 0)
      $header['comment'] = fread($zip, $header['comment_len']);
    else $header['comment'] = '';

    if ($header['mdate'] && $header['mtime'])
    {
      $hour = ($header['mtime'] & 0xF800) >> 11;
      $minute = ($header['mtime'] & 0x07E0) >> 5;
      $seconde = ($header['mtime'] & 0x001F)*2;
      $year = (($header['mdate'] & 0xFE00) >> 9) + 1980;
      $month = ($header['mdate'] & 0x01E0) >> 5;
      $day = $header['mdate'] & 0x001F;
      $header['mtime'] = mktime($hour, $minute, $seconde, $month, $day, $year);
    } else {
      $header['mtime'] = time();
    }
    $header['stored_filename'] = $header['filename'];
    $header['status'] = 'ok';
    if (substr($header['filename'], -1) == '/')
      $header['external'] = 0x41FF0010;
    return $header;
 }

 function ReadCentralDir($zip,$zip_name){
	$size = filesize($zip_name);

	if ($size < 277) $maximum_size = $size;
	else $maximum_size=277;
	
	@fseek($zip, $size-$maximum_size);
	$pos = ftell($zip); $bytes = 0x00000000;
	
	while ($pos < $size){
		$byte = @fread($zip, 1); $bytes=($bytes << 8) | ord($byte);
		if ($bytes == 0x504b0506 or $bytes == 0x2e706870504b0506){ $pos++;break;} $pos++;
	}
	
	$fdata=fread($zip,18);
	
	$data=@unpack('vdisk/vdisk_start/vdisk_entries/ventries/Vsize/Voffset/vcomment_size',$fdata);
	
	if ($data['comment_size'] != 0) $centd['comment'] = fread($zip, $data['comment_size']);
	else $centd['comment'] = ''; $centd['entries'] = $data['entries'];
	$centd['disk_entries'] = $data['disk_entries'];
	$centd['offset'] = $data['offset'];$centd['disk_start'] = $data['disk_start'];
	$centd['size'] = $data['size'];  $centd['disk'] = $data['disk'];
	return $centd;
  }

 function ExtractFile($header,$to,$zip){
	$header = $this->readfileheader($zip);
	
	if(substr($to,-1)!="/") $to.="/";
	if($to=='./') $to = '';	
	$pth = explode("/",$to.$header['filename']);
	$mydir = '';
	for($i=0;$i<count($pth)-1;$i++){
		if(!$pth[$i]) continue;
		$mydir .= $pth[$i]."/";
		if((!is_dir($mydir) && @mkdir($mydir,0777)) || (($mydir==$to.$header['filename'] || ($mydir==$to && $this->total_folders==0)) && is_dir($mydir)) )
		{
			@chmod($mydir,0777);
			$this->total_folders ++;
			$this->out_text= "Dir:$mydir<br>";
		}
	}
	
	if(strrchr($header['filename'],'/')=='/') return;	

	if (!($header['external']==0x41FF0010)&&!($header['external']==16)){
		if ($header['compression']==0){
			$fp = @fopen($to.$header['filename'], 'wb');
			if(!$fp) return(-1);
			$size = $header['compressed_size'];
		
			while ($size != 0){
				$read_size = ($size < 2048 ? $size : 2048);
				$buffer = fread($zip, $read_size);
				$binary_data = pack('a'.$read_size, $buffer);
				@fwrite($fp, $binary_data, $read_size);
				$size -= $read_size;
			}
			fclose($fp);
			touch($to.$header['filename'], $header['mtime']);
		}else{
			$fp = @fopen($to.$header['filename'].'.gz','wb');
			if(!$fp) return(-1);
			$binary_data = pack('va1a1Va1a1', 0x8b1f, Chr($header['compression']),
			Chr(0x00), time(), Chr(0x00), Chr(3));
			
			fwrite($fp, $binary_data, 10);
			$size = $header['compressed_size'];
		
			while ($size != 0){
				$read_size = ($size < 1024 ? $size : 1024);
				$buffer = fread($zip, $read_size);
				$binary_data = pack('a'.$read_size, $buffer);
				@fwrite($fp, $binary_data, $read_size);
				$size -= $read_size;
			}
		
			$binary_data = pack('VV', $header['crc'], $header['size']);
			fwrite($fp, $binary_data,8); fclose($fp);
	
			$gzp = @gzopen($to.$header['filename'].'.gz','rb') or die("Cette archive est compress閑");
			if(!$gzp) return(-2);
			$fp = @fopen($to.$header['filename'],'wb');
			if(!$fp) return(-1);
			$size = $header['size'];
		
			while ($size != 0){
				$read_size = ($size < 2048 ? $size : 2048);
				$buffer = gzread($gzp, $read_size);
				$binary_data = pack('a'.$read_size, $buffer);
				@fwrite($fp, $binary_data, $read_size);
				$size -= $read_size;
			}
			fclose($fp); gzclose($gzp);
		
			touch($to.$header['filename'], $header['mtime']);
			@unlink($to.$header['filename'].'.gz');
			
		}
	}
	
	$this->total_files ++;
	$this->out_text="File: $to$header[filename]<br>";
	return true;
 }

// end class
}
/**
 * 解压缩一个文件到一个文件夹下
 *
 * @param string $todir     目标释放目录
 * @param string $tmp_name  临时文件名
 * @param string $new_name  目标文件名
 * @return string  有三种情况 1 表示成功 0 表示失败 -1 表示此文件不是zip文件
 * @example  unzip('abc/','abc.zip','abc.zip'); 
 * 			 使用完毕要使用 clearstatcache(); 来清空cache
 * 			 使用之前请先使用 set_time_limit(0); 来延长超时时间
 */
function unzip($todir,$tmp_name,$new_name)
{
	global $z,$have_zip_file;
	$z = new Zip;
	$upfile = array("tmp_name"=>$tmp_name,"name"=>$new_name);
	if(is_file($upfile[tmp_name])){
		if(preg_match('/\.zip$/mis',$upfile[name])){
			$result=$z->Extract($upfile[tmp_name],$todir);
			if($result==-1){
				return 0;
			}
			return 1;
		}else{
			return -1;
		}
		if(realpath($upfile[name])!=realpath($upfile[tmp_name])){
			@unlink($upfile[name]);
			rename($upfile[tmp_name],$upfile[name]);
		}
	}
	else
	{
		echo '<script language="javascript">alert("文件不存在!");history.back(1);</script>';
	}
}