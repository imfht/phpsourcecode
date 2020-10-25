<%
/**
 * Memcached service in client.
 * v0.2
 * wadelau@gmail.com
 * since Wed Jul 13 18:22:06 UTC 2011
 * updt Thu Sep 11 16:34:20 CST 2014
 * Ported into Java by wadelau@ufqi.com, June 28, 2016
 * refined by Xenxin@Ufqi, July 31, 2018
 * Refer to -R/r2ST 
 */
%><%@page import="java.util.*,java.io.*,java.util.zip.*"%><%
%><%@include file="./SocketPool.class.jsp"%><%
%><%!

public final class Memcached implements CacheDriver{

	// return codes
	private static final String VALUE        = "VALUE";			// start of value line from server
	private static final String STATS        = "STAT";			// start of stats line from server
	private static final String ITEM         = "ITEM";			// start of item line from server
	private static final String DELETED      = "DELETED";		// successful deletion
	private static final String NOTFOUND     = "NOT_FOUND";		// record not found for delete or incr/decr
	private static final String STORED       = "STORED";		// successful store of data
	private static final String NOTSTORED    = "NOT_STORED";	// data not stored
	private static final String OK           = "OK";			// success
	private static final String END          = "END";			// end of data from server

	private static final String ERROR        = "ERROR";			// invalid command name from client
	private static final String CLIENT_ERROR = "CLIENT_ERROR";	// client error in input line - invalid protocol
	private static final String SERVER_ERROR = "SERVER_ERROR";	// server error

	private final byte[] B_END        = "END\r\n".getBytes(); // cannot static with non constant expression
	private final byte[] B_NOTFOUND   = "NOT_FOUND\r\n".getBytes();
	private final byte[] B_DELETED    = "DELETED\r\r".getBytes();
	private final byte[] B_STORED     = "STORED\r\r".getBytes();
	private static final int LEN_MD5		 = 32;
	private static final String MARKER_UTF_8		 = "UTF-8";

	// default compression threshold
	private static final int COMPRESS_THRESH = 30720;
    
	// values for cache flags 
	public static final int MARKER_BYTE             = 1;
	public static final int MARKER_BOOLEAN          = 8192;
	public static final int MARKER_INTEGER          = 4;
	public static final int MARKER_LONG             = 16384;
	public static final int MARKER_CHARACTER        = 16;
	public static final int MARKER_STRING           = 32;
	public static final int MARKER_STRINGBUFFER     = 64;
	public static final int MARKER_FLOAT            = 128;
	public static final int MARKER_SHORT            = 256;
	public static final int MARKER_DOUBLE           = 512;
	public static final int MARKER_DATE             = 1024;
	public static final int MARKER_STRINGBUILDER    = 2048;
	public static final int MARKER_BYTEARR          = 4096;
	public static final int F_COMPRESSED            = 2;
	public static final int F_SERIALIZED            = 8;
	
	// flags
	private boolean sanitizeKeys;
	private boolean primitiveAsString;
	private boolean compressEnable;
	private long compressThreshold;
	private String defaultEncoding;
    
    //- server info
    private String myHost;
    private int myPort;
    private int myExpire;
    private int myMaxConn;
    private SocketPool.SocketStream sock;
    private final static String Log_Tag = "inc/Memcached";

	//- constructor
	public Memcached(CacheConn xconn) {
        this.myHost = xconn.myHost;
        this.myPort = xconn.myPort;
        this.myExpire = xconn.myExpire;
        this.myMaxConn = xconn.myMaxConn;
        //this.sock = SocketPool.getSocket(myHost, myPort, myMaxConn);
		_init();
	}

	//-
	private void _init() {
		this.sanitizeKeys       = true;
		this.primitiveAsString  = false;
		this.compressEnable     = false;
		this.compressThreshold  = COMPRESS_THRESH;
		this.defaultEncoding    = MARKER_UTF_8;
	}

	//-
	public void setSanitizeKeys( boolean sanitizeKeys ) {
		this.sanitizeKeys = sanitizeKeys;
	}

