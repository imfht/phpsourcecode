<%
/* Zip/Unzip, Encrypt/Decrypt & Encode/Decode, administrator
 * v0.1
 * Xenxin@Ufqi.com
 * Sun Jul  1 09:40:17 UTC 2018
 */
%><%@page import="java.math.BigInteger,java.security.MessageDigest,
java.security.NoSuchAlgorithmException,java.io.UnsupportedEncodingException"%><%
%><%!
public static final class Zeea{
	//- variables
	double ver = 0.1;

	//- constructor
	public Zeea(){
		//- @todo

	}

	//- methods, public
	public static String md5(String txt){
		return getMD(txt, "MD5");
	}
	
	//- sha1
	public static String sha1(String txt){
		return getMD(txt, "SHA-1");
	}
	
	//- sha256
	public static String sha256(String txt){
		return getMD(txt, "SHA-256");
	}
	
	//-sha512
	public static String sha512(String txt){
		return getMD(txt, "SHA-512");
	}
	
	//- methods, private
	private static String getMD(String txt, String hashType){
		String mdStr = "";
		try {
			MessageDigest md = MessageDigest.getInstance(hashType);
			byte[] array = md.digest(txt.getBytes("UTF-8"));
			StringBuffer sb = new StringBuffer();

			for(int i = 0; i < array.length; ++i) {
				sb.append(Integer.toHexString(array[i] & 255 | 256).substring(1, 3));
			}

			return mdStr=sb.toString();
		}
		catch (NoSuchAlgorithmException var6){
			return mdStr;
		}
		catch (UnsupportedEncodingException var7){
			return mdStr;
		}
	} 

}
%>