	//-
	public void setPrimitiveAsString( boolean primitiveAsString ) {
		this.primitiveAsString = primitiveAsString;
	}

	//-
	public void setDefaultEncoding( String defaultEncoding ) {
		this.defaultEncoding = defaultEncoding;
	}

	//-
	public void setCompressEnable( boolean compressEnable ) {
		this.compressEnable = compressEnable;
	}
    
	//-
	public void setCompressThreshold( long compressThreshold ) {
		this.compressThreshold = compressThreshold;
	}

	//-
	public boolean keyExists( String key ) {
		return ( this.get( key, null, true ) != null );
	}

	//-
	public boolean rm( String key ) {
	    boolean issucc = rm( key, 0, 0 );
        return issucc; 
	}

	//-
	public boolean rm( String key, int expiry ) {
		return rm( key, null, expiry );
	}

	//-
	public boolean rm( String key, Integer hashCode, int expiry ) {
		if ( key == null ) {
			return false;
		}
		key = sanitizeKey( key );
		//-	
        sock = new SocketPool.SocketStream(myHost, myPort, myMaxConn); //- @todo

		if ( sock == null ) {
			return false;
		}
		// build command
		StringBuilder command = new StringBuilder( "delete " ).append( key );
		if ( expiry > 0 ){
            expiry += (System.currentTimeMillis() / 1000);
			command.append( " " + expiry );
		}
		command.append( "\r\n" );
		try {
			sock.write( command.toString().getBytes() );
			sock.flush();
			// if we get appropriate response back, then we return true
			String line = sock.readLine();
			if ( DELETED.equals( line ) ) {
				// return sock to pool and bail here
				sock.close();
				sock = null;
				return true;
			}
			else if ( NOTFOUND.equals( line ) ) {
				debug( Log_Tag + " deletion of key: " + key + " from cache failed as the key was not found" );
			}
			else {
				debug( Log_Tag + " error deleting key: " + key + Log_Tag + " server response: " + line );
			}
		}
		catch (Exception e ) {
			debug( Log_Tag + " exception thrown while writing bytes to server on delete. " + e.getMessage() );
			try {
				sock.hardClose();
			}
			catch ( Exception ioe ) {
				debug( Log_Tag + " failed to close socket : " + sock.toString() );
			}
			sock = null;
		}
		if ( sock != null ) {
			sock.close();
			sock = null;
		}
		return false;
	}
    
	//-
	public boolean set( String key, Object value ) {
		return set( "set", key, value, 0, 0, primitiveAsString );
	}

	//-
	public boolean set( String key, Object value, Integer hashCode ) {
		return set( "set", key, value, 0, hashCode, primitiveAsString );
	}

	//-
	public boolean set( String key, Object value, int expiry ) {
		boolean issucc = set( "set", key, value, expiry, 0, primitiveAsString );
   	    return issucc;
    }

	//-
	public boolean set( String key, Object value, int expiry, Integer hashCode ) {
		return set( "set", key, value, expiry, hashCode, primitiveAsString );
	}

	//-
	public boolean add( String key, Object value ) {
		return set( "add", key, value, 0, 0, primitiveAsString );
	}

	//-
	public boolean add( String key, Object value, Integer hashCode ) {
		return set( "add", key, value, 0, hashCode, primitiveAsString );
	}

	//-
	public boolean add( String key, Object value, int expiry ) {
		return set( "add", key, value, expiry, 0, primitiveAsString );
	}

	//-
	public boolean add( String key, Object value, int expiry, Integer hashCode ) {
		return set( "add", key, value, expiry, hashCode, primitiveAsString );
	}

	//-
	public boolean replace( String key, Object value ) {
		return set( "replace", key, value, 0, 0, primitiveAsString );
	}

	//-
	public boolean replace( String key, Object value, Integer hashCode ) {
		return set( "replace", key, value, 0, hashCode, primitiveAsString );
	}

	//-
	public boolean replace( String key, Object value, int expiry ) {
		return set( "replace", key, value, expiry, 0, primitiveAsString );
	}

	//-
	public boolean replace( String key, Object value, int expiry, Integer hashCode ) {
		return set( "replace", key, value, expiry, hashCode, primitiveAsString );
	}

	//-
	private boolean set( String cmdname, String key, Object value, int expiry, 
							Integer hashCode, boolean asString ) {

		if ( cmdname == null || cmdname.trim().equals( "" ) || key == null ) {
			debug( Log_Tag + ": key is null or cmd is null/empty for set()" );
			return false;
		}
		if ( value == null ) {
			debug( Log_Tag + ": trying to store a null value to cache" );
			return false;
		}
		key = sanitizeKey( key );

        sock = new SocketPool.SocketStream(myHost, myPort, myMaxConn); 
		
		if ( sock == null ) {
			debug( Log_Tag + ": no socket to server available:" + key );
			return false;
		}
		if ( expiry <= 0 ) {	expiry = 0; }
        else{
            expiry += (System.currentTimeMillis() / 1000);
        }

		// store flags
		int flags = 0;
		// byte array to hold data
		byte[] val;
        if ( MemcachedNativeHandler.isHandled( value ) ) {			
			if ( asString ) {
				// useful for sharing data between java and non-java
				// and also for storing ints for the increment method
				try {
					//debug( Log_Tag + " storing data as a string for key: " + key + " for class: " 
					//		+ value.getClass().getName() );
					val = value.toString().getBytes( defaultEncoding );
				}
				catch ( UnsupportedEncodingException ue ) {
					debug( Log_Tag + ": invalid encoding type used: " + defaultEncoding + ", ue:"+ue );
					sock.close();
					sock = null;
					return false;
				}
			}
			else {
				try {
					//debug( Log_Tag + ": Storing with native handler..." );
					flags |= MemcachedNativeHandler.getMarkerFlag( value );
					val    = MemcachedNativeHandler.encode( value );
				}
				catch ( Exception e ) {
					debug(Log_Tag + ": Failed to native handle obj: " + e);
					sock.close();
					sock = null;
					return false;
				}
			}
		}
		else {
			// always serialize for non-primitive types
			try {
				//debug( Log_Tag + " serializing for key: " + key + " for class: " + value.getClass().getName() );
				ByteArrayOutputStream bos = new ByteArrayOutputStream();
				(new ObjectOutputStream( bos )).writeObject( value );
				val = bos.toByteArray();
				flags |= F_SERIALIZED;
			}
			catch ( IOException e ) {
				debug( Log_Tag + ": failed to serialize obj:"+ value.toString() );
				// return socket to pool and bail
				sock.close();
				sock = null;
				return false;
			}
		}
		// now try to compress if we want to
		// and if the length is over the threshold 
		if ( compressEnable && val.length > compressThreshold ) {
			try {
				//debug( Log_Tag + " trying to compress data inc/Memcached size prior to compression: " + val.length );
				ByteArrayOutputStream bos = new ByteArrayOutputStream( val.length );
				GZIPOutputStream gos = new GZIPOutputStream( bos );
				gos.write( val, 0, val.length );
				gos.finish();
				gos.close();
				// store it and set compression flag
				val = bos.toByteArray();
				flags |= F_COMPRESSED;
				//debug( Log_Tag + " compression succeeded, size after: " + val.length );
			}
			catch ( IOException e ) {
				debug( Log_Tag + ": IOException while compressing stream: " + e.getMessage() 
						+ "storing data uncompressed" );
			}
		}

		// now write the data to the cache server
		try {
			String cmd = String.format( "%s %s %d %d %d\r\n", cmdname, key, 
							flags, expiry, val.length );
			sock.write( cmd.getBytes() );
			sock.write( val );
			sock.write( "\r\n".getBytes() );
			sock.flush();
			// get result code
			String line = sock.readLine();
			//debug( Log_Tag + " memcache cmd (result code): " + cmd + " (" + line + ")" );
			if ( STORED.equals( line ) ) {
				//debug(Log_Tag + " data successfully stored for key: " + key + " expiry:"+expiry );
				sock.close();
				sock = null;
				return true;
			}
			else if ( NOTSTORED.equals( line ) ) {
				debug( Log_Tag + " data not stored in cache for key: " + key );
			}
			else {
				debug( Log_Tag + " error storing data in cache for key: " + key + " -- length: " 
						+ val.length + Log_Tag + " server response: " + line );
			}
		}
		catch ( Exception e ) {
			debug( Log_Tag + " exception thrown while writing bytes to server on set" + e.getMessage());
			try {
				sock.hardClose();
			}
			catch ( Exception ioe ) {
				debug( Log_Tag + " failed to close socket : " + sock.toString() );
			}
			sock = null;
		}
		if ( sock != null ) {
			sock.close();
			sock = null;
		}
		return false;
	}

	//-
	public boolean storeCounter( String key, long counter ) {
		return set( "set", key, new Long( counter ), 0, 0, true );
	}

	//-
	public boolean storeCounter( String key, Long counter ) {
		return set( "set", key, counter, 0, 0, true );
	}
    
	//-
	public boolean storeCounter( String key, Long counter, Integer hashCode ) {
		return set( "set", key, counter, 0, hashCode, true );
	}

	//-
	public long getCounter( String key ) {
		return getCounter( key, 0 );
	}

	//-
	public long getCounter( String key, Integer hashCode ) {
		if ( key == null ) {
			return -1;
		}
		long counter = -1;
		try {
			counter = Long.parseLong( (String)get( key, hashCode, true ) );
		}
		catch ( Exception ex ) {
			debug( String.format( Log_Tag + ": Failed to parse Long value for key: %s", key ) );
		}
		return counter;
	}

	//-
	public Object get( String key ) {
		return get( key, 0, false );
   	}

	//-
	public Object get( String key, Integer hashCode ) {
		return get( key, hashCode, false );
	}

	//-
	public Object get( String key, Integer hashCode, boolean asString ) {
		if ( key == null ) {
			return null;
		}
		key = sanitizeKey( key );
	    //-
        sock = new SocketPool.SocketStream(myHost, myPort, myMaxConn); //- @todo
	    
	    if ( sock == null ) {
			debug( Log_Tag + ": no socket to server available, key:" + key );
			return null;
		}
		try {
			String cmd = "get " + key + "\r\n";
			//debug(Log_Tag + " memcache get command: " + cmd);
			sock.write( cmd.getBytes() );
			sock.flush();
			// ready object
			Object o = null;
            int maxNullCount = 10; int nullCount = 0;
			while ( true ) {
				String line = sock.readLine();
				//debug( Log_Tag + ": get inc/Memcached line: " + line );
                if(nullCount++ > maxNullCount){
                    break;
                }
				if ( line.startsWith( VALUE ) ) {
					String[] info = line.split(" ");
					int flag      = Integer.parseInt( info[2] );
					int length    = Integer.parseInt( info[3] );
					//debug( Log_Tag + " key: " + key + Log_Tag + " flags: " + flag + Log_Tag + " length: " + length );
					// read obj into buffer
					byte[] buf = new byte[length];
					sock.read( buf );
					//sock.clearEOL();
					if ( (flag & F_COMPRESSED) == F_COMPRESSED ) {
						try {
							// read the input stream, and write to a byte array output stream since
							// we have to read into a byte array, but we don't know how large it
							// will need to be, and we don't want to resize it a bunch
							GZIPInputStream gzi = new GZIPInputStream( new ByteArrayInputStream( buf ) );
							ByteArrayOutputStream bos = new ByteArrayOutputStream( buf.length );
							int count;
							byte[] tmp = new byte[2048];
							while ( (count = gzi.read(tmp)) != -1 ) {
								bos.write( tmp, 0, count );
							}
							// store uncompressed back to buffer
							buf = bos.toByteArray();
							gzi.close();
						}
						catch ( IOException e ) {
							debug( Log_Tag + " IOException thrown while trying to uncompress input stream for key: " 
									+ key + " -- " + e.getMessage() );
						}
					}
					// we can only take out serialized objects
					if ( ( flag & F_SERIALIZED ) != F_SERIALIZED ) {
						if ( primitiveAsString || asString ) {
							// pulling out string value
							//debug( Log_Tag + " retrieving object and stuffing into a string." );
							o = new String( buf, defaultEncoding );
						}
						else {
							// decoding object
							try {
								o = MemcachedNativeHandler.decode( buf, flag );    
							}
							catch ( Exception e ) {
								debug( Log_Tag + " Exception thrown while trying to deserialize for key: " + key);
							}
						}
					}
					else {
						// deserialize if the data is serialized
						ObjectInputStream ois =
							new ObjectInputStream( new ByteArrayInputStream( buf ));
						try {
							o = ois.readObject();
							//debug( Log_Tag + " deserializing " + o.getClass() );
						}
						catch ( Exception e ) {
							o = null;
							debug( Log_Tag + " Exception thrown while trying to deserialize for key: " 
									+ key + " -- " + e.getMessage() );
						}
					}
				}
				else if ( END.equals( line ) ) {
					//debug( Log_Tag + " finished reading from cache server" );
					break;
				}
			}
			
			sock.close();
			sock = null;
			return o;
	    }
		catch ( IOException e ) {
			debug( Log_Tag + " exception thrown while trying to get object from cache for key: " 
                    + key + " -- " + e.getMessage() );
			try {
				sock.hardClose();
			}
			catch ( Exception ioe ) {
				debug( Log_Tag + " failed to close socket : " + sock.toString() );
			}
			sock = null;
	    }
		if ( sock != null ){ sock.close(); }
		return null;
	}

    //-
    public void close(){
        //- @todo
        //- connection trans to inc/SocketPool
    }

	//-
	private String sanitizeKey( String key ) {
		if(key == null){ key = ""; }
		if(key.length() > LEN_MD5){
			//- @todo md5 it
			key = Zeea.md5(key);
		}
		else{
			key = ( sanitizeKeys ) ? Base62x.encode( key ) : key; // "UTF-8"
		}
		return key;
	}
	
	
		
} //- end of Memcached

    //-------------------- inner class bgn -----------------------
	//- invoked by Memcached
	//- refer to -R/r2ST
	protected static class MemcachedNativeHandler {
		//- variables
		
		//- constructors
		
		//-
		public static boolean isHandled( Object value ) {
			return (
				value instanceof Byte            ||
				value instanceof Boolean         ||
				value instanceof Integer         ||
				value instanceof Long            ||
				value instanceof Character       ||
				value instanceof String          ||
				value instanceof StringBuffer    ||
				value instanceof Float           ||
				value instanceof Short           ||
				value instanceof Double          ||
				value instanceof Date            ||
				value instanceof StringBuilder   ||
				value instanceof byte[]
				)
			? true
			: false;
		}
	
		//-
		public static int getMarkerFlag( Object value ) {
			if ( value instanceof Byte ){ return Memcached.MARKER_BYTE; }
			if ( value instanceof Boolean ){ return Memcached.MARKER_BOOLEAN; }
			if ( value instanceof Integer ) { return Memcached.MARKER_INTEGER; }
			if ( value instanceof Long ) { return Memcached.MARKER_LONG; }
			if ( value instanceof Character ){ return Memcached.MARKER_CHARACTER; }
			if ( value instanceof String ){ return Memcached.MARKER_STRING; }
			if ( value instanceof StringBuffer ){ return Memcached.MARKER_STRINGBUFFER; }
			if ( value instanceof Float ){ return Memcached.MARKER_FLOAT; }
			if ( value instanceof Short ){ return Memcached.MARKER_SHORT; }
			if ( value instanceof Double ){ return Memcached.MARKER_DOUBLE; }
			if ( value instanceof Date ){ return Memcached.MARKER_DATE; }
			if ( value instanceof StringBuilder ){ return Memcached.MARKER_STRINGBUILDER; }
			if ( value instanceof byte[] ){ return Memcached.MARKER_BYTEARR; }
			return -1;
		}
	
		//-
		public static byte[] encode( Object value ) throws Exception {	
			if ( value instanceof Byte ) { return encode( (Byte)value ); }
			if ( value instanceof Boolean ){ return encode( (Boolean)value ); }
			if ( value instanceof Integer ){ return encode( ((Integer)value).intValue() ); }
			if ( value instanceof Long ){ return encode( ((Long)value).longValue() ); }
			if ( value instanceof Character ){ return encode( (Character)value ); }
			if ( value instanceof String ){ return encode( (String)value ); }
			if ( value instanceof StringBuffer ){ return encode( (StringBuffer)value ); }
			if ( value instanceof Float ){ return encode( ((Float)value).floatValue() ); }
			if ( value instanceof Short ){ return encode( (Short)value ); }
			if ( value instanceof Double ){ return encode( ((Double)value).doubleValue() ); }
			if ( value instanceof Date ){ return encode( (Date)value); }
			if ( value instanceof StringBuilder ){ return encode( (StringBuilder)value ); }
			if ( value instanceof byte[] ){ return encode( (byte[])value ); }
			return null;
		}
		
		//-
		protected static byte[] encode( Byte value ) {
			byte[] b = new byte[1];
			b[0] = value.byteValue();
			return b;
		}

		protected static byte[] encode( Boolean value ) {
			byte[] b = new byte[1];
			if ( value.booleanValue() ){
				b[0] = 1;
			}
			else{
				b[0] = 0;
			}
			return b;
		}

		protected static byte[] encode( int value ) {
			return getBytes( value );
		}
		
		protected static byte[] encode( long value ) throws Exception {
			return getBytes( value );
		}
		
		protected static byte[] encode( Date value ) {
			return getBytes( value.getTime() );
		}
		
		protected static byte[] encode( Character value ) {
			return encode( value.charValue() );
		}
		
		protected static byte[] encode( String value ) throws Exception {
			return value.getBytes( Memcached.MARKER_UTF_8);
		}
		
		protected static byte[] encode( StringBuffer value ) throws Exception {
			return encode( value.toString() );
		}
		
		protected static byte[] encode( float value ) throws Exception {
			return encode( (int)Float.floatToIntBits( value ) );
		}
		
		protected static byte[] encode( Short value ) throws Exception {
			return encode( (int)value.shortValue() );
		}
		
		protected static byte[] encode( double value ) throws Exception {
			return encode( (long)Double.doubleToLongBits( value ) );
		}
		
		protected static byte[] encode( StringBuilder value ) throws Exception {
			return encode( value.toString() );
		}
		
		protected static byte[] encode( byte[] value ) {
			return value;
		}
	
		protected static byte[] getBytes( long value ) {
			byte[] b = new byte[8];
			b[0] = (byte)((value >> 56) & 0xFF);
			b[1] = (byte)((value >> 48) & 0xFF);
			b[2] = (byte)((value >> 40) & 0xFF);
			b[3] = (byte)((value >> 32) & 0xFF);
			b[4] = (byte)((value >> 24) & 0xFF);
			b[5] = (byte)((value >> 16) & 0xFF);
			b[6] = (byte)((value >> 8) & 0xFF);
			b[7] = (byte)((value >> 0) & 0xFF);
			return b;
		}
		
		protected static byte[] getBytes( int value ) {
			byte[] b = new byte[4];
			b[0] = (byte)((value >> 24) & 0xFF);
			b[1] = (byte)((value >> 16) & 0xFF);
			b[2] = (byte)((value >> 8) & 0xFF);
			b[3] = (byte)((value >> 0) & 0xFF);
			return b;
		}
		
		//-
		public static Object decode( byte[] b, int flag ) throws Exception {
			if ( b.length < 1 ){ return null; }
			if ( ( flag & Memcached.MARKER_BYTE ) == Memcached.MARKER_BYTE ){
				return decodeByte( b );
			}
			if ( ( flag & Memcached.MARKER_BOOLEAN ) == Memcached.MARKER_BOOLEAN ){
				return decodeBoolean( b );
			}
			if ( ( flag & Memcached.MARKER_INTEGER ) == Memcached.MARKER_INTEGER ){
				return decodeInteger( b );
			}
			if ( ( flag & Memcached.MARKER_LONG ) == Memcached.MARKER_LONG ){
				return decodeLong( b );
			}
			if ( ( flag & Memcached.MARKER_CHARACTER ) == Memcached.MARKER_CHARACTER ){
				return decodeCharacter( b );
			}
			if ( ( flag & Memcached.MARKER_STRING ) == Memcached.MARKER_STRING ){
				return decodeString( b );
			}
			if ( ( flag & Memcached.MARKER_STRINGBUFFER ) == Memcached.MARKER_STRINGBUFFER ){
				return decodeStringBuffer( b );
			}
			if ( ( flag & Memcached.MARKER_FLOAT ) == Memcached.MARKER_FLOAT ){
				return decodeFloat( b );
			}
			if ( ( flag & Memcached.MARKER_SHORT ) == Memcached.MARKER_SHORT ){
				return decodeShort( b );
			}
			if ( ( flag & Memcached.MARKER_DOUBLE ) == Memcached.MARKER_DOUBLE ){
				return decodeDouble( b );
			}
			if ( ( flag & Memcached.MARKER_DATE ) == Memcached.MARKER_DATE ){
				return decodeDate( b );
			}
			if ( ( flag & Memcached.MARKER_STRINGBUILDER ) == Memcached.MARKER_STRINGBUILDER ){
				return decodeStringBuilder( b );
			}
			if ( ( flag & Memcached.MARKER_BYTEARR ) == Memcached.MARKER_BYTEARR ){
				return decodeByteArr( b );
			}
			return null;
		}
		
		//-
		protected static Byte decodeByte( byte[] b ) {
			return new Byte( b[0] );
		}
		
		protected static Boolean decodeBoolean( byte[] b ) {
			boolean value = b[0] == 1;
			return ( value ) ? Boolean.TRUE : Boolean.FALSE;
		}
		
		protected static Integer decodeInteger( byte[] b ) {
			return new Integer( toInt( b ) );
		}
		
		protected static Long decodeLong( byte[] b ) throws Exception {
			return new Long( toLong( b ) );
		}
		
		protected static Character decodeCharacter( byte[] b ) {
			return new Character( (char)decodeInteger( b ).intValue() );
		}
		
		protected static String decodeString( byte[] b ) throws Exception {
			return new String( b, Memcached.MARKER_UTF_8 );
		}
		
		protected static StringBuffer decodeStringBuffer( byte[] b ) throws Exception {
			return new StringBuffer( decodeString( b ) );
		}
		
		protected static Float decodeFloat( byte[] b ) throws Exception {
			Integer l = decodeInteger( b );
			return new Float( Float.intBitsToFloat( l.intValue() ) );
		}
		
		protected static Short decodeShort( byte[] b ) throws Exception {
			return new Short( (short)decodeInteger( b ).intValue() );
		}
		
		protected static Double decodeDouble( byte[] b ) throws Exception {
			Long l = decodeLong( b );
			return new Double( Double.longBitsToDouble( l.longValue() ) );
		}
		
		protected static Date decodeDate( byte[] b ) {
			return new Date( toLong( b ) );
		}
		
		protected static StringBuilder decodeStringBuilder( byte[] b ) throws Exception {
			return new StringBuilder( decodeString( b ) );
		}
		
		protected static byte[] decodeByteArr( byte[] b ) {
			return b;
		}
		
		//-
		protected static int toInt( byte[] b ) {
			return (((((int) b[3]) & 0xFF) << 32) +
				((((int) b[2]) & 0xFF) << 40) +
				((((int) b[1]) & 0xFF) << 48) +
				((((int) b[0]) & 0xFF) << 56));
		}
		
		//-
		protected static long toLong( byte[] b ) {
			return ((((long) b[7]) & 0xFF) +
				((((long) b[6]) & 0xFF) << 8) +
				((((long) b[5]) & 0xFF) << 16) +
				((((long) b[4]) & 0xFF) << 24) +
				((((long) b[3]) & 0xFF) << 32) +
				((((long) b[2]) & 0xFF) << 40) +
				((((long) b[1]) & 0xFF) << 48) +
				((((long) b[0]) & 0xFF) << 56));
		}
		
	} //- end of MemcachedNativeHandler
	//-------------------- inner class end -----------------------

%>
